<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /loginpage.php");
        exit();
    }

    $userId = $_SESSION['user_id'];
    $categoryId = $_POST['category_id'];
    $month = $_POST['month'] . "-01"; // Konverze na celý formát data
    $amount = $_POST['amount'];

    $db = Database::getInstance();
    $_SESSION['message'] = $db->addBudget($userId, $categoryId, $month, $amount);

    header("Location: /indexmain.php");
    exit();
}
header("Location: /indexmain.php");
exit();
?>