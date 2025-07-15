<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Zkontroluj, jestli je uživatel přihlášen
if (!isset($_SESSION['user_id'])) {
    header('Location: /hostpage.php');
    exit();
}

require_once 'db.php';


$db = Database::getInstance();
$userId = $_SESSION['user_id'];

if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-warning text-center'>{$_SESSION['message']}</div>";
    unset($_SESSION['message']);
}

// Získání posledních transakcí
//$transactions = $db->getLatestTransactions($userId);

// Získání hodnot filtrů z GET parametrů (default: latest)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'latest';
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;

// Načteme filtrované transakce (limit 10)
$transactions = $db->getFilteredTransactions($userId, $filter, 10, $selectedCategory);

// Získání kategorií pro select inputy
$categories = $db->getCategories($userId);

$budgets = $db->getUserBudgets($userId);
$exceededBudgets = [];

foreach ($budgets as $budget) {
    if ($budget['spent_amount'] > $budget['budget_amount'] ) {
        $exceededBudgets[] = $budget;
    }
}

include 'header.php';
?>

<!-- Upozornění na překročený budget -->
<?php if (!empty($exceededBudgets)): ?>
    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
        <strong>Warning!</strong> You have exceeded your budget in the following categories:
        <ul class="mt-2 mb-0">
            <?php foreach ($exceededBudgets as $budget): ?>
                <li><strong><?= htmlspecialchars($budget['category_name']) ?></strong> (Budget: <?= number_format($budget['budget_amount'], 2) ?> Czk, Spent: <?= number_format($budget['spent_amount'], 2) ?> Czk)</li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Welcome to FinanceTrack</h1>
    <p class="text-center">Here you can track your finances!</p>

    <!-- Výběr měsíce -->
    <div class="text-center mb-4">
        <label for="monthInput" class="me-2"><strong>Choose month:</strong></label>
        <input type="month" id="monthInput" value="">
    </div>

    <!-- Řádek pro grafy -->
    <div class="row">
        <!-- Vývoj zůstatku -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card p-3 border-success shadow-sm">
                <h5 class="text-center"><strong>Graph of your Balance</strong></h5>
                <canvas id="financeChart" style="max-height: 350px;"></canvas>
            </div>
        </div>

        <!-- Výdaje podle kategorií -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card p-3 border-primary shadow-sm">
                <h5 class="text-center"><strong>Expenses by Category</strong></h5>
                <canvas id="categoryChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Příjmy vs. Výdaje -->
    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4 mx-auto">
            <div class="card p-3 border-danger shadow-sm">
                <h5 class="text-center"><strong>Income vs Expenses</strong></h5>
                <canvas id="incomeExpenseChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <br>
    <br>
    <br>

    <!-- Nadpis sekce, tlačítko filtru a tlačítko smazání -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Latest Transactions</h3>
    <div class="d-flex gap-2">
        <!-- Tlačítko pro mazání -->
        <button id="toggleDeleteMode" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-trash-alt me-1"></i> Delete
        </button>
        
        <!-- Tlačítko filtru s dropdown menu -->
        <div class="dropdown">
            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm" style="min-width: 250px;">
                <form id="filterForm" method="GET" action="/indexmain.php">
                    <div class="mb-2">
                        <label for="filterSelect" class="form-label small">Filter by:</label>
                        <select class="form-select form-select-sm" name="filter" id="filterSelect">
                            <option value="latest" <?= $filter=='latest' ? 'selected' : '' ?>>Latest Transactions</option>
                            <option value="amount" <?= $filter=='amount' ? 'selected' : '' ?>>Sort by Amount</option>
                            <option value="date" <?= $filter=='date' ? 'selected' : '' ?>>Sort by Date</option>
                            <option value="category" <?= $filter=='category' ? 'selected' : '' ?>>Filter by Category</option>
                        </select>
                    </div>
                    <div class="mb-2 d-none" id="categoryFilterDiv">
                        <label for="categoryFilterSelect" class="form-label small">Category:</label>
                        <select class="form-select form-select-sm" name="category" id="categoryFilterSelect">
                            <option value="" disabled <?= $selectedCategory ? '' : 'selected' ?>>Choose category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>" <?= ($selectedCategory == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-check me-1"></i> Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabulka transakcí -->
<div class="card shadow-sm rounded-4">
    <div class="card-body p-4">
        <form id="deleteForm" action="/delete_transactions.php" method="POST" onsubmit="return confirmTransactionDeletion();">
            <table class="table table-hover mt-3">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th class="delete-checkbox-header d-none">Select</th>
                    </tr>
                </thead>
                <tbody id="transactionTableBody">
                    <?php foreach ($transactions as $i => $row): 
                        $hiddenClass = ($i >= 3) ? 'd-none' : ''; ?>
                        <tr class="<?= $hiddenClass ?>">
                            <td><?= htmlspecialchars($row['transaction_date']) ?></td>
                            <td><?= $row['category_name'] ?? '<span class="text-muted">Income</span>' ?></td>
                            <td><?= number_format($row['amount'], 2) ?> Czk</td>
                            <td class="delete-checkbox-cell d-none">
                                <input type="checkbox" class="delete-checkbox" name="transaction_ids[]" value="<?= $row['id'] ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Confirm Delete Button -->
            <div class="text-center mt-3">
                <button type="submit" id="confirmDelete" class="btn btn-outline-danger btn-sm d-none">Confirm Delete</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <button id="showMoreBtn" class="btn btn-primary-outline btn-sm d-none">Show More</button>
        </div>
    </div>
</div>
</div>

<div class="row mt-5">
    <!-- Formulář pro příjem -->
    <div class="col-lg-5 col-md-6 mx-auto">
        <div class="card p-4 bg-light border rounded-4 shadow-sm">
            <h4 class="text-center">Add Income</h4>
            <form action="/add_transactions.php" method="POST">
                <input type="hidden" name="transaction_type" value="income">
                <div class="mb-3">
                    <label for="income_amount" class="form-label">Amount</label>
                    <input type="number" step="1" class="form-control" id="income_amount" name="amount" min="1" required>
                </div>
                <div class="mb-3">
                    <label for="income_date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="income_date" name="transaction_date" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Add Income</button>
            </form>
        </div>
    </div>

    <!-- Formulář pro výdaj -->
    <div class="col-lg-5 col-md-6 mx-auto">
        <div class="card p-4 bg-light border rounded-4 shadow-sm">
            <h4 class="text-center">Add Expense</h4>
            <form action="/add_transactions.php" method="POST">
                <input type="hidden" name="transaction_type" value="expense">
                <div class="mb-3">
                    <label for="expense_amount" class="form-label">Amount</label>
                    <!-- Tip, který se zobrazí při focusu -->
                    <div id="expenseTip" class="form-text text-muted d-none">
                    <p class="mt-1">Fill in positive amount, it automatically transfers to negative amount.</p>
                    </div>
                    <input type="number" step="0.01" class="form-control" id="expense_amount" name="amount" min="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="expense_date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="expense_date" name="transaction_date" required>
                </div>
                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="" selected disabled>Choose category</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger w-100">Add Expense</button>
            </form>
        </div>
    </div>
</div>



<!-- Formulář pro budgety -->
<div class="row mt-5">
    <div class="col-lg-6 mx-auto">
        <div class="card p-4 bg-light border rounded-4 shadow-sm">
            <h4 class="text-center">Create Budget</h4>
            <form action="/add_budget.php" method="POST">
                <div class="mb-3">
                    <label for="budget_category_id" class="form-label">Select Category</label>
                    <select class="form-select" id="budget_category_id" name="category_id" required>
                        <option value="" selected disabled>Choose a category</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="month" class="form-label">Select Month</label>
                    <input type="month" class="form-control" id="month" name="month" required>
                </div>
                <div class="mb-3">
                    <label for="budget_amount" class="form-label">Set Budget Amount</label>
                    <input type="number" step="1" class="form-control" id="budget_amount" name="amount" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Create Budget</button>
            </form>
        </div>
    </div>
</div>
</div>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const expenseAmountInput = document.getElementById("expense_amount");
    const expenseTip = document.getElementById("expenseTip");

    expenseAmountInput.addEventListener("focus", function() {
        expenseTip.classList.remove("d-none");
    });

    expenseAmountInput.addEventListener("blur", function() {
        expenseTip.classList.add("d-none");
    });
});
</script>

<!-- JavaScript for Show More and Delete Mode -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Správa filtru: zobrazení/skrytí selectu pro kategorii
    const filterSelect = document.getElementById('filterSelect');
    const categoryFilterDiv = document.getElementById('categoryFilterDiv');
    filterSelect.addEventListener('change', function() {
        if (filterSelect.value === 'category') {
            categoryFilterDiv.classList.remove('d-none');
        } else {
            categoryFilterDiv.classList.add('d-none');
        }
    });

    // Show More / Show Less
    const showMoreBtn = document.getElementById('showMoreBtn');
    const transactionRows = document.querySelectorAll('#transactionTableBody tr.d-none');
    let showingAll = false;
    if (transactionRows.length > 0) {
        showMoreBtn.classList.remove("d-none");
    }
    showMoreBtn.addEventListener('click', () => {
        transactionRows.forEach(row => row.classList.toggle('d-none'));
        showMoreBtn.textContent = showingAll ? 'Show More' : 'Show Less';
        showingAll = !showingAll;
    });

    // Delete Mode
    const toggleDeleteBtn = document.getElementById("toggleDeleteMode");
    const confirmDeleteBtn = document.getElementById("confirmDelete");
    const deleteCheckboxHeaders = document.querySelectorAll(".delete-checkbox-header");
    const deleteCheckboxCells = document.querySelectorAll(".delete-checkbox-cell");

    let deleteMode = false;
    toggleDeleteBtn.addEventListener("click", function () {
        deleteMode = !deleteMode;
        deleteCheckboxHeaders.forEach(header => header.classList.toggle("d-none", !deleteMode));
        deleteCheckboxCells.forEach(cell => cell.classList.toggle("d-none", !deleteMode));
        confirmDeleteBtn.classList.toggle("d-none", !deleteMode);
        toggleDeleteBtn.textContent = deleteMode ? "Cancel" : "Delete";
    });
});

// Funkce pro potvrzení mazání
function confirmTransactionDeletion() {
    const selectedCheckboxes = document.querySelectorAll(".delete-checkbox:checked");
    if (selectedCheckboxes.length === 0) {
        alert("Please select at least one transaction to delete.");
        return false;
    }
    return confirm("Are you sure you want to delete the selected transactions?");
}
</script>

<script>
const monthInput = document.getElementById('monthInput');
const currentMonth = new Date().toISOString().slice(0, 7);
monthInput.value = currentMonth;

let balanceChart = null;
let categoryChart = null;
let incomeExpenseChart = null;

// 📌 Funkce pro načtení dat a vykreslení všech grafů
const fetchAndDrawCharts = (month) => {
    fetch(`get_chart_data.php?month=${month}`)
        .then(response => response.json())
        .then(data => {
            drawBalanceChart(data.balanceOverTime, month);
            drawCategoryChart(data.spendingByCategory);
            drawIncomeExpenseChart(data.incomeVsExpenses);
        })
        .catch(error => console.error('Error fetching chart data:', error));
};

// 📊 Vývoj zůstatku (původní graf)
const drawBalanceChart = (data, month) => {
    const labels = data.map(item => item.day);
    const amounts = data.map(item => parseFloat(item.total));

    // Vypočítáme kumulativní součet
    const cumulativeAmounts = amounts.reduce((acc, value, index) => {
        acc.push((acc[index - 1] || 0) + value);
        return acc;
    }, []);

    const ctx = document.getElementById('financeChart').getContext('2d');

    if (balanceChart) {
        balanceChart.destroy();
    }

    balanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: `Balance (${month})`,
                data: cumulativeAmounts,
                borderColor: 'rgb(1, 212, 18)',
                borderWidth: 2,
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
};

// 📊 Výdaje podle kategorií (koláčový graf)
const drawCategoryChart = (data) => {
    const labels = data.map(item => item.category_name);
    const amounts = data.map(item => parseFloat(item.total_spent));

    const ctx = document.getElementById('categoryChart').getContext('2d');

    if (categoryChart) {
        categoryChart.destroy();
    }

    categoryChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: amounts,
                backgroundColor: ['red', 'blue', 'green', 'yellow', 'purple'],
                borderWidth: 1
            }]
        }
    });
};

// 📊 Příjmy vs. Výdaje (sloupcový graf)
const drawIncomeExpenseChart = (data) => {
    const labels = ["Income", "Expenses"];
    const amounts = [parseFloat(data.total_income), parseFloat(data.total_expenses)];

    const ctx = document.getElementById('incomeExpenseChart').getContext('2d');

    if (incomeExpenseChart) {
        incomeExpenseChart.destroy();
    }

    incomeExpenseChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: amounts,
                backgroundColor: ['green', 'red'],
                borderWidth: 1
            }]
        }
    });
};

// 📌 Když se změní měsíc, znovu načíst data
monthInput.addEventListener('change', () => {
    const selectedMonth = monthInput.value;
    fetchAndDrawCharts(selectedMonth);
});

// 📌 Načíst všechny grafy při startu
document.addEventListener("DOMContentLoaded", function() {
    fetchAndDrawCharts(currentMonth);
});
</script>

<?php
include 'footer.php';
?>