<?php
session_start(); // Start session at the top of the page

// Check if the user is logged in by seeing if a session variable is set
$isLoggedIn = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <h1>Skibidi ohio rizz


        
        </h1>
        <div class="container-fluid">
            <a class="navbar-brand" href="#">FinanceTrack</a>
            <div class="d-flex ms-auto">
                <?php if (!$isLoggedIn): ?>
                    <!-- Show Login button only if the user is not logged in -->
                    <a href="login.html" class="btn btn-primary">Login</a>
                <?php else: ?>
                    <!-- Show a greeting and a logout button if the user is logged in -->
                    <span class="navbar-text me-3">Hello, <?= htmlspecialchars($_SESSION['user']) ?>!</span>
                    <a href="logout.php" class="btn btn-secondary">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Welcome to the Homepage</h1>
        <p>Some content here...</p>
    </div>

    <script src="main.js"></script>
</body>
</html>