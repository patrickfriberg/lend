<?php
session_start();
include 'db.php';

// Kontrollera att endast admin kan ändra roller
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];

    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);

    header("Location: admin.php");
    exit;
}
?>
