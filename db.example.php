<?php
// ==========================================
// Configuration Base de données (EXEMPLE)
// db.example.php
// ==========================================

// ⚠️ INSTRUCTIONS :
// 1. Copiez ce fichier et renommez-le en "db.php"
// 2. Remplacez les valeurs ci-dessous par vos informations de connexion
// 3. Ne partagez JAMAIS le fichier "db.php" sur GitHub !

$host = 'localhost';
$dbname = 'evenement_db'; // ← Nom de votre base de données
$username = 'root'; // ← Votre utilisateur MySQL
$password = ''; // ← Votre mot de passe MySQL (vide pour WAMP par défaut)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
