<?php
session_start();

// Check if the user is logged in by checking if a session variable is set
$isLoggedIn = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">FinanceTrack</a>
            <div class="d-flex ms-auto position-relative">
                <!-- Menu Button -->
                <button id="menuButton" class="btn btn-outline-secondary">☰</button>

                <!-- Menu Box -->
                <div id="menuBox" class="position-absolute top-100 end-0 bg-light border rounded p-3 shadow" style="display: none; min-width: 150px;">
                    <?php if (!$isLoggedIn): ?>
                        <a href="login.html" class="btn btn-primary w-100 mb-2">Login</a>
                    <?php else: ?>
                        <a href="logout.php" class="btn btn-secondary w-100 mb-2">Logout</a>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-light w-100 mb-2">Main Page</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <h1>Account Details</h1>
        <p>Some content here...</p>
    </div>

    <script src="main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>