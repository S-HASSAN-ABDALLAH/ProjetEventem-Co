<?php
// Gestion des réservations (liste + filtres)
// Événement & Co

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once "../includes/db.php";
require_once "includes/csrf_helper.php";

$csrf_token = generer_csrf_token();

// Récupérer les réservations avec filtres
try {
    $sql = "SELECT r.id, r.nom_client, r.email_client, r.tel_client,
                   r.date_evenement, r.ville, r.nombre_invites,
                   r.statut, r.prix_total, r.created_at,
                   e.nom as nom_evenement
            FROM reservations r
            INNER JOIN evenements e ON r.evenement_id = e.id
            WHERE 1=1";

    $params = [];

    // Filtre par statut
    if (isset($_GET['statut']) && $_GET['statut'] !== '') {
        $sql .= " AND r.statut = :statut";
        $params[':statut'] = $_GET['statut'];
    }

    // Filtre par ville
    if (isset($_GET['ville']) && $_GET['ville'] !== '') {
        $sql .= " AND r.ville = :ville";
        $params[':ville'] = $_GET['ville'];
    }

    $sql .= " ORDER BY r.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erreur admin réservations : " . $e->getMessage());
    die("Une erreur technique est survenue. Veuillez réessayer.");
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des réservations</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .badge-en_attente { background-color: #ffc107; color: #000; }
        .badge-confirme { background-color: #28a745; color: #fff; }
        .badge-annule { background-color: #dc3545; color: #fff; }

        .admin-header {
            background: linear-gradient(to right, #b465da 0%, #cf6cc9 33%, #ee609c 66%, #ee609c 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .table-hover tbody tr:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>

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

<div class="container mb-5">

    <?php if (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Succès !</strong> Le statut de la réservation a été mis à jour.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Supprimée !</strong> La réservation a été annulée.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erreur !</strong> Une erreur est survenue. Veuillez réessayer.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistiques -->
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

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i> Filtres de recherche
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="admin_reservations.php" class="row g-3">

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

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                    <a href="admin_reservations.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </a>
                </div>

            </form>

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
                                    <td><strong>#<?= $resa['id'] ?></strong></td>

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

                                    <td><?= htmlspecialchars($resa['nom_evenement']) ?></td>

                                    <td>
                                        <?php
                                        $date = new DateTime($resa['date_evenement']);
                                        echo $date->format('d/m/Y');
                                        ?>
                                    </td>

                                    <td>
                                        <i class="bi bi-geo-alt"></i>
                                        <?= htmlspecialchars($resa['ville']) ?>
                                    </td>

                                    <td>
                                        <i class="bi bi-people"></i>
                                        <?= $resa['nombre_invites'] ?>
                                    </td>

                                    <td>
                                        <strong><?= number_format($resa['prix_total'], 2, ',', ' ') ?> €</strong>
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
                                        <?php if ($resa['statut'] == 'en_attente'): ?>
                                            <form method="POST" action="update_reservation.php" style="display: inline;">
                                                <?= afficher_csrf_input() ?>
                                                <input type="hidden" name="reservation_id" value="<?= $resa['id'] ?>">
                                                <input type="hidden" name="nouveau_statut" value="confirme">
                                                <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Confirmer cette réservation ?')">
                                                    <i class="bi bi-check-circle"></i> Confirmer
                                                </button>
                                            </form>

                                            <form method="POST" action="update_reservation.php" style="display: inline;">
                                                <?= afficher_csrf_input() ?>
                                                <input type="hidden" name="reservation_id" value="<?= $resa['id'] ?>">
                                                <input type="hidden" name="nouveau_statut" value="annule">
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Annuler cette réservation ?')">
                                                    <i class="bi bi-x-circle"></i> Annuler
                                                </button>
                                            </form>

                                        <?php elseif ($resa['statut'] == 'confirme'): ?>
                                            <form method="POST" action="update_reservation.php" style="display: inline;">
                                                <?= afficher_csrf_input() ?>
                                                <input type="hidden" name="reservation_id" value="<?= $resa['id'] ?>">
                                                <input type="hidden" name="nouveau_statut" value="annule">
                                                <button type="submit" class="btn btn-sm btn-warning"
                                                        onclick="return confirm('Annuler cette réservation confirmée ?')">
                                                    <i class="bi bi-x-circle"></i> Annuler
                                                </button>
                                            </form>

                                        <?php else: ?>
                                            <form method="POST" action="delete_reservation.php" style="display: inline;">
                                                <?= afficher_csrf_input() ?>
                                                <input type="hidden" name="reservation_id" value="<?= $resa['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('ATTENTION : Cette action est IRRÉVERSIBLE.\n\nVoulez-vous vraiment SUPPRIMER définitivement cette réservation ?')">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>