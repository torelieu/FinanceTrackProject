<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

require_once 'db.php';

$db = Database::getInstance();
$userId = $_SESSION['user_id'];
$sortBy = $_GET['sort'] ?? 'latest';
$category = $_GET['category'] ?? '';

$transactions = $db->getFilteredTransactions($userId, $sortBy, $category);
echo json_encode($transactions);
?>