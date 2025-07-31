<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if (empty($name)) {
        echo "Name is required.";
        exit();
    }

    // Volá metody pro aktualizaci uživatele
    $db = Database::getInstance();
    $message = $db->updateUser($_SESSION['user_id'], $name);

    $_SESSION['message'] = "Data updated succesfuly!";
    header('Location: /accountpage.php');
    exit();
}