<?php
session_start();
include 'db.php';

// Kontrollera att användaren är inloggad och har behörighet
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    header("Location: index.php");
    exit;
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

// Om moderatorn försöker redigera en produkt på en annan plats, blockera
if ($_SESSION['role'] === 'moderator' && $_SESSION['location_id'] !== $product['location_id']) {
    header("Location: index.php"); // Skicka tillbaka om det inte är deras plats
    exit;
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Redigera Produkt</title>
</head>
<body>
    <h2>Redigera Produkt</h2>
    <form method="post" action="process_update_product.php">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

        <label>Produkt ID</label>
        <input type="text" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>" readonly>

        <label>Storlek:</label>
        <input type="text" name="storlek" value="<?= htmlspecialchars($product['storlek']) ?>" required>

        <label>Kvalité:</label>
        <input type="text" name="kvalite" value="<?= htmlspecialchars($product['kvalite']) ?>" required>

        <!-- Admin kan välja plats, moderator har den automatiskt -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <label>Plats:</label>
            <select name="location_id">
                <?php
                $stmt = $pdo->query("SELECT * FROM locations ORDER BY location_name ASC");
                while ($row = $stmt->fetch()) {
                    echo "<option value='{$row['id']}' " . ($product['location_id'] == $row['id'] ? 'selected' : '') . ">{$row['location_name']}</option>";
                }
                ?>
            </select>
        <?php else: ?>
            <input type="hidden" name="location_id" value="<?= $_SESSION['location_id'] ?>">
        <?php endif; ?>

        <button type="submit">Spara ändringar</button>
    </form>
</body>
</html>
