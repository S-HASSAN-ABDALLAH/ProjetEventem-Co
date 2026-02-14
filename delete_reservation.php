<?php
// ========================================
// FICHIER : delete_reservation.php
// RÔLE : Supprimer une réservation (CRUD - DELETE)
// PROJET : Événement & Co - DWWM Bac+2
// ========================================

// Démarrer la session
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Charger la connexion à la base de données
require_once "db.php";

// Vérifier que le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // ========================================
    // ÉTAPE 1 : Récupérer l'ID de la réservation
    // ========================================

    $reservation_id = (int) $_POST['reservation_id'];

    // ========================================
    // ÉTAPE 2 : Validation
    // ========================================

    if ($reservation_id <= 0) {
        header('Location: admin_reservations.php?error=invalid_id');
        exit();
    }

    // ========================================
    // ÉTAPE 3 : Supprimer la réservation
    // ========================================

    try {
        // Requête DELETE
        $sql_delete = "DELETE FROM reservations WHERE id = :id";

        $stmt = $pdo->prepare($sql_delete);
        $stmt->execute([':id' => $reservation_id]);

        // Vérifier si une ligne a été supprimée
        if ($stmt->rowCount() > 0) {
            // Succès : rediriger avec message
            header('Location: admin_reservations.php?success=deleted');
            exit();
        } else {
            // Aucune suppression (ID n'existe pas)
            header('Location: admin_reservations.php?error=notfound');
            exit();
        }

    } catch (PDOException $e) {
        // Erreur de base de données
        header('Location: admin_reservations.php?error=db');
        exit();
    }

} else {
    // Si accès direct (pas via POST), rediriger
    header('Location: admin_reservations.php');
    exit();
}
?>
