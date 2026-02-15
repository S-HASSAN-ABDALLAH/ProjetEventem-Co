<?php
// ==========================================
// Configuration Email - PHPMailer (EXEMPLE)
// email_config.example.php
// ==========================================

// ⚠️ INSTRUCTIONS :
// 1. Copiez ce fichier et renommez-le en "email_config.php"
// 2. Remplacez les valeurs ci-dessous par vos vraies informations
// 3. Ne partagez JAMAIS le fichier "email_config.php" sur GitHub !

// Configuration SMTP Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre.email@gmail.com'); // ← Remplacez par votre email Gmail
define('SMTP_PASSWORD', 'VOTRE_APP_PASSWORD_ICI'); // ← Remplacez par votre App Password de 16 caractères
define('SMTP_FROM_EMAIL', 'votre.email@gmail.com'); // ← Même email que SMTP_USERNAME
define('SMTP_FROM_NAME', 'Événement & Co');

// Email où vous recevez les notifications
define('ADMIN_EMAIL', 'votre.email@gmail.com'); // ← Email de réception des messages

?>
