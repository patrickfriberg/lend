<?php
session_start();
include 'db.php';

// Kontrollera att användaren är inloggad och har rätt roll
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    header("Location: index.php");
    exit;
}

$category_id = $_POST['category_id'];
$storlek = $_POST['storlek'];
$kvalite = $_POST['kvalite'];
$bild_path = null;
$location_id = ($_SESSION['role'] === 'admin') ? $_POST['location_id'] : $_SESSION['location_id'];
// Hämta plats-ID från sessionen

// Hantera filuppladdning om bild skickats
if (isset($_FILES['bild']) && $_FILES['bild']['error'] == 0) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $filename = basename($_FILES['bild']['name']);
    $targetFilePath = $targetDir . time() . "_" . $filename; // Tidsstämpel för unik filnamn
    if (move_uploaded_file($_FILES['bild']['tmp_name'], $targetFilePath)) {
        $bild_path = $targetFilePath;
    }
}

// Spara produkt i databasen
$stmt = $pdo->prepare("INSERT INTO products (category_id, storlek, kvalite, bild, location_id) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$category_id, $storlek, $kvalite, $bild_path, $location_id]);

// Hämta kategorins namn från kategoritabellen
$category_stmt = $pdo->prepare("SELECT category_name FROM categories WHERE id = ?");
$category_stmt->execute([$category_id]);
$category_name = $category_stmt->fetchColumn();

// Lägg till loggpost med kategori
$log_stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, ?)");
$log_stmt->execute([$_SESSION['user_id'], $_SESSION['username'], "Lade till produkt i kategorin $category_name"]);

header("Location: create_product.php?success=1");
exit;
?>
