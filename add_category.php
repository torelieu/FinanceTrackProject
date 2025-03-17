<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: loginpage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $category_name = $_POST['category_name'];

    try {
        // Check if the category already exists
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM categories 
            WHERE user_id = :user_id AND name = :name
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':name' => $category_name,
        ]);
        $category_exists = $stmt->fetchColumn() > 0;

        if ($category_exists) {
            echo "Category already exists!";
        } else {
            // Begin the transaction
            $pdo->beginTransaction();

            // Add the category
            $stmt = $pdo->prepare("
                INSERT INTO categories (user_id, name)
                VALUES (:user_id, :name)
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':name' => $category_name,
            ]);

            $pdo->commit();
            echo "Category added successfully!";
            header('Location: indexmain.php');
            exit();
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "Error: " . $e->getMessage();
    }
}
?>