<?php

require_once __DIR__ . '/vendor/autoload.php'; // Zajisti správné načtení Composeru

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASS');

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

        try {
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection error. Check logs.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function loginUser($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                header('Location: /indexmain.php');
                exit();
            } else {
                echo "<div class='alert alert-danger'>Invalid email or password.</div>";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            echo "<div class='alert alert-danger'>Login error. Check logs.</div>";
        }
    }

    public function registerUser($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);
            echo "<div class='alert alert-success'>Registration successful!</div>";
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }

    public function findUserByEmail($email) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertGoogleUser($email, $username, $passwordHash) {
        $stmt = $this->pdo->prepare('INSERT INTO users (email, username, password_hash) VALUES (:email, :username, :password_hash)');
        $stmt->execute([
            'email' => $email,
            'username' => $username,
            'password_hash' => $passwordHash
        ]);
    }

    public function getUserById($userId) {
        $stmt = $this->pdo->prepare("SELECT username, email, created_at FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($userId, $categoryName) {
        try {
            // Zkontroluj, zda kategorie již existuje
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categories WHERE user_id = :user_id AND name = :name");
            $stmt->execute([
                ':user_id' => $userId,
                ':name' => $categoryName
            ]);
            if ($stmt->fetchColumn() > 0) {
                return "Category already exists!";
            }
    
            // Přidání nové kategorie
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO categories (user_id, name) VALUES (:user_id, :name)");
            $stmt->execute([
                ':user_id' => $userId,
                ':name' => $categoryName
            ]);
            $this->pdo->commit();
            return "Category added successfully!";
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return "Error: " . $e->getMessage();
        }
    }

    public function updateUser($userId, $name) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET username = :name WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':id' => $userId
            ]);
            return "Údaje byly úspěšně aktualizovány!";
        } catch (Exception $e) {
            return "Došlo k chybě při aktualizaci údajů: " . $e->getMessage();
        }
    }

    public function getTransactions($userId, $month) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    TO_CHAR(transaction_date, 'YYYY-MM-DD') AS day,
                    SUM(amount) AS total
                FROM transactions
                WHERE user_id = :user_id AND TO_CHAR(transaction_date, 'YYYY-MM') = :month
                GROUP BY day
                ORDER BY day
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':month' => $month
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function addTransaction($userId, $amount, $transactionDate, $categoryId = null, $forceCategory = null) {
        try {
            $this->pdo->beginTransaction();
    
            // Pokud je příjem, nastavíme kategorii jako "Income"
            if ($forceCategory === "Income") {
                $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE user_id = :user_id AND name = 'Income' LIMIT 1");
                $stmt->execute([':user_id' => $userId]);
                $incomeCategory = $stmt->fetch(PDO::FETCH_ASSOC);
                $categoryId = $incomeCategory ? $incomeCategory['id'] : null;
            }
    
            // Uložit transakci
            $stmt = $this->pdo->prepare("
                INSERT INTO transactions (user_id, amount, transaction_date, category_id)
                VALUES (:user_id, :amount, :transaction_date, :category_id)
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':amount' => $amount,
                ':transaction_date' => $transactionDate,
                ':category_id' => $categoryId
            ]);
    
            // Aktualizovat zůstatek
            $stmt = $this->pdo->prepare("
                UPDATE balances
                SET balance = balance + :amount
                WHERE user_id = :user_id
            ");
            $stmt->execute([
                ':amount' => $amount,
                ':user_id' => $userId
            ]);
    
            $this->pdo->commit();
            return "Transaction added successfully!";
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return "Error: " . $e->getMessage();
        }
    }

    public function deleteTransactions($userId, $transactionIds) {
        try {
            $placeholders = implode(',', array_fill(0, count($transactionIds), '?'));
            $stmt = $this->pdo->prepare("DELETE FROM transactions WHERE id IN ($placeholders) AND user_id = ?");
            $stmt->execute([...$transactionIds, $userId]);

            return "Selected transactions deleted successfully!";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function addBudget($userId, $categoryId, $month, $amount) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM budgets WHERE user_id = ? AND category_id = ? AND month = ?");
            $stmt->execute([$userId, $categoryId, $month]);
            if ($stmt->fetchColumn() > 0) {
                return "Budget already exists for this category and month!";
            }

            if ($amount <= 0) {
                return "You cannot set a zero or negative amount for a budget!";
            }

            $stmt = $this->pdo->prepare("INSERT INTO budgets (user_id, category_id, month, amount) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$userId, $categoryId, $month, $amount])) {
                return "Budget successfully created!";
            } else {
                return "Failed to create budget.";
            }
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    public function getCategories($userId) {
        $stmt = $this->pdo->prepare("SELECT id, name FROM categories WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserBudgets($userId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                b.id, 
                b.category_id, 
                c.name AS category_name, 
                b.month, 
                b.amount AS budget_amount,
                COALESCE(SUM(t.amount) * -1, 0) AS spent_amount
            FROM budgets b
            LEFT JOIN transactions t 
                ON b.category_id = t.category_id 
                AND DATE_TRUNC('month', t.transaction_date) = DATE_TRUNC('month', b.month::DATE)
                AND t.user_id = b.user_id
            JOIN categories c ON b.category_id = c.id
            WHERE b.user_id = :user_id
            GROUP BY b.id, c.name, b.amount
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteBudgets($userId, $budgetIds) {
        if (empty($budgetIds)) {
            return "No budgets selected for deletion.";
        }
    
        $placeholders = implode(',', array_fill(0, count($budgetIds), '?'));
        $stmt = $this->pdo->prepare("DELETE FROM budgets WHERE id IN ($placeholders) AND user_id = ?");
        $stmt->execute([...$budgetIds, $userId]);
    
        return "Selected budgets have been deleted!";
    }

    public function getSpendingByCategory($userId, $month) {
    $stmt = $this->pdo->prepare("
        SELECT 
            c.name AS category_name, 
            ABS(SUM(t.amount)) AS total_spent
        FROM transactions t
        JOIN categories c ON t.category_id = c.id
        WHERE t.user_id = :user_id 
        AND t.amount < 0
        AND TO_CHAR(t.transaction_date, 'YYYY-MM') = :month
        GROUP BY c.name
    ");
    $stmt->execute([':user_id' => $userId, ':month' => $month]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getIncomeVsExpenses($userId, $month) {
    $stmt = $this->pdo->prepare("
        SELECT 
            SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) AS total_income,
            SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) AS total_expenses
        FROM transactions
        WHERE user_id = :user_id 
        AND TO_CHAR(transaction_date, 'YYYY-MM') = :month
    ");
    $stmt->execute([':user_id' => $userId, ':month' => $month]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getLatestTransactions($userId, $limit = 10) {
    $stmt = $this->pdo->prepare("
        SELECT t.id, t.transaction_date, c.name AS category_name, t.amount 
        FROM transactions t
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.user_id = :user_id
        ORDER BY t.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getFilteredTransactions($userId, $filter = 'latest', $limit = 10, $category = null) {
    $whereCategory = "";
    $params = [':user_id' => $userId];

    if ($filter === 'category' && !empty($category)) {
        $whereCategory = " AND t.category_id = :category";
        $params[':category'] = $category;
        // V tomto případě řadíme defaultně podle vytvoření
        $order = "ORDER BY t.created_at DESC";
    } elseif ($filter === 'amount') {
        $order = "ORDER BY ABS(t.amount) DESC";
    } elseif ($filter === 'date') {
        $order = "ORDER BY t.transaction_date DESC";
    } else {
        // Default: latest transactions
        $order = "ORDER BY t.created_at DESC";
    }

    $query = "
        SELECT t.id, t.transaction_date, c.name AS category_name, t.amount 
        FROM transactions t
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.user_id = :user_id
        $whereCategory
        $order
        LIMIT :limit
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    if ($filter === 'category' && !empty($category)) {
        $stmt->bindValue(':category', $category, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>