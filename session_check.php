<?php
if (session_status() === PHP_SESSION_NONE){
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Skicka användaren till login om sessionen saknas
    exit;
}

// Se till att rollen är korrekt inställd
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'user'; // Standardroll om rollen saknas
}
?>
