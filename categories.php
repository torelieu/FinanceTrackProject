<?php
session_start();
include 'header.php';
require_once 'db.php';

// Kontrola, zda je uživatel přihlášen
if (!isset($_SESSION['user_id'])) {
    header('Location: hostpage.php');
    exit();
}
?>

<br><br><br><br><br><br>
<h2 class="text-center">ADD CATEGORIES AND MANAGE BUDGETS</h2>

<div class="container p-5">
    <div style="width: 40%; margin:auto; text-align:center;" class="mt-3 container p-5 bg-light shadow-lg rounded-4">
        <h3>ADD CATEGORY</h3>
        <div class="row justify-content-center mt-3">
            <form action="add_category.php" method="POST" class="mt-2">
                <div class="mb-3">
                    <label for="category_name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" required>
                </div>
                <button type="submit" class="btn btn-success mt-3">Add Category</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>