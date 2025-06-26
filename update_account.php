<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// Kontrola, zda je formulář odeslán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if (empty($name)) {
        echo "Name is required.";
        exit();
    }

    // Volání metody pro aktualizaci uživatele
    $db = Database::getInstance();
    $message = $db->updateUser($_SESSION['user_id'], $name);

    $_SESSION['message'] = "Category added successfully!";
    header('Location: /accountpage.php');
    exit();
}