<?php
// ========================================
// FICHIER : login.php
// RÔLE : Authentification admin (Login)
// ========================================

// Démarrer la session
session_start();

// Si l'utilisateur est déjà connecté, rediriger vers dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Charger la connexion à la base de données
require_once "../includes/db.php";

// Variable pour stocker les erreurs
$error_message = "";

// ========================================
// TRAITEMENT DU FORMULAIRE DE CONNEXION
// ========================================

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Récupérer les données du formulaire
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password']; // Ne pas htmlspecialchars le mot de passe

    // ========================================
    // ÉTAPE 1 : Validation basique
    // ========================================

    if (empty($email) || empty($password)) {
        $error_message = "Veuillez remplir tous les champs.";
    } else {

        // ========================================
        // ÉTAPE 2 : Rechercher l'utilisateur dans la base de données
        // ========================================

        try {
            $sql = "SELECT id, nom, email, mot_de_passe, role
                    FROM utilisateurs
                    WHERE email = :email
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // ========================================
            // ÉTAPE 3 : Vérifier si l'utilisateur existe
            // ========================================

            if ($user) {

                // ========================================
                // ÉTAPE 4 : Vérifier le mot de passe avec password_verify()
                // ========================================

                if (password_verify($password, $user['mot_de_passe'])) {

                    // ÉTAPE 5 : Connexion réussie → Créer la session
                    

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];

                    // Rediriger vers le dashboard
                    header('Location: dashboard.php');
                    exit();

                } 
                
                else {
                    // Mot de passe incorrect
                    $error_message = "Email ou mot de passe incorrect.";
                }

            } else {
                // Utilisateur n'existe pas
                $error_message = "Email ou mot de passe incorrect.";
            }

        } catch (PDOException $e) {
            $error_message = "Erreur de connexion à la base de données.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Événement & Co</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #b8cbb8 0%, #b8cbb8 0%, #b465da 0%, #cf6cc9 33%, #ee609c 66%, #ee609c 100%);            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background-image: linear-gradient(to top, #cc208e 0%, #6713d2 100%);            
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2rem;
        }

        .btn-login {
            background-image: linear-gradient(to top, #cc208e 0%, #6713d2 100%);            
            border: none;
            color: white;
            padding: 0.75rem;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Header -->
    <div class="login-header">
        <i class="bi bi-shield-lock" style="font-size: 3rem;"></i>
        <h3 class="mt-3 mb-0">Connexion Admin</h3>
        <p class="mb-0 mt-2">Événement & Co</p>
    </div>

    <!-- Body -->
    <div class="login-body">

        <!-- Message d'erreur -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST" action="login.php">

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope"></i> Adresse e-mail
                </label>
                <input type="email" class="form-control" id="email" name="email"
                       placeholder="admin@evenements-co.fr" required autofocus>
            </div>

            <!-- Mot de passe -->
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="bi bi-lock"></i> Mot de passe
                </label>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="••••••••" required>
            </div>

            <!-- Bouton de connexion -->
            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right"></i>
                Se connecter
            </button>

        </form>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
