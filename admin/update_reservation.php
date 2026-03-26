<?php
// Mise à jour du statut d'une réservation + envoi email
// Événement & Co

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../includes/db.php";
require_once "../includes/email_config.php";
require_once "includes/csrf_helper.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Vérification CSRF
    proteger_csrf($_POST['csrf_token'] ?? '');

    $reservation_id = (int) $_POST['reservation_id'];
    $nouveau_statut = htmlspecialchars(trim($_POST['nouveau_statut']));

    $errors = [];

    if ($reservation_id <= 0) {
        $errors[] = "ID de réservation invalide.";
    }

    $statuts_valides = ['confirme', 'annule'];
    if (!in_array($nouveau_statut, $statuts_valides)) {
        $errors[] = "Statut invalide.";
    }

    if (empty($errors)) {
        try {
            // Récupérer la réservation avant modification
            $sql_select = "SELECT r.*, e.nom as nom_evenement
                           FROM reservations r
                           INNER JOIN evenements e ON r.evenement_id = e.id
                           WHERE r.id = :id";

            $stmt_select = $pdo->prepare($sql_select);
            $stmt_select->execute([':id' => $reservation_id]);
            $reservation = $stmt_select->fetch(PDO::FETCH_ASSOC);

            if (!$reservation) {
                header('Location: admin_reservations.php?error=notfound');
                exit();
            }

            // Mettre à jour le statut
            $sql_update = "UPDATE reservations SET statut = :statut WHERE id = :id";
            $stmt = $pdo->prepare($sql_update);
            $stmt->execute([
                ':statut' => $nouveau_statut,
                ':id' => $reservation_id
            ]);

            if ($stmt->rowCount() > 0) {

                // Envoyer un email au client
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = SMTP_PORT;
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                    $mail->addAddress($reservation['email_client'], $reservation['nom_client']);

                    $date = new DateTime($reservation['date_evenement']);
                    $date_formatee = $date->format('d/m/Y');

                    if ($nouveau_statut == 'confirme') {
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

Pour toute question, n'hésitez pas à nous contacter :
   Email : contact@evenements-co.fr
   Téléphone : 04 55 66 77 88

Cordialement,
L'équipe Événement & Co
";

                    } else {
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

Pour toute question, n'hésitez pas à nous contacter :
   Email : contact@evenements-co.fr
   Téléphone : 04 55 66 77 88

Cordialement,
L'équipe Événement & Co
";
                    }

                    $mail->send();

                } catch (Exception $e) {
                    error_log("Erreur envoi email : " . $e->getMessage());
                }

                header('Location: admin_reservations.php?success=updated');
                exit();
            } else {
                header('Location: admin_reservations.php?error=notfound');
                exit();
            }

        } catch (PDOException $e) {
            header('Location: admin_reservations.php?error=db');
            exit();
        }

    } else {
        header('Location: admin_reservations.php?error=validation');
        exit();
    }

} else {
    header('Location: admin_reservations.php');
    exit();
}
?>