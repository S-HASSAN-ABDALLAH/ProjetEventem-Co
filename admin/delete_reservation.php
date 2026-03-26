<?php
// Suppression d'une réservation
// Événement & Co

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once "../includes/db.php";
require_once "includes/csrf_helper.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Vérification CSRF
    proteger_csrf($_POST['csrf_token'] ?? '');

    $reservation_id = (int) $_POST['reservation_id'];

    if ($reservation_id <= 0) {
        header('Location: admin_reservations.php?error=invalid_id');
        exit();
    }

    try {
        $sql_delete = "DELETE FROM reservations WHERE id = :id";
        $stmt = $pdo->prepare($sql_delete);
        $stmt->execute([':id' => $reservation_id]);

        if ($stmt->rowCount() > 0) {
            header('Location: admin_reservations.php?success=deleted');
            exit();
        } else {
            header('Location: admin_reservations.php?error=notfound');
            exit();
        }

    } catch (PDOException $e) {
        header('Location: admin_reservations.php?error=db');
        exit();
    }

} else {
    header('Location: admin_reservations.php');
    exit();
}
?>