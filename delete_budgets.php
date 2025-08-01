<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /hostpage.php');
    exit();
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['budget_ids'])) {
    $db = Database::getInstance();
    $_SESSION['message'] = $db->deleteBudgets($_SESSION['user_id'], $_POST['budget_ids']);
}

header('Location: /budgets.php');
exit();
?>