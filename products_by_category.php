<?php
include 'db.php';
$location_id = $_GET['location_id'] ?? null;
// Hämta alla kategorier
$stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $stmt->fetchAll();

$category_id = $_GET['category_id'] ?? null;
$products = [];

$query = "SELECT * FROM products WHERE 1";
$params = [];

if ($category_id) {
    $query .= " AND category_id = ?";
    $params[] = $category_id;
}
if ($location_id) {
    $query .= " AND location_id = ?";
    $params[] = $location_id;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<?php 
$stmt = $pdo->query("SELECT * FROM locations ORDER BY location_name ASC");
$locations = $stmt->fetchAll();
$location_id = $_GET['location_id'] ?? null;
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Produkter per Kategori</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <h1>Välj Kategori</h1>
  
  <form method="get" action="products_by_category.php">
    <label>Kategori:</label>
    <select name="category_id" onchange="this.form.submit()">
        <option value="">Välj kategori</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>" <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['category_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Plats:</label>
    <select name="location_id" onchange="this.form.submit()">
        <option value="">Alla platser</option>
        <?php foreach ($locations as $location): ?>
            <option value="<?= $location['id'] ?>" <?= $location_id == $location['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($location['location_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

  <h2>Produkter i kategori:</h2>
  <?php if (count($products) > 0): ?>
    <div class="product-container">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <?php if ($product['bild']): ?>
                <img src="<?= htmlspecialchars($product['bild']) ?>" alt="Bild på produkten">
            <?php else: ?>
                <img src="default.jpg" alt="Ingen bild tillgänglig">
            <?php endif; ?>

            <h3><?= htmlspecialchars($product['storlek']) ?></h3>
            <p>Kvalité: <?= htmlspecialchars($product['kvalite']) ?></p>

            <?php if ($product['status'] == 'inne'): ?>
                <a href="lend_product.php?id=<?= $product['id'] ?>" class="loan-button">Låna ut</a>
            <?php else: ?>
                <?php
                // Beräkna förväntat återlämningsdatum (lånedatum + 2 veckor)
                $return_date = date('Y-m-d', strtotime($product['lånedatum'] . ' + 14 days'));
                ?>
                <a class="loaned-link" title="Förväntat återlämningsdatum: <?= htmlspecialchars($return_date) ?>"><span class="icon">⏳</span>Utlånad</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
  <p>Inga produkter hittades i denna kategori.</p>
<?php endif; ?>

  <a href="index.php" class="back-button">Tillbaka</a>
</body>
</html>
