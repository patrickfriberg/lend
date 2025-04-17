<?php
session_start();
include 'db.php';

// Kontrollera att användaren är inloggad och har behörighet
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $storlek = $_POST['storlek'];
    $kvalite = $_POST['kvalite'];

    // Om admin redigerar kan platsen ändras, annars behåll moderatorns plats
    $location_id = ($_SESSION['role'] === 'admin') ? $_POST['location_id'] : $_SESSION['location_id'];

    // Uppdatera produkten i databasen
    $stmt = $pdo->prepare("UPDATE products SET product_name = ?, storlek = ?, kvalite = ?, location_id = ? WHERE id = ?");
    $stmt->execute([$product_name, $storlek, $kvalite, $location_id, $product_id]);

    header("Location: edit_product.php?id=$product_id&success=1");
    exit;
}
?>
