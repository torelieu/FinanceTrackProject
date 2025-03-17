<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['budget_ids'])) {
    $budgetIds = $_POST['budget_ids'];
    
    if (!empty($budgetIds)) {
        $placeholders = implode(',', array_fill(0, count($budgetIds), '?'));

        $stmt = $pdo->prepare("DELETE FROM budgets WHERE id IN ($placeholders) AND user_id = ?");
        $stmt->execute([...$budgetIds, $_SESSION['user_id']]);
    }
}

$_SESSION['message'] = "Selected budgets have been deleted!";
header('Location: budgets.php');
exit();
?>