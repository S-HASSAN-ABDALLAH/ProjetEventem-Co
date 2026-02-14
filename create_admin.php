<?php
// ========================================
// FICHIER : create_admin.php
// RÔLE : Créer le compte administrateur
// ========================================

// Charger la connexion à la base de données
require_once "db.php";

try {
    // ========================================
    // ÉTAPE 1 : Vérifier si la table utilisateurs existe
    // ========================================

    $sql_check_table = "SHOW TABLES LIKE 'utilisateurs'";
    $stmt_check = $pdo->query($sql_check_table);

    if ($stmt_check->rowCount() == 0) {
        // La table n'existe pas, la créer
        $sql_create_table = "CREATE TABLE utilisateurs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            mot_de_passe VARCHAR(255) NOT NULL,
            role ENUM('admin', 'client') DEFAULT 'client',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql_create_table);
    }

    // ========================================
    // ÉTAPE 2 : Supprimer l'ancien compte admin s'il existe
    // ========================================

    $sql_delete = "DELETE FROM utilisateurs WHERE email = 'admin@evenements-co.fr'";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute();

    // ========================================
    // ÉTAPE 3 : Créer le nouveau compte administrateur
    // ========================================

    $nom = "Administrateur";
    $email = "admin@evenements-co.fr";
    $password = "admin123";

    // Hacher le mot de passe avec bcrypt
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insérer l'admin dans la base de données
    $sql_insert = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
                   VALUES (:nom, :email, :password, 'admin')";

    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':password' => $password_hash
    ]);

    // ========================================
    // Message de succès et redirection
    // ========================================
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Compte admin créé</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(to right, #b8cbb8 0%, #b8cbb8 0%, #b465da 0%, #cf6cc9 33%, #ee609c 66%, #ee609c 100%);                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .success-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                padding: 3rem;
                max-width: 500px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="success-card">
            <div style="font-size: 4rem; color: #28a745;">✅</div>
            <h2 class="mt-3">Compte admin créé !</h2>
            <p class="text-muted mt-3">
                Le compte administrateur a été réinitialisé avec succès.
            </p>
            <div class="alert alert-info mt-4">
                <strong>Identifiants :</strong><br>
                Email : <code>admin@evenements-co.fr</code><br>
                Mot de passe : <code>admin123</code>
            </div>
            <a href="login.php" class="btn btn-primary btn-lg mt-3">
                Se connecter
            </a>
        </div>
    </body>
    </html>
    <?php

} catch (PDOException $e) {
    echo " Erreur : " . $e->getMessage();
}
?>
