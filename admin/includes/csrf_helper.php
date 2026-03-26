<?php
// Fonctions de protection CSRF
// Événement & Co

// Générer un token CSRF
function generer_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['csrf_token'])) {
        return $_SESSION['csrf_token'];
    }

    // Token aléatoire de 64 caractères
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;

    return $token;
}

// Vérifier si le token est valide
function verifier_csrf_token($token_soumis) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    if (empty($token_soumis)) {
        return false;
    }

    // hash_equals pour éviter les attaques par timing
    return hash_equals($_SESSION['csrf_token'], $token_soumis);
}

// Générer le champ hidden pour les formulaires
function afficher_csrf_input() {
    $token = generer_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// Bloquer la requête si le token est invalide
function proteger_csrf($token_soumis) {
    if (!verifier_csrf_token($token_soumis)) {
        http_response_code(403);
        die('
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Erreur de sécurité</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                    }
                    .error-box {
                        background: white;
                        padding: 3rem;
                        border-radius: 15px;
                        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                        text-align: center;
                        max-width: 500px;
                    }
                    h1 { color: #dc3545; }
                    p { color: #666; line-height: 1.6; }
                    .btn {
                        display: inline-block;
                        margin-top: 1.5rem;
                        padding: 0.75rem 2rem;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                </style>
            </head>
            <body>
                <div class="error-box">
                    <h1>Erreur de sécurité</h1>
                    <p>
                        Token CSRF invalide ou manquant.
                        Votre session a peut-être expiré.
                    </p>
                    <a href="dashboard.php" class="btn">Retour au tableau de bord</a>
                </div>
            </body>
            </html>
        ');
    }
}
?>