<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kontrola přihlášení
if (!isset($_SESSION['user_id'])) {
    header('Location: /hostpage.php');
    exit();
}

require_once 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $categoryName = $_POST['category_name'];

    // Volání metody pro přidání kategorie
    $db = Database::getInstance();
    $message = $db->addCategory($userId, $categoryName);

    $_SESSION['message'] = "Category added successfully!";
    header('Location: /categories.php');
    exit();
}
?>