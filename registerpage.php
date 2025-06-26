<?php
// public/register.php
include 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = Database::getInstance();
    $db->registerUser($username, $email, $password);
}

include 'head.html';
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="col-lg-4 col-md-6 col-sm-10 p-5 bg-light border border-3 rounded-4 shadow text-center">
        <h2 class="mb-4">REGISTER</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Register</button>
                <a class="btn btn-outline-secondary btn-sm" href="/loginpage.php">Login Page</a>
            </div>
        </form>
    </div>
</div>

<style>
    /* Mobilní přizpůsobení */
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