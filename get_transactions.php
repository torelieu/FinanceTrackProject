<?php
require 'db.php'; // Připojení k databázi
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];
$month = $_GET['month'] ?? date('Y-m');

try {
    $stmt = $pdo->prepare("
        SELECT 
            TO_CHAR(transaction_date, 'YYYY-MM-DD') AS day,
            SUM(amount) AS total
        FROM transactions
        WHERE user_id = :user_id AND TO_CHAR(transaction_date, 'YYYY-MM') = :month
        GROUP BY day
        ORDER BY day
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':month' => $month
    ]);

    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($transactions);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>