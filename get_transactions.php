<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$db = Database::getInstance();
$userId = $_SESSION['user_id'];
$sortBy = $_GET['sort'] ?? 'latest';
$category = $_GET['category'] ?? '';

$transactions = $db->getFilteredTransactions($userId, $sortBy, $category);
echo json_encode($transactions);
?>