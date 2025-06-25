<?php
include 'head.html';
?>

<style>
    /* Nastavení celkové stránky */
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background: linear-gradient(to right, #2c3e50, #4ca1af);
        color: white;
    }

    /* Kontejner hlavní sekce */
    .hero-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        text-align: center;
        padding: 20px;
    }

    .hero-section h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .hero-section p {
        font-size: 1.2rem;
        font-weight: 300;
        max-width: 700px;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .cta-buttons {
        display: flex;
        gap: 15px;
    }

    .btn-custom {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        transition: 0.3s ease-in-out;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background-color: #f39c12;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background-color: #e67e22;
        transform: scale(1.05);
    }

    .btn-outline {
        background: transparent;
        border: 2px solid white;
        color: white;
    }

    .btn-outline:hover {
        background: white;
        color: #2c3e50;
        transform: scale(1.05);
    }

    /* Sekce funkcí */
    .features-section {
        background: white;
        color: #2c3e50;
        padding: 80px 20px;
        text-align: center;
    }

    .features-section h2 {
        font-size: 2.5rem;
        margin-bottom: 20px;
    }

    .features-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
        max-width: 1100px;
        margin: auto;
    }

    .feature-card {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 10px;
        width: 300px;
        text-align: center;
        box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .feature-icon {
        font-size: 40px;
        color: #f39c12;
        margin-bottom: 15px;
    }

    /* Sekce Footer */
    .footer {
        background: #222;
        color: #fff;
        text-align: center;
        padding: 20px;
    }

    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2.5rem;
        }

        .features-container {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<!-- Hlavní úvodní sekce -->
<section class="hero-section">
    <h1>Take Control of Your Finances</h1>
    <p>Track your income, expenses, and budgets effortlessly. Get real-time insights into your spending habits and achieve financial freedom with FinanceTrack.</p>
    
    <div class="cta-buttons">
        <a href="/registerpage.php" class="btn-custom btn-primary">Get Started</a>
        <a href="/loginpage.php" class="btn-custom btn-outline">Login</a>
    </div>
</section>

<!-- Sekce s funkcemi -->
<section class="features-section">
    <h2>Why Choose FinanceTrack?</h2>

    <div class="features-container">
        <div class="feature-card">
            <i class="fas fa-chart-line feature-icon"></i>
            <h4>Real-Time Insights</h4>
            <p>Monitor your financial health with interactive charts and reports.</p>
        </div>

        <div class="feature-card">
            <i class="fas fa-wallet feature-icon"></i>
            <h4>Budget Planning</h4>
            <p>Set and track budgets to manage your money more effectively.</p>
        </div>

        <div class="feature-card">
            <i class="fas fa-money-check-alt feature-icon"></i>
            <h4>Expense Tracking</h4>
            <p>Categorize and analyze your spending to stay on top of your finances.</p>
        </div>

        <div class="feature-card">
            <i class="fas fa-lock feature-icon"></i>
            <h4>Secure & Private</h4>
            <p>Your financial data is encrypted and securely stored.</p>
        </div>
    </div>
</section>

<?php
include 'footer.php';
?>
