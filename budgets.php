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


$db = Database::getInstance();
$budgets = $db->getUserBudgets($_SESSION['user_id']);

include 'header.php';
?>

<div style="width: 60%;" class="container mt-5">
    <h2 class="text-center">Your Budgets</h2>

    <form id="deleteForm" action="/delete_budgets.php" method="POST" onsubmit="return confirmDeletion();">
        <div class="list-group">
            <?php if (count($budgets) > 0): ?>
                <?php foreach ($budgets as $budget): 
                    $percentage = ($budget['budget_amount'] > 0) 
                        ? min(100, ($budget['spent_amount'] / $budget['budget_amount']) * 100) 
                        : 0;

                    $progressClass = $percentage < 60 ? "bg-success" : ($percentage < 100 ? "bg-warning" : "bg-danger");
                ?>
                    <br>
                    <div class="list-group-item border border-1 border-dark rounded-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h5><strong><?= htmlspecialchars($budget['category_name']) ?></strong> - <?= date('F Y', strtotime($budget['month'])) ?></h5>
                            <p>Budget: <strong><?= number_format($budget['budget_amount'], 2) ?></strong> | Spent: <strong><?= number_format($budget['spent_amount'], 2) ?></strong></p>
                            <div class="progress mt-3" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated <?= $progressClass ?>" 
                                    role="progressbar" 
                                    style="width: <?= $percentage ?>%;" 
                                    aria-valuenow="<?= $percentage ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    <?= round($percentage, 1) ?>%
                                </div>
                            </div>
                        </div>
                        <div>
                            <input type="checkbox" class="delete-checkbox" name="budget_ids[]" value="<?= $budget['id'] ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div style="width:22%; margin:auto;" class="row justify-content-center">
                    <button type="submit" class="btn btn-danger w-100">Delete Selected</button>
                </div>

            <?php else: ?>
                <strong class="text-center">No budgets set up yet.</strong>
                <p class="text-center">If you want to create a budget, go to the main page and you'll find a form there.</p>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
function confirmDeletion() {
    const selectedCheckboxes = document.querySelectorAll(".delete-checkbox:checked");
    if (selectedCheckboxes.length === 0) {
        alert("Please select at least one budget to delete.");
        return false;
    }
    return confirm("Are you sure you want to delete the selected budgets?");
}
</script>

<?php include 'footer.php'; ?>