<?php
session_start();
include 'db.php';

// Kontrollera adminstatus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $new_location_id = $_POST['new_location_id'];

    $stmt = $pdo->prepare("UPDATE users SET location_id = ? WHERE id = ?");
    $stmt->execute([$new_location_id, $user_id]);

    header("Location: admin.php");
    exit;
}
?>
