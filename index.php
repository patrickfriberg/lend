<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<?php 
include 'db.php';

$user_location_id = $_SESSION['location_id']; // Hämta plats från inloggad användare

$stmt = $pdo->prepare("SELECT * FROM products WHERE location_id = ?");
$stmt->execute([$user_location_id]);
$products = $stmt->fetchAll();
?>
<?php
// Hämta användarens plats-ID från sessionen
$location_id = $_SESSION['location_id'] ?? 1; // Standardlogotyp om inget plats-ID finns

// Kontrollera att filen för logotypen finns
$logo_path = "logos/$location_id.png";
if (!file_exists($logo_path)) {
    $logo_path = "logos/default.png"; // Använd en standardlogotyp om ingen matchning finns
}
?>
<?php include 'navbar2.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FABRIKENS utlåningssystem</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Välkommen, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
  <center>
  <img src="<?= $logo_path ?>" alt="Platsens logotyp" class="startlogo">
  </center>
</body>
</html>

