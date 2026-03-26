<?php
// Traitement des réservations d'événements
// Événement & Co

require_once "includes/db.php";
require_once "includes/email_config.php";
require_once "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Récupérer et nettoyer les données
    $nom_client = htmlspecialchars(trim($_POST["nom_client"]));
    $email_client = htmlspecialchars(trim($_POST["email_client"]));
    $evenement_id = (int) $_POST["evenement_id"];
    $date_evenement = htmlspecialchars(trim($_POST["date_evenement"]));
    $ville = htmlspecialchars(trim($_POST["ville"]));

    $tel_client = isset($_POST["tel_client"]) ? htmlspecialchars(trim($_POST["tel_client"])) : null;
    $nombre_invites = isset($_POST["nombre_invites"]) ? (int) $_POST["nombre_invites"] : 50;
    $message_client = isset($_POST["message_client"]) ? htmlspecialchars(trim($_POST["message_client"])) : null;

    // Validation
    $errors = [];

    if (empty($nom_client)) {
        $errors[] = "Le nom est obligatoire.";
    }

    if (empty($email_client)) {
        $errors[] = "L'email est obligatoire.";
    } elseif (!filter_var($email_client, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }

    if (empty($evenement_id) || !in_array($evenement_id, [1, 2, 3])) {
        $errors[] = "Veuillez sélectionner un type d'événement valide.";
    }

    if (empty($date_evenement)) {
        $errors[] = "La date de l'événement est obligatoire.";
    } else {
        $today = date('Y-m-d');
        if ($date_evenement <= $today) {
            $errors[] = "La date de l'événement doit être dans le futur.";
        }
    }

    $villes_valides = ['Grenoble', 'Lyon', 'Annecy'];
    if (empty($ville)) {
        $errors[] = "Veuillez sélectionner une ville.";
    } elseif (!in_array($ville, $villes_valides)) {
        $errors[] = "La ville sélectionnée n'est pas valide.";
    }

    if ($nombre_invites < 10 || $nombre_invites > 500) {
        $errors[] = "Le nombre d'invités doit être entre 10 et 500.";
    }

    // Vérifier la disponibilité (max 3 par jour par ville)
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
                $errors[] = "Désolé, aucune disponibilité pour cette date dans cette ville.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la vérification de disponibilité.";
        }
    }

    if (empty($errors)) {
        try {
            // Récupérer l'événement
            $sql_event = "SELECT nom, prix_base FROM evenements WHERE id = :evenement_id";
            $stmt_event = $pdo->prepare($sql_event);
            $stmt_event->execute([':evenement_id' => $evenement_id]);
            $evenement = $stmt_event->fetch(PDO::FETCH_ASSOC);

            $prix_total = $evenement['prix_base'];

            // Insérer la réservation
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

            // Envoyer email à l'admin
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress(ADMIN_EMAIL);
            $mail->Subject = 'Nouvelle demande de réservation - ' . $evenement['nom'];
            $mail->Body = "Bonjour,\n\n" .
                          "Une nouvelle demande de réservation a été reçue :\n\n" .
                          "Type d'événement : " . $evenement['nom'] . "\n" .
                          "Nom du client : $nom_client\n" .
                          "Email : $email_client\n" .
                          "Téléphone : " . ($tel_client ?: 'Non fourni') . "\n" .
                          "Date souhaitée : $date_evenement\n" .
                          "Ville : $ville\n" .
                          "Nombre d'invités : $nombre_invites\n" .
                          "Prix total : " . number_format($prix_total, 2, ',', ' ') . " €\n" .
                          "Message : " . ($message_client ?: 'Aucun message') . "\n\n" .
                          "Cordialement,\n" .
                          "Événement & Co";
            $mail->send();

            // Envoyer confirmation au client
            $mail->clearAddresses();
            $mail->addAddress($email_client, $nom_client);
            $mail->Subject = 'Confirmation de votre demande de réservation - ' . $evenement['nom'];
            $mail->Body = "Bonjour $nom_client,\n\n" .
                          "Nous avons bien reçu votre demande de réservation :\n\n" .
                          "Type d'événement : " . $evenement['nom'] . "\n" .
                          "Date souhaitée : $date_evenement\n" .
                          "Ville : $ville\n" .
                          "Nombre d'invités : $nombre_invites\n" .
                          "Prix estimé : " . number_format($prix_total, 2, ',', ' ') . " €\n\n" .
                          "Statut : En attente de validation\n\n" .
                          "Notre équipe vous contactera prochainement.\n\n" .
                          "Cordialement,\n" .
                          "L'équipe Événement & Co\n" .
                          "12 Rue des Fêtes, 38000 Grenoble";
            $mail->send();

            header('Location: programme.html?success=1');
            exit();

        } catch (PDOException $e) {
            header('Location: programme.html?error=db');
            exit();
        } catch (Exception $e) {
            error_log("Erreur envoi email : " . $e->getMessage());
            header('Location: programme.html?error=email');
            exit();
        }
    } else {
        $errorMessages = implode('|', $errors);
        header('Location: programme.html?error=validation&messages=' . urlencode($errorMessages));
        exit();
    }

}
?>