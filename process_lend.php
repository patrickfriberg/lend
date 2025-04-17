<?php
// process_lend.php
include 'db.php';

$id = $_POST['id'];
$lånant_namn = $_POST['lånant_namn'];
$lånant_telefon = $_POST['lånant_telefon'];
$lånedatum = $_POST['lånedatum'];
$location_id = $_POST['location_id'];

// Uppdatera produkten med utlåningsstatus
$stmt = $pdo->prepare("UPDATE products SET status = 'utlånad', lånedatum = ?, lånant_namn = ?, lånant_telefon = ? WHERE id = ?");
$stmt->execute([$lånedatum, $lånant_namn, $lånant_telefon, $id]);

// Lägg också in en post i loans‑tabellen för historik
$stmt2 = $pdo->prepare("INSERT INTO loans (product_id, lånedatum, lånant_namn, lånant_telefon, location_id) VALUES (?, ?, ?, ?, ?)");
$stmt2->execute([$id, $lånedatum, $lånant_namn, $lånant_telefon, $location_id]);

header("Location: utlanade.php?success=1");
exit;
?>
