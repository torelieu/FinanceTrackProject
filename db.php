<?php
// includes/db.php
$host = 'localhost';
$port = '5432';
$dbname = 'financedb';
$user = 'postgres';
$password = '5y3kb3Fy';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>