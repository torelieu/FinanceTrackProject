<?php
session_start();
// public/index.php
include 'header.php';
?>

<h2>Welcome to FinanceTrack</h2>
<p>Here you can track your finances!</p>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="assets/chart.js"></script>
<script>
    // Placeholder script for chart.js
</script>

<?php
include 'footer.php';
?>