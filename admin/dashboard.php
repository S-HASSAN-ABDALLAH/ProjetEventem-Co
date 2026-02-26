<?php
// ========================================
// FICHIER : dashboard.php
// RÔLE : Tableau de bord admin avec statistiques SQL avancées
// ========================================

// Démarrer la session
session_start();

// ========================================
// ÉTAPE 1 : Vérifier que l'utilisateur est connecté
// ========================================

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Charger la connexion à la base de données
require_once "../includes/db.php";

// ========================================
// ÉTAPE 2 : Récupérer les statistiques SQL
// ========================================

try {

    // STATISTIQUE 1 : Nombre total de réservations
    $sql_total = "SELECT COUNT(*) as total FROM reservations";
    $stmt_total = $pdo->query($sql_total);
    $total_reservations = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

    // STATISTIQUE 2 : Nombre de réservations en attente
    $sql_attente = "SELECT COUNT(*) as total FROM reservations WHERE statut = 'en_attente'";
    $stmt_attente = $pdo->query($sql_attente);
    $total_attente = $stmt_attente->fetch(PDO::FETCH_ASSOC)['total'];

    // STATISTIQUE 3 : Nombre de réservations confirmées
    $sql_confirme = "SELECT COUNT(*) as total FROM reservations WHERE statut = 'confirme'";
    $stmt_confirme = $pdo->query($sql_confirme);
    $total_confirme = $stmt_confirme->fetch(PDO::FETCH_ASSOC)['total'];

    // STATISTIQUE 4 : Nombre de réservations annulées
    $sql_annule = "SELECT COUNT(*) as total FROM reservations WHERE statut = 'annule'";
    $stmt_annule = $pdo->query($sql_annule);
    $total_annule = $stmt_annule->fetch(PDO::FETCH_ASSOC)['total'];

    // STATISTIQUE 5 : Réservations récentes (dernières 5)
    $sql_recentes = "SELECT r.id, r.nom_client, r.date_evenement, r.statut,
                            e.nom as nom_evenement
                     FROM reservations r
                     INNER JOIN evenements e ON r.evenement_id = e.id
                     ORDER BY r.created_at DESC
                     LIMIT 5";
    $stmt_recentes = $pdo->query($sql_recentes);
    $reservations_recentes = $stmt_recentes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Événement & Co</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .dashboard-header {
            background: linear-gradient(to right, #b8cbb8 0%, #b8cbb8 0%, #b465da 0%, #cf6cc9 33%, #ee609c 66%, #ee609c 100%);           
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card.total { border-left-color: #667eea; }
        .stat-card.attente { border-left-color: #ffc107; }
        .stat-card.confirme { border-left-color: #28a745; }
        .stat-card.annule { border-left-color: #dc3545; }
        .stat-card.ca { border-left-color: #17a2b8; }
        .stat-card.invites { border-left-color: #fd7e14; }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .badge-en_attente { background-color: #ffc107; color: #000; }
        .badge-confirme { background-color: #28a745; color: #fff; }
        .badge-annule { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>

<!-- Header -->
<div class="dashboard-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0">
                    <i class="bi bi-speedometer2"></i>
                    Tableau de bord
                </h1>
                <p class="mb-0 mt-2">Bienvenue, <?= htmlspecialchars($_SESSION['user_nom']) ?> !</p>
            </div>
            <div>
                <a href="admin_reservations.php" class="btn btn-light me-2">
                    <i class="bi bi-list-ul"></i> Voir les réservations
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

    <!-- Ligne 1 : Statistiques principales -->
    <div class="row g-4 mb-4">

        <!-- Total réservations -->
        <div class="col-md-3">
            <div class="card stat-card total h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">Total réservations</h6>
                        <i class="bi bi-calendar-check fs-3 text-primary"></i>
                    </div>
                    <div class="stat-number text-primary"><?= $total_reservations ?></div>
                </div>
            </div>
        </div>

        <!-- En attente -->
        <div class="col-md-3">
            <div class="card stat-card attente h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">En attente</h6>
                        <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                    </div>
                    <div class="stat-number text-warning"><?= $total_attente ?></div>
                </div>
            </div>
        </div>

        <!-- Confirmées -->
        <div class="col-md-3">
            <div class="card stat-card confirme h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">Confirmées</h6>
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                    </div>
                    <div class="stat-number text-success"><?= $total_confirme ?></div>
                </div>
            </div>
        </div>

        <!-- Annulées -->
        <div class="col-md-3">
            <div class="card stat-card annule h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">Annulées</h6>
                        <i class="bi bi-x-circle fs-3 text-danger"></i>
                    </div>
                    <div class="stat-number text-danger"><?= $total_annule ?></div>
                </div>
            </div>
        </div>

    </div>

    <!-- Réservations récentes -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="bi bi-clock-history"></i>
                Réservations récentes
            </h5>
        </div>
        <div class="card-body p-0">

            <?php if (empty($reservations_recentes)): ?>
                <div class="alert alert-info m-3">
                    <i class="bi bi-info-circle"></i>
                    Aucune réservation récente
                </div>
            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Événement</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations_recentes as $resa): ?>
                                <tr>
                                    <td><strong>#<?= $resa['id'] ?></strong></td>
                                    <td><?= htmlspecialchars($resa['nom_client']) ?></td>
                                    <td><?= htmlspecialchars($resa['nom_evenement']) ?></td>
                                    <td>
                                        <?php
                                        $date = new DateTime($resa['date_evenement']);
                                        echo $date->format('d/m/Y');
                                        ?>
                                    </td>
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
                                    <td>
                                        <a href="admin_reservations.php" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Voir
                                        </a>
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
