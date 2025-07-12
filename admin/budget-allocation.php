<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle new budget submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['department'], $_POST['fiscal_year'], $_POST['allocated_amount'])) {
    $department = $_POST['department'];
    $fiscal_year = $_POST['fiscal_year'];
    $allocated_amount = $_POST['allocated_amount'];

    $stmt = $conn->prepare("INSERT INTO budgets (department, fiscal_year, allocated_amount) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssd", $department, $fiscal_year, $allocated_amount);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Budget added successfully!'); window.location.href='budget-allocation.php';</script>";
}

// Handle expense submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['budget_id'], $_POST['description'], $_POST['amount'], $_POST['expense_date'])) {
    $budget_id = $_POST['budget_id'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $expense_date = $_POST['expense_date'];

    $stmt = $conn->prepare("INSERT INTO expenses (budget_id, description, amount, expense_date) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("isds", $budget_id, $description, $amount, $expense_date);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE budgets SET spent_amount = spent_amount + ? WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("di", $amount, $budget_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Expense added successfully!'); window.location.href='budget-allocation.php';</script>";
}

// Calculate total revenue from enrollments
$revenueResult = mysqli_query($conn, "SELECT SUM(paid_amount) AS total_revenue FROM enrollments WHERE budget_id IS NOT NULL");
if ($revenueResult === false) {
    die("Query failed: " . $conn->error);
}
$totalRevenue = mysqli_fetch_assoc($revenueResult)['total_revenue'] ?? 0;

// Fetch budgets for dropdowns and display
$budgets = $conn->query("SELECT * FROM budgets");
if ($budgets === false) {
    die("Query failed: " . $conn->error);
}

// Fetch students (adjust 'email' to your actual column name, e.g., 'user_name' or 'full_name')
$students = $conn->query("SELECT id, email AS username FROM users WHERE role = 'student'");
if ($students === false) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Allocation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #800000;
            --primary-hover: #a00000;
            --accent-color: #ffc107;
            --light: #fff;
            --gray-light: #f5f5f5;
            --dark-bg: #121212;
            --dark-text: #e0e0e0;
            --shadow: 0 4px 8px rgba(0,0,0,0.1);
            --transition: all 0.3s ease-in-out;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: var(--gray-light);
            color: #333;
            transition: var(--transition);
        }

        body.dark {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }

        .dashboard { display: flex; min-height: 100vh; }

        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            padding-top: 30px;
            position: fixed;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transform: translateX(-100%);
            transition: var(--transition);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header { text-align: center; margin-bottom: 30px; }
        .nav-menu { list-style: none; padding: 0 20px; }
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin: 8px 0;
            color: #f1f1f1;
            text-decoration: none;
            border-radius: 4px;
        }
        .nav-link i { margin-right: 10px; }
        .nav-link:hover, .nav-link.active { background-color: rgba(255, 255, 255, 0.2); }

        .main-content {
            margin-left: 0;
            padding: 40px;
            flex: 1;
            transition: var(--transition);
        }

        .main-content.sidebar-active {
            margin-left: 250px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .burger-menu {
            font-size: 24px;
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            padding: 10px;
        }

        body.dark .burger-menu {
            color: var(--dark-text);
        }

        .dark-mode-toggle {
            background: var(--accent-color);
            color: black;
            padding: 10px;
            border: none;
            width: calc(100% - 40px);
            margin: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
        }

        .form-section, .table-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        body.dark .form-section, body.dark .table-section {
            background-color: #1e1e1e;
            color: var(--dark-text);
        }

        .form-section h2, .table-section h2 {
            margin-top: 0;
        }

        .form-section form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-section label {
            font-weight: bold;
        }

        .form-section input, .form-section select, .form-section textarea {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-section button {
            background-color: var(--primary-color);
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-section button:hover {
            background-color: var(--primary-hover);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        body.dark table, body.dark th, body.dark td {
            border-color: #555;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div>
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
                <p>Welcome, Admin</p>
            </div>
            <ul class="nav-menu">
                <li><a href="admin-dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="manage-students.php" class="nav-link"><i class="fas fa-users"></i> Manage Students</a></li>
                <li><a href="manage-courses.php" class="nav-link"><i class="fas fa-book"></i> Manage Courses</a></li>
                <li><a href="budget-allocation.php" class="nav-link active"><i class="fas fa-money-bill"></i> Budget Allocation</a></li>
                <li><a href="messages.php" class="nav-link"><i class="fas fa-envelope"></i> Messages</a></li>
            </ul>
        </div>
        <div>
            <button class="dark-mode-toggle" onclick="toggleDarkMode()"><i class="fas fa-moon"></i> Dark Mode</button>
            <button class="logout-btn" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="header">
            <button class="burger-menu" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <h1>Budget Allocation</h1>
        </div>

        <!-- Budget Form -->
        <div class="form-section">
            <h2>Add New Budget</h2>
            <form method="post">
                <label>Department:</label>
                <select name="department" required>
                    <option value="IT Infrastructure">IT Infrastructure</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Student Services">Student Services</option>
                    <option value="Facilities">Facilities</option>
                    <option value="Research">Research</option>
                </select>
                <label>Fiscal Year:</label>
                <input type="text" name="fiscal_year" placeholder="YYYY-YYYY" required>
                <label>Allocated Amount (LKR):</label>
                <input type="number" step="0.01" name="allocated_amount" required>
                <button type="submit">Add Budget</button>
            </form>
        </div>

        <!-- Expense Form -->
        <div class="form-section">
            <h2>Log Expense</h2>
            <form method="post">
                <label>Budget:</label>
                <select name="budget_id" required>
                    <?php while ($budget = $budgets->fetch_assoc()) {
                        echo "<option value='{$budget['id']}'>{$budget['department']} ({$budget['fiscal_year']}) - LKR " . number_format($budget['allocated_amount'], 2) . "</option>";
                    } ?>
                </select>
                <label>Description:</label>
                <input type="text" name="description" required>
                <label>Amount (LKR):</label>
                <input type="number" step="0.01" name="amount" required>
                <label>Date:</label>
                <input type="date" name="expense_date" required>
                <button type="submit">Log Expense</button>
            </form>
        </div>

        <!-- Revenue Overview -->
        <div class="form-section">
            <h2>Income Overview</h2>
            <p>Total income from Fees: LKR <?php echo number_format($totalRevenue, 2); ?></p>
        </div>

        <!-- Budgets Table -->
        <div class="table-section">
            <h2>Existing Budgets</h2>
            <table>
                <tr>
                    <th>Department</th>
                    <th>Fiscal Year</th>
                    <th>Allocated Amount</th>
                    <th>Spent Amount</th>
                    <th>Remaining</th>
                </tr>
                <?php
                $budgets->data_seek(0); // Reset pointer
                while ($budget = $budgets->fetch_assoc()) {
                    $remaining = $budget['allocated_amount'] - $budget['spent_amount'];
                    echo "<tr>
                        <td>" . htmlspecialchars($budget['department']) . "</td>
                        <td>" . htmlspecialchars($budget['fiscal_year']) . "</td>
                        <td>LKR " . number_format($budget['allocated_amount'], 2) . "</td>
                        <td>LKR " . number_format($budget['spent_amount'], 2) . "</td>
                        <td>LKR " . number_format($remaining, 2) . "</td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>

<script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark');
        localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
    }

    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = '../pages/logout.php';
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('sidebar-active');
    }

    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark');
    }
</script>

</body>
</html>