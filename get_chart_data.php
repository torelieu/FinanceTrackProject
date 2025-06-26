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
$month = $_GET['month'] ?? date('Y-m');

$response = [
    'balanceOverTime' => $db->getTransactions($userId, $month),
    'spendingByCategory' => $db->getSpendingByCategory($userId, $month),
    'incomeVsExpenses' => $db->getIncomeVsExpenses($userId, $month)
];

echo json_encode($response);
?>