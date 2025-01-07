<?php
// public/index.php
include 'header.php';
require_once 'db.php';
?>

<h2>Welcome to FinanceTrack</h2>
<p>Here you can track your finances!</p>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="container mt-5">
        <h1 class="text-center">Monthly movement of finances</h1>
        

        <div>
            <label for="monthInput">Vyberte měsíc:</label>
            <input type="month" id="monthInput" value="">
        </div>


        <canvas id="financeChart" style="max-height: 400px;"></canvas>
        
        <h2 class="mt-5">Add income or outcome</h2>
        <form action="add_transactions.php" method="POST" class="mt-3">
            <div class="mb-3">
                <label for="amount" class="form-label">Value</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="mb-3">
                <label for="transaction_date" class="form-label">Date of transaction</label>
                <input type="date" class="form-control" id="transaction_date" name="transaction_date" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="" selected>Without category</option>
                    <!-- Dynamické načtení kategorií -->
                    <?php
                    require_once 'db.php';
                    $stmt = $pdo->query("SELECT id, name FROM categories");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Make transaction</button>
        </form>
    </div>

    <script>
    const monthInput = document.getElementById('monthInput'); // Input pro výběr měsíce
    // Nastavení aktuálního měsíce při načtení stránky
    const currentMonth = new Date().toISOString().slice(0, 7); // Formát YYYY-MM
    monthInput.value = currentMonth;

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
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: `Zůstatek (${month})`,
                        data: cumulativeAmounts, // Kumulativní součet
                        borderColor: 'rgba(75, 192, 192, 1)',
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