<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ověříme, jestli je uživatel přihlášen
if (!isset($_SESSION['user_id'])) {
    header('Location: /hostpage.php');
    exit();
}

// Teď už můžeme načítat další věci (HTML, komponenty, DB, atd.)
require_once 'db.php';
include 'header.php';

$isGoogleLogin = isset($_SESSION['isGoogle']) ? true : false;

// Získání instance databáze
$db = Database::getInstance();

// Získání uživatelských údajů
$user = $db->getUserById($_SESSION['user_id']);

if (!$user) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit();
}
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Account Details</h1>
    <div style="width: 60%;" class="container mt-5">
        <div class="card p-4 border border-1 border-dark rounded-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-3"><strong>Name:</strong></p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-3"><?php echo htmlspecialchars($user['username']); ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-3"><strong>Email:</strong></p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-3"><strong>Account Created:</strong></p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-3"><?php echo htmlspecialchars($user['created_at']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$isGoogleLogin) { ?>
        <br>
        <div style="width:35%; margin:auto; text-align:center;" class="container border border-1 border- p-5 my-5 bg-light rounded-4">
            <div class="row justify-content-center">
                <h2>Edit Account Details</h2>
                <form action="/update_account.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    <?php } ?>
</div>

<?php include 'footer.php'; ?>