<?php
// process_search.php
include 'db.php';

$category_id = $_GET['category_id'];
$sort = ($_GET['sort'] === 'asc') ? 'ASC' : 'DESC';

$query = "SELECT products.*, categories.category_name FROM products 
          JOIN categories ON products.category_id = categories.id";

if ($category_id) {
    $query .= " WHERE products.category_id = ?";
    $stmt = $pdo->prepare($query . " ORDER BY storlek $sort");
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query($query . " ORDER BY storlek $sort");
}

$products = $stmt->fetchAll();
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Sökresultat</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Sökresultat för Kategori: <?= htmlspecialchars($category_id) ?></h1>
  
  <?php if ($products): ?>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Typ</th>
        <th>Storlek</th>
        <th>Kvalité</th>
        <th>Bild</th>
        <th>Status</th>
        <th>Åtgärd</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $prod): ?>
        <tr>
          <td><?= $prod['id'] ?></td>
          <td><?= htmlspecialchars($prod['typ']) ?></td>
          <td><?= htmlspecialchars($prod['storlek']) ?></td>
          <td><?= htmlspecialchars($prod['kvalite']) ?></td>
          <td>
            <?php if ($prod['bild']): ?>
              <img src="<?= htmlspecialchars($prod['bild']) ?>" alt="Produktbild" style="max-width:100px;">
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($prod['status']) ?></td>
          <td>
            <!-- Länkar för borttag, utlåning/återlämning -->
            <a href="delete_product.php?id=<?= $prod['id'] ?>" class="returnlink">Ta bort</a>
            <?php if ($prod['status'] == "inne"): ?>
              <a href="lend_product.php?id=<?= $prod['id'] ?>" class="returnlink">Låna ut</a>
            <?php else: ?>
              <a href="return_product.php?id=<?= $prod['id'] ?>" class="returnlink">Återlämna</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p>Inga produkter hittades.</p>
  <?php endif; ?>
  
  <a href="index.php" class="back-button">Tillbaka</a>
</body>
</html>
