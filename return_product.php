<?php
// return_product.php
include 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("UPDATE products SET status = 'inne', lånedatum = NULL, lånant_namn = NULL, lånant_telefon = NULL WHERE id = ?");
$stmt->execute([$id]);

header("Location: utlanade.php?returned=1");
exit;
?>
