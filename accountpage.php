<?php
// public/account.php

include 'header.php';
include 'db.php';

$is_google_user = isset($_SESSION['google_user']) === true;

if ($is_google_user) {
    echo "<h2>Welcome, " . htmlspecialchars($_SESSION['user_name']) . "!</h2>";
    echo "<p>Email: " . htmlspecialchars($_SESSION['user_email']) . "</p>";
    echo "<p>We cant provide more info, because you are not logged in within our database.</p>";
    exit();
}
else{
    $stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
    
?>

<div class="container mt-5">
    <h1>Account Details</h1>

    <?php if(!$is_google_user): ?>
        <!-- Informace pro uživatele přihlášeného přes vlastní systém -->
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Account Created:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>

        <!-- Formulář pro úpravu údajů -->
        <h2>Edit Account Details</h2>
        <form action="update_account.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    <?php endif; ?>
</div>

<?php
include 'footer.php';
?>