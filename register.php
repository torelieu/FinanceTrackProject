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

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $surname = $conn->real_escape_string($_POST['surname']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert data into the database
    $sql = "INSERT INTO users (first_name, surname, email, password) VALUES ('$firstName', '$surname', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "Registration successful! <a href='login.html'>Go to Login</a>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>