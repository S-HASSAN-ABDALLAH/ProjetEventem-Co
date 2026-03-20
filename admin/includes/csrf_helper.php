<?php
// ========================================
// FICHIER : csrf_helper.php
// RÔLE : Fonctions de gestion des tokens CSRF (Cross-Site Request Forgery)
// PROJET : Événement & Co - DWWM Bac+2
// ========================================

/**
 * PROTECTION CSRF - QU'EST-CE QUE C'EST ?
 *
 * CSRF (Cross-Site Request Forgery) est une attaque où un site malveillant
 * force un utilisateur connecté à exécuter des actions non désirées sur notre site.
 *
 * EXEMPLE D'ATTAQUE SANS PROTECTION CSRF :
 * 1. L'admin se connecte sur notre site (session active)
 * 2. L'admin visite un site pirate (dans un autre onglet)
 * 3. Le site pirate envoie une requête POST vers notre site pour supprimer une réservation
 * 4. Notre serveur accepte car l'admin a une session valide
 * 5. La réservation est supprimée sans que l'admin le sache !
 *
 * SOLUTION : TOKEN CSRF
 * Un token CSRF est un code secret unique généré pour chaque session.
 * Il est ajouté dans chaque formulaire et vérifié à chaque soumission.
 * Le site pirate ne connaît pas ce token, donc la requête sera refusée.
 */

// ========================================
// FONCTION 1 : Générer un token CSRF
// ========================================

/**
 * Génère un nouveau token CSRF et le stocke en session
 *
 * @return string Le token généré (chaîne hexadécimale de 64 caractères)
 */
function generer_csrf_token() {
    // Vérifier qu'une session est active
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si un token existe déjà, le retourner (un seul token par session)
    if (isset($_SESSION['csrf_token'])) {
        return $_SESSION['csrf_token'];
    }

    // Générer un token aléatoire sécurisé
    // random_bytes(32) génère 32 octets aléatoires
    // bin2hex() convertit en chaîne hexadécimale (64 caractères)
    $token = bin2hex(random_bytes(32));

    // Stocker le token dans la session
    $_SESSION['csrf_token'] = $token;

    return $token;
}

// ========================================
// FONCTION 2 : Vérifier un token CSRF
// ========================================

/**
 * Vérifie si le token CSRF soumis correspond à celui en session
 *
 * @param string $token_soumis Le token reçu via POST
 * @return bool True si valide, False si invalide
 */
function verifier_csrf_token($token_soumis) {
    // Vérifier qu'une session est active
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Vérification 1 : Le token existe-t-il en session ?
    if (!isset($_SESSION['csrf_token'])) {
        return false; // Pas de token en session = invalide
    }

    // Vérification 2 : Le token soumis existe-t-il ?
    if (empty($token_soumis)) {
        return false; // Token vide = invalide
    }

    // Vérification 3 : Les deux tokens correspondent-ils ?
    // hash_equals() est utilisé pour éviter les attaques par timing
    // (fonction PHP sécurisée pour comparer des chaînes sensibles)
    return hash_equals($_SESSION['csrf_token'], $token_soumis);
}

// ========================================
// FONCTION 3 : Afficher un champ input caché avec le token
// ========================================

/**
 * Génère un champ HTML caché contenant le token CSRF
 * À utiliser dans tous les formulaires
 *
 * @return string Code HTML du champ input
 */
function afficher_csrf_input() {
    $token = generer_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// ========================================
// FONCTION 4 : Bloquer la requête si token invalide
// ========================================

/**
 * Vérifie le token CSRF et bloque la requête si invalide
 * Affiche une erreur et arrête le script
 *
 * @param string $token_soumis Le token reçu via POST
 * @return void (arrête le script si invalide)
 */
function proteger_csrf($token_soumis) {
    if (!verifier_csrf_token($token_soumis)) {
        // Token invalide = tentative d'attaque CSRF
        http_response_code(403); // Code HTTP 403 : Accès interdit
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
                    .error-icon {
                        font-size: 4rem;
                        color: #dc3545;
                    }
                    h1 {
                        color: #dc3545;
                        margin: 1rem 0;
                    }
                    p {
                        color: #666;
                        line-height: 1.6;
                    }
                    .btn {
                        display: inline-block;
                        margin-top: 1.5rem;
                        padding: 0.75rem 2rem;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class="error-box">
                    <div class="error-icon">🛡️</div>
                    <h1>Erreur de sécurité CSRF</h1>
                    <p>
                        <strong>Token CSRF invalide ou manquant.</strong><br><br>
                        Cette requête a été bloquée pour des raisons de sécurité.
                        Il se peut que votre session ait expiré ou qu\'une tentative
                        d\'attaque CSRF ait été détectée.
                    </p>
                    <p>
                        <em>Code erreur : 403 Forbidden</em>
                    </p>
                    <a href="dashboard.php" class="btn">Retour au tableau de bord</a>
                </div>
            </body>
            </html>
        ');
    }
    // Si le token est valide, ne rien faire et continuer l'exécution
}

// ========================================
// NOTES POUR LES ÉTUDIANTS DWWM
// ========================================

/*
 * COMMENT UTILISER CES FONCTIONS ?
 *
 * 1. DANS LES FORMULAIRES (exemple : admin_reservations.php)
 *    Ajouter le champ caché avec le token :
 *
 *    <form method="POST" action="update_reservation.php">
 *        <?php echo afficher_csrf_input(); ?>
 *        <input type="text" name="nom">
 *        <button type="submit">Envoyer</button>
 *    </form>
 *
 *
 * 2. DANS LES FICHIERS DE TRAITEMENT (exemple : update_reservation.php)
 *    Vérifier le token au début du script :
 *
 *    require_once 'includes/csrf_helper.php';
 *
 *    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 *        proteger_csrf($_POST['csrf_token'] ?? '');
 *        // Si le script continue ici, le token est valide ✅
 *        // Traiter les données du formulaire...
 *    }
 *
 *
 * AVANTAGES :
 * ✅ Protection contre les attaques CSRF
 * ✅ Code réutilisable et facile à maintenir
 * ✅ Conforme aux standards de sécurité web
 * ✅ Niveau professionnel pour un projet DWWM
 */
?>
