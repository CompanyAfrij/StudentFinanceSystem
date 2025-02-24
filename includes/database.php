<?php
$host = "localhost";  // XAMPP default: localhost
$user = "root";       // XAMPP default: root
$password = "";       // XAMPP default: (empty)
$database = "finance_management"; // Replace with your DB name

$conn = new mysqli($host, $user, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
