<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>FinanceTrack</title>
    
</head>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Trirong">
<style>
body {
    background-color: #f4f4f9; /* Light neutral color */
    background-image: url('https://www.toptal.com/designers/subtlepatterns/patterns/dotted-pattern.png'); /* Replace with your favorite pattern */
    background-size: cover; /* Ensures the pattern scales nicely */
    margin: 0;
    font-family: 'Arial', sans-serif;
}
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="indexmain.php">FinanceTrack</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="budgets.php">Show Budgets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">Add Category</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="accountpage.php">Account</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger" href="logoutmain.php">➜</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
<?php
?>