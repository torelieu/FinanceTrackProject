<?php
// public/account.php
include 'header.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: loginpage.php');
    exit;
}
?>

<h2>Account Details</h2>
<p>Manage your account here.</p>

<?php
include 'footer.php';
?>