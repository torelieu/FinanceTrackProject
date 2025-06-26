<?php
// public/login.php
require_once 'db.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = Database::getInstance();
    $db->loginUser($email, $password);
}

include 'head.html';
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="col-lg-4 col-md-6 col-sm-10 p-5 bg-light border border-3 rounded-4 shadow text-center">
        <h2 class="mb-4">LOGIN</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Login</button>
                <a class="btn btn-outline-secondary btn-sm" href="/registerpage.php">Register First</a>
                
                <!-- Google Login Button -->
                <a href="/google-login.php">
                    <img class="google-login mx-auto" src="https://cdn1.iconfinder.com/data/icons/google-s-logo/150/Google_Icons-09-512.png" alt="GoogleLogin">
                </a>

                <a class="btn btn-outline-danger btn-sm" href="/hostpage.php">Go Back</a>
            </div>
        </form>
    </div>
</div>

<style>
    /* Google Login Image Styling */
    .google-login {
        width: 40px;
        display: block;
    }

    /* Mobile Adjustments */
    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }

        .col-lg-4 {
            width: 90%;
        }
    }
</style>


<?php
include 'footer.php';
?>