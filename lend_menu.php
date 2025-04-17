<?php
// lend_menu.php
include 'db.php';

$stmt = $pdo->query("SELECT products.*, categories.category_name 
                     FROM products 
                     JOIN categories ON products.category_id = categories.id 
                     WHERE products.status = 'inne'");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <title>Låna ut Produkt</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Låna ut Produkt</h1>
  <?php if ($products): ?>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Typ</th>
        <th>Storlek</th>
        <th>Kvalité</th>
        <th>Åtgärd</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $prod): ?>
      <tr>
        <td><?= $prod['id'] ?></td>
        <td><?= htmlspecialchars($prod['category_name']) ?></td>
        <td><?= htmlspecialchars($prod['storlek']) ?></td>
        <td><?= htmlspecialchars($prod['kvalite']) ?></td>
        <td>
          <a href="lend_product.php?id=<?= $prod['id'] ?>" class="returnlink" style="padding: 5px";>Låna ut</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p>Inga produkter finns tillgängliga för utlåning.</p>
  <?php endif; ?>
  
  <a href="index.php" class="back-button">Tillbaka</a>
</body>
</html>
