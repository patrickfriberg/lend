<?php
session_start();
include 'db.php';

// Kontrollera adminstatus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $location_name = $_POST['location_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];

    $stmt = $pdo->prepare("INSERT INTO locations (location_name, address, city, country) VALUES (?, ?, ?, ?)");
    $stmt->execute([$location_name, $address, $city, $country]);

    header("Location: admin.php");
    exit;
}
?>
