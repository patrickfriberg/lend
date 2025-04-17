<?php
session_start();
include 'db.php';

// Kontrollera att användaren är inloggad och har behörighet
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    header("Location: index.php");
    exit;
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT location_id FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

// Om moderatorn försöker ta bort en produkt på en annan plats, blockera
if ($_SESSION['role'] === 'moderator' && $_SESSION['location_id'] !== $product['location_id']) {
    header("Location: index.php"); // Skicka tillbaka till startsidan
    exit;
}

// Ta bort produkten
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$product_id]);

// Lägg till loggpost
$log_stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, ?)");
$log_stmt->execute([$_SESSION['user_id'], $_SESSION['username'], "Tog bort produkt-ID: $product_id"]);

header("Location: search_product.php?deleted=1");
exit;
?>
