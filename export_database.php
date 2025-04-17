<?php
/*include 'db.php'; // Ladda databasuppgifterna från .env via db.php

// Ange rätt headers för nedladdning av filen
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="database_backup.sql"');

// Sätt den fullständiga sökvägen till mysqldump
$mysqldump_path = "C:\\xampp\\mysql\\bin\\mysqldump"; 

// Skapa SQL-dump och skicka som fil till användaren
$command = "\"$mysqldump_path\" -h $host -u $user -p$pass --databases $db";
passthru($command);
exit;*/
include 'db.php';

$backup_path = __DIR__ . "/backups/";
if (!is_dir($backup_path)) {
    mkdir($backup_path, 0755, true);
}

$timestamp = date("Ymd_His");
$backup_file = $backup_path . "webapp_backup_$timestamp.sql";

$command = "mysqldump -h $host -u $user -p$pass --databases $db > $backup_file";
exec($command);

echo "Backup sparad: $backup_file";
?>
?>
