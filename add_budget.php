<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'];
    $month = $_POST['month'] . "-01"; // Convert to full date format
    $amount = $_POST['amount'];

    // Check if budget already exists
    $stmt = $pdo->prepare("SELECT * FROM budgets WHERE user_id = ? AND category_id = ? AND month = ?");
    $stmt->execute([$user_id, $category_id, $month]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Budget already exists for this category and month!";
        header("Location: indexmain.php");
        exit();
    }

    if ($amount < 0) {
        $_SESSION['message'] = "You cannot set a negative amount for an budget!";
        header("Location: indexmain.php");
        exit();
    }
    elseif($amount == 0)
    {
        $_SESSION['message'] = "You cannot set a null amount for an budget!";
        header("Location: indexmain.php");
        exit();
    }
    else
    {
        // Insert budget into database
    $stmt = $pdo->prepare("INSERT INTO budgets (user_id, category_id, month, amount) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $category_id, $month, $amount])) {
        $_SESSION['message'] = "Budget successfully created!";
    } else {
        $_SESSION['message'] = "Failed to create budget.";
    }
    }
    header("Location: indexmain.php");
    exit();
} else {
    header("Location: indexmain.php");
    exit();
}
?>