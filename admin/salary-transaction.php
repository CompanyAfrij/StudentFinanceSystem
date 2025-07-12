<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../pages/login.php");
    exit();
}

// Check if connection is established
if (!isset($conn) || $conn === null) {
    die("Database connection failed.");
}

// Handle transaction submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_name'], $_POST['position'], $_POST['bank_name'], $_POST['bank_account_number'], $_POST['phone_number'], $_POST['amount'], $_POST['transaction_date'])) {
    $employee_name = $_POST['employee_name'];
    $position = $_POST['position'];
    $bank_name = $_POST['bank_name'];
    $bank_account_number = $_POST['bank_account_number'];
    $phone_number = $_POST['phone_number'];
    $amount = floatval($_POST['amount']);
    $transaction_date = $_POST['transaction_date'];

    // Start transaction to ensure atomicity
    mysqli_begin_transaction($conn);

    try {
        // Insert into salary_transactions
        $stmt = $conn->prepare("INSERT INTO salary_transactions (employee_name, position, bank_name, bank_account_number, phone_number, amount, transaction_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssd", $employee_name, $position, $bank_name, $bank_account_number, $phone_number, $amount, $transaction_date);
        $stmt->execute();
        $stmt->close();

        // Deduct amount from total balance in enrollments
        $updateStmt = $conn->prepare("UPDATE enrollments SET paid_amount = paid_amount - ? WHERE paid_amount >= ?");
        $updateStmt->bind_param("dd", $amount, $amount);
        $updateStmt->execute();
        $affectedRows = $updateStmt->affected_rows;

        if ($affectedRows == 0) {
            throw new Exception("Insufficient balance in enrollments to process the transaction.");
        }

        $updateStmt->close();
        mysqli_commit($conn);

        echo "<script>alert('Transaction processed successfully!'); window.location.href='salary-transaction.php';</script>";
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='salary-transaction.php';</script>";
        exit();
    }
}

// Fetch transactions
$transactionsResult = mysqli_query($conn, "SELECT * FROM salary_transactions ORDER BY transaction_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Transactions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            background-color: #800000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #a00000;
        }
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .transactions-table th, .transactions-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .transactions-table th {
            background-color: #800000;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Salary Transactions</h1>
        
        <h2>Add New Transaction</h2>
        <form method="post">
            <div class="form-group">
                <label for="employee_name">Employee Name:</label>
                <input type="text" name="employee_name" id="employee_name" required>
            </div>
            <div class="form-group">
                <label for="position">Position:</label>
                <select name="position" id="position" required>
                    <option value="lecturer">Lecturer</option>
                    <option value="non_administration_staff">Administration Staff</option>
                    <option value="maintenance_staff">Maintenance Staff</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bank_name">Bank Name:</label>
                <input type="text" name="bank_name" id="bank_name" required>
            </div>
            <div class="form-group">
                <label for="bank_account_number">Bank Account Number:</label>
                <input type="text" name="bank_account_number" id="bank_account_number" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" name="phone_number" id="phone_number" pattern="[0-9]{10}" placeholder="Enter 10-digit number" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount (LKR):</label>
                <input type="number" name="amount" id="amount" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label for="transaction_date">Transaction Date:</label>
                <input type="date" name="transaction_date" id="transaction_date" required>
            </div>
            <div class="form-group">
                <button type="submit">Process Transaction</button>
            </div>
        </form>

        <h2>Transaction History</h2>
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Position</th>
                    <th>Bank Name</th>
                    <th>Bank Account</th>
                    <th>Phone Number</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $transactionsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['position']); ?></td>
                        <td><?php echo htmlspecialchars($row['bank_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['bank_account_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td>LKR <?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo $row['transaction_date']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>