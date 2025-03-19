<?php
session_start();
require_once 'db.php';

// Kontrola přihlášení
if (!isset($_SESSION['user_id'])) {
    header('Location: loginpage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $categoryName = $_POST['category_name'];

    // Volání metody pro přidání kategorie
    $db = Database::getInstance();
    $message = $db->addCategory($userId, $categoryName);

    echo $message;
    if ($message === "Category added successfully!") {
        header('Location: indexmain.php');
        exit();
    }
}
?>