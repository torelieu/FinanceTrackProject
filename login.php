<?php
// Database connection
$host = 'localhost';
$db = 'financetrackdb';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check if the user exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Start a session and store user info
            session_start();
            $_SESSION['user'] = $user['first_name'];
            $_SESSION['user_id'] = $user['id']; // Store user ID

            // Redirect to index.php after successful login
            header("Location: index.php");
            exit();
        } else {
            echo "Incorrect password. <a href='login.html'>Try again</a>";
        }
    } else {
        echo "No user found with that email. <a href='register.html'>Register here</a>";
    }
}

$conn->close();
?>