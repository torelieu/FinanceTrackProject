<?php
// public/index.php
include 'header.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: hostpage.php');
    exit();
}

if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-warning text-center'>{$_SESSION['message']}</div>";
    unset($_SESSION['message']);
}
?>

<br>
<h1 class="text-center">Welcome to FinanceTrack</h1>
<p class="text-center">Here you can track your finances!</p>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<br>
<div class="container mt-5">
        <h2 class="text-center">GRAPGH OF YOUR BALANCE</h2>
        

        <div>
            <label for="monthInput" class="mt-3"><strong>Choose month:</strong></label>
            <input type="month" id="monthInput" value="">
        </div>

        <div style="width: 85%; margin:auto;" class="graph border border-1 border-success p-5 mt-3 rounded-4 ">
            <canvas id="financeChart" style="max-height: 400px;"></canvas>
        </div>

        <br>
        
        <!-- Display the last transactions -->
<div style="width: 55%; margin:auto;" class="mt-5">
    <div class="card rounded-4">
        <div class="card-header text-center">
            <h3 class="m-0">Latest Transactions</h3>
        </div>
        <div class="card-body p-4">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col"><i class="fas fa-calendar-alt"></i> Date</th>
                        <th scope="col"><i class="fas fa-tags"></i> Category</th>
                        <th scope="col"><i class="fas fa-coins"></i> Amount</th>
                    </tr>
                </thead>
                <tbody id="transactionTableBody">
                    <?php
                    // Fetch the last 10 transactions for the current user
                    $stmt = $pdo->prepare("
                        SELECT t.transaction_date, c.name AS category_name, t.amount 
                        FROM transactions t
                        LEFT JOIN categories c ON t.category_id = c.id
                        WHERE t.user_id = :user_id
                        ORDER BY t.created_at DESC
                        LIMIT 10
                    ");
                    $stmt->execute(['user_id' => $_SESSION['user_id']]);
                    ?>

                    <?php
                    // Fetch rows and categorize them for default
                    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = 0; $i < count($transactions); $i++) {
                        $row = $transactions[$i];
                        $hiddenClass = ($i >= 1) ? 'd-none' : '';
                        echo "<tr class='$hiddenClass'>
                                <td>{$row['transaction_date']}</td>
                                <td>" . ($row['category_name'] ?? '<span class=\"text-muted\">income</span>') . "</td>
                                <td>" . number_format($row['amount'], 2) . " Czk</td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Show More / Show Less Button -->
            <div class="text-center mt-3">
                <button id="showMoreBtn" class="btn btn-primary-outline btn-sm">Show More</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Add JavaScript to Handle Show More/Show Less -->
<script>
    const showMoreBtn = document.getElementById('showMoreBtn');
    const transactionRows = document.querySelectorAll('#transactionTableBody tr.d-none');
    let showingAll = false;

    showMoreBtn.addEventListener('click', () => {
        if (!showingAll) {
            // Show hidden rows
            transactionRows.forEach(row => row.classList.remove('d-none'));
            showMoreBtn.textContent = 'Show Less';
        } else {
            // Hide rows after the first 5
            transactionRows.forEach(row => row.classList.add('d-none'));
            showMoreBtn.textContent = 'Show More';
        }
        showingAll = !showingAll;
    });
</script>
        
        
<br>
<div style="width: 50%; margin:auto; text-align:center;" class="mt-5 container p-5 bg-light border border-1 border-dark rounded-4 ">
    <div class="row justify-content-center">
        <h2>ADD INCOME OR OUTCOME</h2>
        <form style="width: 80%;" action="add_transactions.php" method="POST" class="mt-3">
            <div class="mb-3">
                <label for="amount" class="form-label">Value</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="mb-3">
                <label for="transaction_date" class="form-label">Date of transaction</label>
                <input type="date" class="form-control" id="transaction_date" name="transaction_date" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label"> Category</label>
                <select class="form-select" id="category_id" name="category_id">
                <option value="" selected>Without category</option>
                <!-- Dynamické načtení kategorií -->
                <?php
                require_once 'db.php';
                $stmt = $pdo->query("SELECT id, name FROM categories where user_id = {$_SESSION['user_id']}");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success mt-2">Make transaction</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const amountInput = document.getElementById("amount");
    const categorySelect = document.getElementById("category_id");
    const form = document.querySelector("form");

    form.addEventListener("submit", function (event) {
        const amount = parseFloat(amountInput.value);
        const category = categorySelect.value;

        if (category && amount > 0) {
            alert("You cannot set a positive amount for an outcome transaction!");
            event.preventDefault();
        }
    });
});
</script>

<div style="width: 50%; margin:auto; text-align:center;" class="mt-5 container border border-1 border-dark p-5 bg-light rounded-4">
    <h2>Create Budget</h2>
    <form style="width: 80%; margin:auto;" action="add_budget.php" method="POST">
        <div class="mb-3">
            <label for="category_id" class="form-label">Select Category</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <option value="" selected disabled>Choose a category</option>
                <?php
                require_once 'db.php';
                $stmt = $pdo->query("SELECT id, name FROM categories WHERE user_id = {$_SESSION['user_id']}");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="month" class="form-label">Select Month</label>
            <input type="month" class="form-control" id="month" name="month" required>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Set Budget Amount</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Budget</button>
    </form>
</div>

<script>
const monthInput = document.getElementById('monthInput'); // Input pro výběr měsíce
// Nastavení aktuálního měsíce při načtení stránky
const currentMonth = new Date().toISOString().slice(0, 7); // Formát YYYY-MM
monthInput.value = currentMonth;

let chartInstance = null; // Proměnná pro ukládání instance grafu

// Funkce pro získání a vykreslení dat
const fetchAndDrawChart = (month) => {
    fetch(`get_transactions.php?month=${month}`)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.day); // Dny
            const amounts = data.map(item => parseFloat(item.total)); // Hodnoty
            const cumulativeAmounts = amounts.reduce((acc, value, index) => {
                acc.push((acc[index - 1] || 0) + value); // Kumulativní součet
                return acc;
            }, []);

            const ctx = document.getElementById('financeChart').getContext('2d');
            
            // Zničení existujícího grafu, pokud existuje
            if (chartInstance) {
                chartInstance.destroy();
            }

            // Vytvoření nového grafu
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: `Balance (${month})`,
                        data: cumulativeAmounts, // Kumulativní součet
                        borderColor: 'rgb(1, 212, 18)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1,
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Chyba při načítání dat:', error));
};

// Načtení grafu při změně měsíce
monthInput.addEventListener('change', () => {
    const selectedMonth = monthInput.value;
    fetchAndDrawChart(selectedMonth);
});

// Načtení aktuálního měsíce při načtení stránky
fetchAndDrawChart(currentMonth);
</script>

<?php
include 'footer.php';
?>