<?php
// ==========================================
// Fichier de connexion à la base de données (EXEMPLE)
// db.example.php
// ==========================================

// INSTRUCTIONS :
// 1. Copiez ce fichier et renommez-le en "db.php"
// 2. Modifiez les informations ci-dessous avec vos propres paramètres
// 3. Le fichier db.php est dans le .gitignore et ne sera pas partagé sur Git

// Informations de connexion à modifier
$host = 'localhost';          // Adresse du serveur MySQL
$dbname = 'evenement_db';     // Nom de votre base de données
$username = 'root';           // Votre nom d'utilisateur MySQL
$password = '';               // Votre mot de passe MySQL

try {
    // Créer la connexion avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Active l'affichage des erreurs détaillées
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // En cas d'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
