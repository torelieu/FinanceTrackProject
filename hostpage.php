<?php
include 'db.php';
include 'head.html';
?>
<style>

    * {
        overflow-y: hidden;
    }

    body {
        background: linear-gradient(to right, #2c3e50, #4ca1af);
        color: white;
        text-align: center;
    }
    .container {
        margin-top: 10%;
        background: rgba(255, 255, 255, 0.1);
        padding: 30px;
        border-radius: 10px;
    }
    h1 {
        font-weight: bold;
    }
    .btn-custom {
        background-color: #f39c12;
        border: none;
        padding: 10px 20px;
        font-size: 18px;
        color: white;
        border-radius: 5px;
        transition: 0.3s;
    }
    .btn-custom:hover {
        background-color: #e67e22;
    }
</style>

<div class="container">
        <h1>Welcome to FinanceTrack</h1>
        <p class="lead">Your personal finance management tool</p>

        <hr class="my-4">

        <h3>What is FinanceTrack?</h3>
        <p>FinanceTrack helps you track your income, expenses, and budgets in one place. Analyze your financial habits and plan your future.</p>

        <h3>What can you do?</h3>
        <ul class="list-unstyled">
            <li>✔ Add and categorize transactions</li>
            <li>✔ Set and monitor budgets</li>
            <li>✔ View reports with interactive charts</li>
            <li>✔ Secure login with email or Google</li>
        </ul>

        <h3>How to get started?</h3>
        <p>Click below to log in or sign up and start managing your finances effortlessly.</p>

        <a href="loginpage.php" class="btn btn-custom">Login / Register</a> 
    </div>


<?php
include 'footer.php';
?>