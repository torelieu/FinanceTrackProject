<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /hostpage.php');
    exit();
}

require_once 'db.php';

if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-warning text-center'>{$_SESSION['message']}</div>";
    unset($_SESSION['message']);
}

$isGoogleLogin = isset($_SESSION['isGoogle']) ? true : false;

$db = Database::getInstance();
$user = $db->getUserById($_SESSION['user_id']);

if (!$user) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit();
}

include 'header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Account Details</h1>

    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card p-4 border border-dark rounded-4">
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Name:</strong>
                    </div>
                    <div class="col-6 text-end">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Email:</strong>
                    </div>
                    <div class="col-6 text-end">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Account Created:</strong>
                    </div>
                    <div class="col-6 text-end">
                        <?php echo htmlspecialchars($user['created_at']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$isGoogleLogin) { ?>
        <div class="row justify-content-center mt-5">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card p-4 bg-light border rounded-4">
                    <h2 class="text-center mb-4">Edit Account Details</h2>
                    <form action="/update_account.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php include 'footer.php'; ?>