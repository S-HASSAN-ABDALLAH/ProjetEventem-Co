<?php
// ========================================
// FICHIER : contact.php
// RÔLE : Traiter les messages du formulaire de contact
// PROJET : Événement & Co - DWWM Bac+2
// ========================================

// Charger les dépendances
require_once "includes/db.php";
require_once "includes/email_config.php";
require_once "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Vérifier que le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // ========================================
    // ÉTAPE 1 : Récupérer et nettoyer les données
    // ========================================
    $nom = htmlspecialchars(trim($_POST["nom"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $tel = htmlspecialchars(trim($_POST["tel"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    // ========================================
    // ÉTAPE 2 : Valider les données
    // ========================================
    $errors = [];

    // Validation 1 : Le nom est obligatoire
    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire.";
    }

    // Validation 2 : L'email est obligatoire et doit être valide
    if (empty($email)) {
        $errors[] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }

    // Validation 3 : Le message est obligatoire
    if (empty($message)) {
        $errors[] = "Le message est obligatoire.";
    }

    // ========================================
    // ÉTAPE 3 : Si validation OK, traiter le message
    // ========================================
    if (empty($errors)) {
        try {
            // Sauvegarder dans la base de données
            $sql = "INSERT INTO contacts (nom, email, tel, message) VALUES (:nom, :email, :tel, :message)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':tel' => $tel,
                ':message' => $message,
            ]);

            // Configuration PHPMailer pour l'envoi d'emails
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';

            // Email 1 : Notification à l'administrateur
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress(ADMIN_EMAIL);
            $mail->Subject = 'Nouveau message de contact';
            $mail->Body = "Vous avez reçu un nouveau message de contact :\n\n";
            $mail->Body .= "Nom : $nom\n";
            $mail->Body .= "Email : $email\n";
            $mail->Body .= "Téléphone : $tel\n";
            $mail->Body .= "Message :\n$message\n\n";
            $mail->Body .= "---\nEnvoyé depuis le site Événement & Co";
            $mail->send();

            // Email 2 : Confirmation au client
            $mail->clearAddresses();
            $mail->addAddress($email, $nom);
            $mail->Subject = 'Confirmation de votre message';
            $mail->Body = "Bonjour $nom,\n\n";
            $mail->Body .= "Nous avons bien reçu votre message et nous vous en remercions.\n\n";
            $mail->Body .= "Notre équipe vous répondra dans les plus brefs délais.\n\n";
            $mail->Body .= "Cordialement,\nL'équipe Événement & Co\n\n";
            $mail->Body .= "---\n";
            $mail->Body .= "Ceci est un email automatique, merci de ne pas y répondre.";
            $mail->send();

            // Succès : Rediriger avec message de succès
            header('Location: contact.html?success=1');
            exit();

        } catch (PDOException $e) {
            // Erreur de base de données
            header('Location: contact.html?error=db');
            exit();
        } catch (Exception $e) {
            // Erreur d'envoi d'email
            header('Location: contact.html?error=email');
            exit();
        }

    } else {
        // Erreurs de validation : rediriger avec les messages d'erreur
        $errorMessages = implode('|', $errors);
        header('Location: contact.html?error=validation&messages=' . urlencode($errorMessages));
        exit();
    }

}

?>