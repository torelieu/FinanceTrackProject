<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: loginpage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $transaction_date = $_POST['transaction_date'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

    try {
        // Začátek transakce
        $pdo->beginTransaction();

        // Přidání transakce
        $stmt = $pdo->prepare("
            INSERT INTO transactions (user_id, amount, transaction_date)
            VALUES (:user_id, :amount, :transaction_date)
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':amount' => $amount,
            ':transaction_date' => $transaction_date,
        ]);

        // Aktualizace zůstatku
        $stmt = $pdo->prepare("
            UPDATE balances
            SET balance = balance + :amount
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':amount' => $amount,
            ':user_id' => $user_id
        ]);

        // Potvrzení transakce
        $pdo->commit();
        echo "Transakce byla úspěšně přidána!";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Chyba: " . $e->getMessage();
    }
}
?>