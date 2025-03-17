<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: hostpage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $transaction_date = $_POST['transaction_date'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

    // Prevent positive amounts when a category is selected (Outcome validation)
    if ($category_id && $amount > 0) {
        $_SESSION['message'] = "Error: You cannot set a positive amount for an outcome transaction!";
        header('Location: indexmain.php');
        exit();
    }

    try {
        // Database operations
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("
            INSERT INTO transactions (user_id, amount, transaction_date, category_id)
            VALUES (:user_id, :amount, :transaction_date, :category_id)
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':amount' => $amount,
            ':transaction_date' => $transaction_date,
            ':category_id' => $category_id
        ]);

        // Balance update
        $stmt = $pdo->prepare("
            UPDATE balances
            SET balance = balance + :amount
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':amount' => $amount,
            ':user_id' => $user_id
        ]);

        $pdo->commit();
        $_SESSION['message'] = "Transaction added successfully!";
        header('Location: indexmain.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}
?>