<?php
$stmt = $pdo->query("SELECT users.username, locations.location_name 
                     FROM users JOIN locations ON users.location_id = locations.id");

$users = $stmt->fetchAll();
?>
<table>
  <thead>
    <tr>
      <th>Användarnamn</th>
      <th>Plats</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user): ?>
    <tr>
      <td><?= htmlspecialchars($user['username']) ?></td>
      <td><?= htmlspecialchars($user['location_name']) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
