<?php
// public/index.php
include 'header.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: hostpage.php');
    exit();
}

$query = "
    SELECT 
        b.id, 
        b.category_id, 
        c.name AS category_name, 
        b.month, 
        b.amount AS budget_amount,
        COALESCE(SUM(t.amount), 0) AS spent_amount
    FROM budgets b
    LEFT JOIN transactions t 
        ON b.category_id = t.category_id 
        AND DATE_TRUNC('month', t.transaction_date) = DATE_TRUNC('month', b.month::DATE)
        AND t.user_id = b.user_id
    JOIN categories c ON b.category_id = c.id
    WHERE b.user_id = ?
    GROUP BY b.id, c.name, b.amount";
    
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="width: 60%;" class="container mt-5">
    <h2 class="text-center">Your Budgets</h2>
    
    
    

    <form id="deleteForm" action="delete_budgets.php" method="POST" onsubmit="return confirmDeletion();">
        <div class="list-group">
            <?php if (count($budgets) > 0): ?>
                <?php foreach ($budgets as $budget): 
                    $percentage = ($budget['budget_amount'] > 0) 
                        ? min(100, ($budget['spent_amount'] / $budget['budget_amount']) * 100 * -1) 
                        : 0;

                    if ($percentage < 60) {
                        $progressClass = "bg-success";
                    } elseif ($percentage < 100) {
                        $progressClass = "bg-warning";
                    } else {
                        $progressClass = "bg-danger";
                    }
                ?>
                    <br>
                    <div class="list-group-item border border-1 border-dark rounded-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h5><strong><?= htmlspecialchars($budget['category_name']) ?></strong> - <?= date('F Y', strtotime($budget['month'])) ?></h5>
                            <p>Budget: <strong><?= number_format($budget['budget_amount'], 2) ?></strong> | Spent: <strong><?= number_format($budget['spent_amount'], 2) ?></strong></p>
                            <div class="progress mt-3" style="height: 25px;">
                                <div class="progress-bar <?= $progressClass ?>" 
                                    role="progressbar" 
                                    style="width: <?= $percentage ?>%;" 
                                    aria-valuenow="<?= $percentage ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    <?= round($percentage, 1) ?>%
                                </div>
                            </div>
                        </div>
                        <input type="checkbox" class="delete-checkbox d-none" name="budget_ids[]" value="<?= $budget['id'] ?>">
                    </div>
                <?php endforeach; ?>
                <div style="width:30%; margin:auto;" class="row justify-content-center">
                    <button type="submit" id="confirmDelete" class="btn btn-outline-danger d-none mt-3">Confirm Delete</button>
                </div>
                <br>
                <div style="width:25%; margin:auto;" class="row justify-content-center">
                    <button id="toggleDeleteMode" class="btn btn-danger mb-3">Delete</button>
                </div>
            <?php else: ?>
                <strong class="text-center">No budgets set up yet.</strong>
                <p class="text-center">If you want to create budget, go right down on main page and you'll find a form here.</p>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleDeleteBtn = document.getElementById("toggleDeleteMode");
    const confirmDeleteBtn = document.getElementById("confirmDelete");
    const checkboxes = document.querySelectorAll(".delete-checkbox");

    toggleDeleteBtn.addEventListener("click", function () {
        checkboxes.forEach(checkbox => checkbox.classList.toggle("d-none"));
        confirmDeleteBtn.classList.toggle("d-none");
    });
});

function confirmDeletion() {
    const selectedCheckboxes = document.querySelectorAll(".delete-checkbox:checked");
    if (selectedCheckboxes.length === 0) {
        alert("Please select at least one budget to delete.");
        return false;
    }
    return confirm("Are you sure you want to delete the selected budgets?");
}
</script>

<?php
include 'footer.php';
?>