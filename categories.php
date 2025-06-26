<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ověříme, jestli je uživatel přihlášen
if (!isset($_SESSION['user_id'])) {
    header('Location: /hostpage.php');
    exit();
}

// Teď už můžeme načítat další věci (HTML, komponenty, DB, atd.)
require_once 'db.php';
include 'header.php';
?>

<h2 class="text-center mt-4">ADD CATEGORIES AND MANAGE BUDGETS</h2>

<div class="container d-flex justify-content-center align-items-center mt-4">
    <div class="col-lg-5 col-md-7 col-sm-10 p-5 bg-light border border-2 rounded-4 shadow text-center">
        <h3 class="mb-3">ADD CATEGORY</h3>
        <form action="/add_category.php" method="POST">
            <div class="mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Add Category</button>
        </form>
    </div>
</div>

<style>
    /* Responzivní úpravy */
    @media (max-width: 768px) {
        .col-lg-5 {
            width: 90%;
        }
    }

    /* Animace tlačítka */
    .btn-success {
        transition: all 0.3s ease-in-out;
    }

    .btn-success:hover {
        transform: scale(1.05);
    }

<?php 
include 'footer.php'; 
?>