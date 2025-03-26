<?php
// Start the session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define site-wide constants
define('BASE_URL', 'http://localhost/FinanceManagementSystem/'); // Adjust as needed
define('SITE_NAME', 'FinanceManagementSystem');

// Database connection constants
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root'); 
define('DB_PASS', ''); 
define('DB_NAME', 'finance_management'); 

// Establish database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect users who are not logged in (useful for protected pages)
function check_login() {
    if (!isset($_SESSION['role'])) {
        header("Location: " . BASE_URL . "pages/login.php");
        exit();
    }
}
?>
