<?php
// public/register.php
include 'db.php';
include 'head.html';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password]);
        echo "<div class='alert alert-success'>Registration successful!</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<br>
<br>
<br>
<br>
<div style="width: 35%; margin:auto; text-align:center;" class="mt-6 container p-5 my-5 bg-light rounded-4 shadow-lg">
    <h2>REGISTER</h2>
    <br>
    <div class="row justify-content-center">
        <form style="width:80%;" method="POST" action="">
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
            <div style="width:40%; margin:auto;" class="buttons">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Register</button>
                    <a class="btn btn-outline-secondary btn-sm" href="loginpage.php">Login Page</a>
                </div>
            </div>
        </form>
    </div>
</div>




<?php
include 'footer.php';
?>