<?php
// ========================================
// FICHIER : logout.php
// RÔLE : Déconnexion admin (Logout)
// ========================================

// Démarrer la session
session_start();

// ========================================
// ÉTAPE 1 : Détruire toutes les variables de session
// ========================================

$_SESSION = [];

// ========================================
// ÉTAPE 2 : Détruire le cookie de session (si présent)
// ========================================

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// ========================================
// ÉTAPE 3 : Détruire la session
// ========================================

session_destroy();

// ========================================
// ÉTAPE 4 : Rediriger vers la page de connexion
// ========================================

header('Location: login.php?logout=success');
exit();
?>
