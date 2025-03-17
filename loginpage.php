<?php
// public/login.php
include 'db.php';
include 'head.html';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        header('Location: indexmain.php');
    } else {
        echo "<div class='alert alert-danger'>Invalid email or password</div>";
    }
}
?>

<br>
<br>
<br>
<br>
<div style="width:35%; margin:auto; text-align:center;" class="container p-5 my-5 bg-light border border-3 rounded-4">
    <h2>LOGIN</h2>
    <br>
    <div class="row justify-content-center">
        <form style="width:80%;" method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div style="width:40%; margin:auto;" class="buttons">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a class="btn btn-outline-secondary btn-sm" href="registerpage.php">Register First</a>
                    <a href="google-login.php">
                        <img style="width:30%;" src="https://cdn1.iconfinder.com/data/icons/google-s-logo/150/Google_Icons-09-512.png" alt="GoogleLogin">
                    </a>
                    <a class="btn btn-outline-danger btn-sm" href="hostpage.php">Go Back</a>
                </div>
            </div>
            
         </form>
    </div>
</div>


<?php
include 'footer.php';
?>