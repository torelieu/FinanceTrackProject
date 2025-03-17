<?php
session_start();
require_once 'db.php';

// Zkontrolujeme, zda je formulář odeslán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if (empty($name)) {
        echo "Name and email are required.";
        exit();
    }

    // Aktualizace údajů v databázi
    $stmt = $pdo->prepare("UPDATE users SET username = :name WHERE id = :id");
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Údaje byly úspěšně aktualizovány!";
        header('Location: accountpage.php');
        exit();
    } else {
        echo "Došlo k chybě při aktualizaci údajů.";
        exit();
    }
}
?>