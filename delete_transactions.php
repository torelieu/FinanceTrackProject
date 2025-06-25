<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /hostpage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['transaction_ids'])) {
    $userId = $_SESSION['user_id'];
    $transactionIds = $_POST['transaction_ids'];

    $db = Database::getInstance();
    $_SESSION['message'] = $db->deleteTransactions($userId, $transactionIds);
}

header('Location: /indexmain.php');
exit();
?>