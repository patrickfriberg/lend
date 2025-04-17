<?php include 'db.php';

if (!empty($_POST['approved'])) {
    foreach ($_POST['approved'] as $userId) {
        $stmt = $pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?");
        $stmt->execute([$userId]);
    }
    header("Location: admin.php?approved=1");
}
?>