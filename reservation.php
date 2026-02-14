<?php
// ========================================
// FICHIER : reservation.php
// RÔLE : Traiter les réservations d'événements
// PROJET : Événement & Co - DWWM Bac+2
// ========================================

// Charger la connexion à la base de données
require_once "db.php";

// Charger la configuration email SMTP
require_once "email_config.php";

// Charger PHPMailer (installé via Composer)
require_once "vendor/autoload.php";

// Importer les classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Vérifier que le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // ========================================
    // ÉTAPE 1 : Récupérer et nettoyer les données
    // ========================================

    // Récupérer et nettoyer les données obligatoires
    $nom_client = htmlspecialchars(trim($_POST["nom_client"]));
    $email_client = htmlspecialchars(trim($_POST["email_client"]));
    $evenement_id = (int) $_POST["evenement_id"]; // Convertir en entier
    $date_evenement = htmlspecialchars(trim($_POST["date_evenement"]));
    $ville = htmlspecialchars(trim($_POST["ville"]));

    // Récupérer les données optionnelles
    $tel_client = isset($_POST["tel_client"]) ? htmlspecialchars(trim($_POST["tel_client"])) : null;
    $nombre_invites = isset($_POST["nombre_invites"]) ? (int) $_POST["nombre_invites"] : 50;
    $message_client = isset($_POST["message_client"]) ? htmlspecialchars(trim($_POST["message_client"])) : null;

    // ========================================
    // ÉTAPE 2 : Valider les données
    // ========================================

    // Initialiser le tableau des erreurs
    $errors = [];

    // Validation 1 : Nom obligatoire
    if (empty($nom_client)) {
        $errors[] = "Le nom est obligatoire.";
    }

    // Validation 2 : Email obligatoire et format valide
    if (empty($email_client)) {
        $errors[] = "L'email est obligatoire.";
    } elseif (!filter_var($email_client, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }

    // Validation 3 : Type d'événement obligatoire et valide (1, 2, ou 3)
    if (empty($evenement_id) || !in_array($evenement_id, [1, 2, 3])) {
        $errors[] = "Veuillez sélectionner un type d'événement valide.";
    }

    // Validation 4 : Date obligatoire
    if (empty($date_evenement)) {
        $errors[] = "La date de l'événement est obligatoire.";
    } else {
        // Validation 4.1 : La date doit être dans le futur
        $today = date('Y-m-d');
        if ($date_evenement <= $today) {
            $errors[] = "La date de l'événement doit être dans le futur.";
        }
    }

    // Validation 5 : Ville obligatoire et valide
    $villes_valides = ['Grenoble', 'Lyon', 'Annecy'];
    if (empty($ville)) {
        $errors[] = "Veuillez sélectionner une ville.";
    } elseif (!in_array($ville, $villes_valides)) {
        $errors[] = "La ville sélectionnée n'est pas valide.";
    }

    // Validation 6 : Nombre d'invités (si fourni)
    if ($nombre_invites < 10 || $nombre_invites > 500) {
        $errors[] = "Le nombre d'invités doit être entre 10 et 500.";
    }

    // Validation 7 : Vérifier la disponibilité (max 3 événements par jour et par ville)
    if (empty($errors)) {
        try {
            $sql_check = "SELECT COUNT(*) as total FROM reservations
                          WHERE date_evenement = :date
                          AND ville = :ville
                          AND statut != 'annule'";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([
                ':date' => $date_evenement,
                ':ville' => $ville
            ]);
            $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] >= 3) {
                $errors[] = "Désolé, aucune disponibilité pour cette date dans cette ville. Veuillez choisir une autre date.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la vérification de disponibilité.";
        }
    }

    // ========================================
    // ÉTAPE 3 : Si validation OK, traiter la réservation
    // ========================================

    if (empty($errors)) {
        try {
            // 5A : Récupérer les informations de l'événement (nom et prix)
            $sql_event = "SELECT nom, prix_base FROM evenements WHERE id = :evenement_id";
            $stmt_event = $pdo->prepare($sql_event);
            $stmt_event->execute([':evenement_id' => $evenement_id]);
            $evenement = $stmt_event->fetch(PDO::FETCH_ASSOC);

            // Calculer le prix total (prix de base de l'événement)
            $prix_total = $evenement['prix_base'];

            // 5B : Insérer la réservation dans la base de données
            $sql_insert = "INSERT INTO reservations
                           (evenement_id, nom_client, email_client, tel_client,
                            date_evenement, ville, nombre_invites, message_client,
                            prix_total, statut)
                           VALUES
                           (:evenement_id, :nom_client, :email_client, :tel_client,
                            :date_evenement, :ville, :nombre_invites, :message_client,
                            :prix_total, 'en_attente')";

            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([
                ':evenement_id' => $evenement_id,
                ':nom_client' => $nom_client,
                ':email_client' => $email_client,
                ':tel_client' => $tel_client,
                ':date_evenement' => $date_evenement,
                ':ville' => $ville,
                ':nombre_invites' => $nombre_invites,
                ':message_client' => $message_client,
                ':prix_total' => $prix_total
            ]);

            // 5C : Envoyer un email de notification à l'administrateur
            $mail = new PHPMailer(true);

            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';

            // Email 1 : Notification pour l'admin
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress(ADMIN_EMAIL);
            $mail->Subject = '🔔 Nouvelle demande de réservation - ' . $evenement['nom'];
            $mail->Body = "Bonjour,\n\n" .
                          "Une nouvelle demande de réservation a été reçue :\n\n" .
                          "📅 Type d'événement : " . $evenement['nom'] . "\n" .
                          "👤 Nom du client : $nom_client\n" .
                          "📧 Email : $email_client\n" .
                          "📞 Téléphone : " . ($tel_client ?: 'Non fourni') . "\n" .
                          "📆 Date souhaitée : $date_evenement\n" .
                          "📍 Ville : $ville\n" .
                          "👥 Nombre d'invités : $nombre_invites\n" .
                          "💰 Prix total : " . number_format($prix_total, 2, ',', ' ') . " €\n" .
                          "💬 Message : " . ($message_client ?: 'Aucun message') . "\n\n" .
                          "Veuillez traiter cette demande depuis votre espace administrateur.\n\n" .
                          "Cordialement,\n" .
                          "Système de réservation - Événement & Co";
            $mail->send();

            // 5D : Envoyer un email de confirmation au client
            $mail->clearAddresses(); // Vider les destinataires précédents
            $mail->addAddress($email_client, $nom_client);
            $mail->Subject = '✅ Confirmation de votre demande de réservation - ' . $evenement['nom'];
            $mail->Body = "Bonjour $nom_client,\n\n" .
                          "Nous avons bien reçu votre demande de réservation pour l'événement suivant :\n\n" .
                          "📅 Type d'événement : " . $evenement['nom'] . "\n" .
                          "📆 Date souhaitée : $date_evenement\n" .
                          "📍 Ville : $ville\n" .
                          "👥 Nombre d'invités : $nombre_invites\n" .
                          "💰 Prix estimé : " . number_format($prix_total, 2, ',', ' ') . " €\n\n" .
                          "Statut actuel : ⏳ En attente de validation\n\n" .
                          "Notre équipe va étudier votre demande et vous contacter très prochainement " .
                          "pour confirmer la disponibilité et finaliser les détails de votre événement.\n\n" .
                          "Si vous avez des questions, n'hésitez pas à nous contacter :\n" .
                          "📧 Email : contact@evenements-co.fr\n" .
                          "📞 Téléphone : 04 55 66 77 88\n\n" .
                          "Merci de votre confiance !\n\n" .
                          "Cordialement,\n" .
                          "L'équipe Événement & Co\n" .
                          "12 Rue des Fêtes, 38000 Grenoble";
            $mail->send();

            // 5E : Redirection avec message de succès (Pattern PRG)
            header('Location: programme.html?success=1');
            exit();

        } catch (PDOException $e) {
            // Erreur de base de données
            header('Location: programme.html?error=db');
            exit();
        } catch (Exception $e) {
            // Erreur d'envoi d'email ou autre
            header('Location: programme.html?error=email');
            exit();
        }
    } else {
        // Erreurs de validation : rediriger avec les messages
        $errorMessages = implode('|', $errors);
        header('Location: programme.html?error=validation&messages=' . urlencode($errorMessages));
        exit();
    }

} // Fin du if ($_SERVER['REQUEST_METHOD'] == "POST")
?>
