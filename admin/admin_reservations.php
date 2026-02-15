<?php
// ========================================
// FICHIER : admin_reservations.php
// RÔLE : Afficher la liste de toutes les réservations (CRUD - READ)
// ========================================

// Démarrer la session
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Charger la connexion à la base de données
require_once "../includes/db.php";

// ========================================
// ÉTAPE 1 : Récupérer toutes les réservations avec JOIN
// ========================================

try {
    // ========================================
    // Construction dynamique de la requête SQL avec filtres
    // ========================================

    // Base de la requête SQL avec JOIN
    $sql = "SELECT r.id, r.nom_client, r.email_client, r.tel_client,
                   r.date_evenement, r.ville, r.nombre_invites,
                   r.statut, r.prix_total, r.created_at,
                   e.nom as nom_evenement
            FROM reservations r
            INNER JOIN evenements e ON r.evenement_id = e.id
            WHERE 1=1";

    // Tableau pour les paramètres PDO
    $params = [];

    // Filtre par STATUT (si sélectionné)
    if (isset($_GET['statut']) && $_GET['statut'] !== '') {
        $sql .= " AND r.statut = :statut";
        $params[':statut'] = $_GET['statut'];
    }

    // Filtre par VILLE (si sélectionnée)
    if (isset($_GET['ville']) && $_GET['ville'] !== '') {
        $sql .= " AND r.ville = :ville";
        $params[':ville'] = $_GET['ville'];
    }

    // Tri par date de création (plus récent d'abord)
    $sql .= " ORDER BY r.created_at DESC";

    // Exécution de la requête avec les paramètres
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des réservations</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Style personnalisé pour les badges de statut */
        .badge-en_attente {
            background-color: #ffc107;
            color: #000;
        }
        .badge-confirme {
            background-color: #28a745;
            color: #fff;
        }
        .badge-annule {
            background-color: #dc3545;
            color: #fff;
        }

        /* Style pour le header admin */
        .admin-header {
         background: linear-gradient(to right, #b8cbb8 0%, #b8cbb8 0%, #b465da 0%, #cf6cc9 33%, #ee609c 66%, #ee609c 100%);#f093fb 0%, #f5576c 100%);;
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        /* Améliorer la lisibilité du tableau */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<!-- Header Admin -->
<div class="admin-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0">
                    <i class="bi bi-clipboard-data"></i>
                    Gestion des réservations
                </h1>
                <p class="mb-0 mt-2">Panneau d'administration - Événement & Co</p>
            </div>
            <div>
                <a href="dashboard.php" class="btn btn-light me-2">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="logout.php" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Contenu principal -->
<div class="container mb-5">

    <!-- Messages de succès/erreur -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong> Succès !</strong> Le statut de la réservation a été mis à jour.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>🗑️ Supprimée !</strong> La réservation a été annulée.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong> Erreur !</strong> Une erreur est survenue. Veuillez réessayer.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">
                        <i class="bi bi-hourglass-split"></i> En attente
                    </h5>
                    <h2 class="mb-0">
                        <?php
                        $count_attente = count(array_filter($reservations, function($r) {
                            return $r['statut'] == 'en_attente';
                        }));
                        echo $count_attente;
                        ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <i class="bi bi-check-circle"></i> Confirmées
                    </h5>
                    <h2 class="mb-0">
                        <?php
                        $count_confirme = count(array_filter($reservations, function($r) {
                            return $r['statut'] == 'confirme';
                        }));
                        echo $count_confirme;
                        ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        <i class="bi bi-x-circle"></i> Annulées
                    </h5>
                    <h2 class="mb-0">
                        <?php
                        $count_annule = count(array_filter($reservations, function($r) {
                            return $r['statut'] == 'annule';
                        }));
                        echo $count_annule;
                        ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i> Filtres de recherche
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="admin_reservations.php" class="row g-3">

                <!-- Filtre par Statut -->
                <div class="col-md-4">
                    <label for="statut" class="form-label">
                        <i class="bi bi-bookmark"></i> Statut
                    </label>
                    <select name="statut" id="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente"
                            <?php if(isset($_GET['statut']) && $_GET['statut'] == 'en_attente') echo 'selected'; ?>>
                            En attente
                        </option>
                        <option value="confirme"
                            <?php if(isset($_GET['statut']) && $_GET['statut'] == 'confirme') echo 'selected'; ?>>
                            Confirmée
                        </option>
                        <option value="annule"
                            <?php if(isset($_GET['statut']) && $_GET['statut'] == 'annule') echo 'selected'; ?>>
                            Annulée
                        </option>
                    </select>
                </div>

                <!-- Filtre par Ville -->
                <div class="col-md-4">
                    <label for="ville" class="form-label">
                        <i class="bi bi-geo-alt"></i> Ville
                    </label>
                    <select name="ville" id="ville" class="form-select">
                        <option value="">Toutes les villes</option>
                        <option value="Grenoble"
                            <?php if(isset($_GET['ville']) && $_GET['ville'] == 'Grenoble') echo 'selected'; ?>>
                            Grenoble
                        </option>
                        <option value="Lyon"
                            <?php if(isset($_GET['ville']) && $_GET['ville'] == 'Lyon') echo 'selected'; ?>>
                            Lyon
                        </option>
                        <option value="Annecy"
                            <?php if(isset($_GET['ville']) && $_GET['ville'] == 'Annecy') echo 'selected'; ?>>
                            Annecy
                        </option>
                    </select>
                </div>

                <!-- Boutons d'action -->
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                    <a href="admin_reservations.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </a>
                </div>

            </form>

            <!-- Affichage des filtres actifs -->
            <?php if (isset($_GET['statut']) || isset($_GET['ville'])): ?>
                <div class="mt-3 p-2 bg-info bg-opacity-10 rounded">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> <strong>Filtres actifs :</strong>
                        <?php if (isset($_GET['statut']) && $_GET['statut'] !== ''): ?>
                            <span class="badge bg-primary">
                                Statut: <?= htmlspecialchars($_GET['statut']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (isset($_GET['ville']) && $_GET['ville'] !== ''): ?>
                            <span class="badge bg-primary">
                                Ville: <?= htmlspecialchars($_GET['ville']) ?>
                            </span>
                        <?php endif; ?>
                    </small>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Tableau des réservations -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i>
                Liste de toutes les réservations (<?= count($reservations) ?> au total)
            </h5>
        </div>
        <div class="card-body p-0">

            <?php if (empty($reservations)): ?>
                <div class="alert alert-info m-3">
                    <i class="bi bi-info-circle"></i>
                    Aucune réservation pour le moment.
                </div>
            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Événement</th>
                                <th>Date</th>
                                <th>Ville</th>
                                <th>Invités</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $resa): ?>
                                <tr>
                                    <!-- ID -->
                                    <td><strong>#<?= $resa['id'] ?></strong></td>

                                    <!-- Client -->
                                    <td>
                                        <div><strong><?= htmlspecialchars($resa['nom_client']) ?></strong></div>
                                        <small class="text-muted">
                                            <i class="bi bi-envelope"></i>
                                            <?= htmlspecialchars($resa['email_client']) ?>
                                        </small>
                                        <?php if ($resa['tel_client']): ?>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-telephone"></i>
                                                <?= htmlspecialchars($resa['tel_client']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Événement -->
                                    <td><?= htmlspecialchars($resa['nom_evenement']) ?></td>

                                    <!-- Date -->
                                    <td>
                                        <?php
                                        $date = new DateTime($resa['date_evenement']);
                                        echo $date->format('d/m/Y');
                                        ?>
                                    </td>

                                    <!-- Ville -->
                                    <td>
                                        <i class="bi bi-geo-alt"></i>
                                        <?= htmlspecialchars($resa['ville']) ?>
                                    </td>

                                    <!-- Invités -->
                                    <td>
                                        <i class="bi bi-people"></i>
                                        <?= $resa['nombre_invites'] ?>
                                    </td>

                                    <!-- Prix -->
                                    <td>
                                        <strong><?= number_format($resa['prix_total'], 2, ',', ' ') ?> €</strong>
                                    </td>

                                    <!-- Statut -->
                                    <td>
                                        <?php
                                        $badge_class = 'badge-' . $resa['statut'];
                                        $statut_text = '';
                                        switch($resa['statut']) {
                                            case 'en_attente':
                                                $statut_text = ' En attente';
                                                break;
                                            case 'confirme':
                                                $statut_text = ' Confirmée';
                                                break;
                                            case 'annule':
                                                $statut_text = ' Annulée';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= $statut_text ?>
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td>
                                        <?php if ($resa['statut'] == 'en_attente'): ?>
                                            <!-- Bouton Confirmer -->
                                            <form method="POST" action="update_reservation.php" style="display: inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $resa['id'] ?>">
                                                <input type="hidden" name="nouveau_statut" value="confirme">
                                                <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Confirmer cette réservation ?')">
                                                    <i class="bi bi-check-circle"></i> Confirmer
                                                </button>
                                            </form>

                                            <!-- Bouton Annuler -->
                                            <form method="POST" action="update_reservation.php" style="display: inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $resa['id'] ?>">
                                                <input type="hidden" name="nouveau_statut" value="annule">
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Annuler cette réservation ?')">
                                                    <i class="bi bi-x-circle"></i> Annuler
                                                </button>
                                            </form>

                                        <?php elseif ($resa['statut'] == 'confirme'): ?>
                                            <!-- Si déjà confirmée, permettre seulement l'annulation -->
                                            <form method="POST" action="update_reservation.php" style="display: inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $resa['id'] ?>">
                                                <input type="hidden" name="nouveau_statut" value="annule">
                                                <button type="submit" class="btn btn-sm btn-warning"
                                                        onclick="return confirm('Annuler cette réservation confirmée ?')">
                                                    <i class="bi bi-x-circle"></i> Annuler
                                                </button>
                                            </form>

                                        <?php else: ?>
                                            <!-- Si annulée, permettre la suppression définitive -->
                                            <form method="POST" action="delete_reservation.php" style="display: inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $resa['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm(' ATTENTION : Cette action est IRRÉVERSIBLE.\n\nVoulez-vous vraiment SUPPRIMER définitivement cette réservation de la base de données ?')">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
