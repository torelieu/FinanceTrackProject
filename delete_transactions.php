<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: hostpage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['transaction_ids'])) {
    $user_id = $_SESSION['user_id'];
    $transaction_ids = $_POST['transaction_ids'];

    $placeholders = implode(',', array_fill(0, count($transaction_ids), '?'));

    try {
        $stmt = $pdo->prepare("DELETE FROM transactions WHERE id IN ($placeholders) AND user_id = ?");
        $stmt->execute([...$transaction_ids, $user_id]);

        $_SESSION['message'] = "Selected transactions deleted successfully!";
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

header('Location: indexmain.php');
exit();
?>