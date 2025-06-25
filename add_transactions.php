<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /hostpage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $transactionDate = $_POST['transaction_date'];
    $transactionType = $_POST['transaction_type'];
    
    $db = Database::getInstance();

    if ($transactionType === "income") {
        // Uložit jako příjem s kategorií "Income"
        $categoryId = null;
        $amount = abs($amount); // Ujistit se, že je kladné
        $_SESSION['message'] = $db->addTransaction($userId, $amount, $transactionDate, $categoryId, "Income");
    } else {
        // Uložit jako výdaj (záporné číslo)
        $categoryId = $_POST['category_id'] ?? null;

        if (!$categoryId) {
            $_SESSION['message'] = "Error: You must select a category for expenses!";
            header('Location: /indexmain.php');
            exit();
        }

        $amount = -abs($amount); // Uložit jako zápornou hodnotu
        $_SESSION['message'] = $db->addTransaction($userId, $amount, $transactionDate, $categoryId);
    }

    header('Location: /indexmain.php');
    exit();
}
?>