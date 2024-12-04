<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to save a budget.");
}

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
    $userId = $_SESSION['user_id'];
    $monthYear = $conn->real_escape_string($_POST['month_year']);
    $amount = $conn->real_escape_string($_POST['amount']);

    // Validate inputs
    if (empty($monthYear) || empty($amount) || !is_numeric($amount) || $amount < 0) {
        die("Invalid input. Please provide valid month/year and budget amount.");
    }

    // Insert into budgets table
    $sql = "INSERT INTO budgets (user_id, month_year, amount) VALUES ('$userId', '$monthYear', '$amount')";
    if ($conn->query($sql) === TRUE) {
        echo "Budget saved successfully. <a href='index.php'>Go back</a>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>