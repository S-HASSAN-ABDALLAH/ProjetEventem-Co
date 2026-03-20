<?php
// ========================================
// FICHIER : update_reservation.php
// RÔLE : Modifier le statut d'une réservation (CRUD - UPDATE)
// ========================================

// Charger PHPMailer (doit être au début du fichier)
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Charger la connexion à la base de données
require_once "../includes/db.php";

// Charger la configuration email SMTP
require_once "../includes/email_config.php";

// ========================================
// PROTECTION CSRF : Charger les fonctions
// ========================================
require_once "includes/csrf_helper.php";

// Vérifier que le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // ========================================
    // ÉTAPE 0 : VÉRIFICATION DU TOKEN CSRF
    // ========================================
    // IMPORTANT : Cette vérification DOIT être faite AVANT tout traitement
    // Elle protège contre les attaques CSRF (Cross-Site Request Forgery)
    proteger_csrf($_POST['csrf_token'] ?? '');
    // Si le token est invalide, le script s'arrête ici avec une erreur 403
    // Si le token est valide, le script continue normalement ✅

    // ========================================
    // ÉTAPE 1 : Récupérer les données du formulaire
    // ========================================

    $reservation_id = (int) $_POST['reservation_id']; // ID de la réservation à modifier
    $nouveau_statut = htmlspecialchars(trim($_POST['nouveau_statut'])); // Nouveau statut

    // ========================================
    // ÉTAPE 2 : Valider les données
    // ========================================

    $errors = [];

    // Validation 1 : L'ID doit être un nombre positif
    if ($reservation_id <= 0) {
        $errors[] = "ID de réservation invalide.";
    }

    // Validation 2 : Le statut doit être valide (confirme ou annule uniquement)
    $statuts_valides = ['confirme', 'annule'];
    if (!in_array($nouveau_statut, $statuts_valides)) {
        $errors[] = "Statut invalide.";
    }

    // ========================================
    // ÉTAPE 3 : Si validation OK, mettre à jour la réservation
    // ========================================

    if (empty($errors)) {
        try {
            // ========================================
            // ÉTAPE 3A : Récupérer les informations de la réservation AVANT modification
            // ========================================
            $sql_select = "SELECT r.*, e.nom as nom_evenement
                           FROM reservations r
                           INNER JOIN evenements e ON r.evenement_id = e.id
                           WHERE r.id = :id";

            $stmt_select = $pdo->prepare($sql_select);
            $stmt_select->execute([':id' => $reservation_id]);
            $reservation = $stmt_select->fetch(PDO::FETCH_ASSOC);

            // Si la réservation n'existe pas
            if (!$reservation) {
                header('Location: admin_reservations.php?error=notfound');
                exit();
            }

            // ========================================
            // ÉTAPE 3B : Mettre à jour le statut
            // ========================================
            $sql_update = "UPDATE reservations
                           SET statut = :statut
                           WHERE id = :id";

            $stmt = $pdo->prepare($sql_update);
            $stmt->execute([
                ':statut' => $nouveau_statut,
                ':id' => $reservation_id
            ]);

            // Vérifier si une ligne a été modifiée
            if ($stmt->rowCount() > 0) {

                // ========================================
                // ÉTAPE 3C : Envoyer un email au client
                // ========================================

                $mail = new PHPMailer(true);

                try {
                    // Configuration SMTP (utilise les constantes de email_config.php)
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = SMTP_PORT;
                    $mail->CharSet = 'UTF-8';

                    // Activer le mode debug en développement (à désactiver en production)
                    // $mail->SMTPDebug = 2; // Décommenter pour voir les détails SMTP

                    // Expéditeur
                    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

                    // Destinataire : le client
                    $mail->addAddress($reservation['email_client'], $reservation['nom_client']);

                    // Formater la date
                    $date = new DateTime($reservation['date_evenement']);
                    $date_formatee = $date->format('d/m/Y');

                    // ========================================
                    // Contenu selon le statut
                    // ========================================

                    if ($nouveau_statut == 'confirme') {
                        // EMAIL DE CONFIRMATION
                        $mail->Subject = 'Votre réservation est confirmée';

                        $mail->Body = "
Bonjour {$reservation['nom_client']},

Bonne nouvelle !

Votre réservation pour l'événement \"{$reservation['nom_evenement']}\" a été CONFIRMÉE par notre équipe.

RÉCAPITULATIF DE VOTRE RÉSERVATION :
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Événement       : {$reservation['nom_evenement']}
Date            : {$date_formatee}
Ville           : {$reservation['ville']}
Nombre d'invités: {$reservation['nombre_invites']} personnes
Prix total      : {$reservation['prix_total']} €

Statut : CONFIRMÉE

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Nous sommes ravis de vous compter parmi nous et mettons tout en œuvre pour faire de votre événement un moment inoubliable !

Pour toute question, n'hésitez pas à nous contacter :
   Email : contact@evenements-co.fr
   Téléphone : 04 55 66 77 88

Nous vous attendons avec impatience !

Cordialement,
L'équipe Événement & Co

---
Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.
";

                    } else {
                        // EMAIL D'ANNULATION
                        $mail->Subject = 'Votre réservation a été annulée';

                        $mail->Body = "
Bonjour {$reservation['nom_client']},

Nous sommes désolés de vous informer que votre réservation pour l'événement \"{$reservation['nom_evenement']}\" a été annulée.

DÉTAILS DE LA RÉSERVATION ANNULÉE :
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Événement       : {$reservation['nom_evenement']}
Date            : {$date_formatee}
Ville           : {$reservation['ville']}
Nombre d'invités: {$reservation['nombre_invites']} personnes

Statut : ANNULÉE

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Si vous souhaitez effectuer une nouvelle réservation ou obtenir plus d'informations sur les raisons de cette annulation, n'hésitez pas à nous contacter.

Contact :
   Email : contact@evenements-co.fr
   Téléphone : 04 55 66 77 88

Nous espérons avoir le plaisir de vous accueillir prochainement pour un autre événement.

Cordialement,
L'équipe Événement & Co

---
Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.
";
                    }

                    // Envoyer l'email
                    $mail->send();

                } catch (Exception $e) {
                    // Si l'email échoue, on continue quand même (la mise à jour a réussi)
                    // Logger l'erreur pour diagnostic
                    error_log("ERREUR ENVOI EMAIL CLIENT: " . $e->getMessage());
                    // La mise à jour de la réservation a réussi, on continue
                }

                // Succès : rediriger avec message de succès
                header('Location: admin_reservations.php?success=updated');
                exit();
            } else {
                // Aucune modification (peut-être que l'ID n'existe pas)
                header('Location: admin_reservations.php?error=notfound');
                exit();
            }

        } catch (PDOException $e) {
            // Erreur de base de données
            header('Location: admin_reservations.php?error=db');
            exit();
        }

    } else {
        // Erreurs de validation
        header('Location: admin_reservations.php?error=validation');
        exit();
    }

} else {
    // Si la page est accédée directement (pas via POST), rediriger
    header('Location: admin_reservations.php');
    exit();
}
?>
