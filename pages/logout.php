<?php
session_start();
include '../includes/config.php'; // Include database connection

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $logout_time = date("Y-m-d H:i:s");

    // Insert logout record into the database
    $query = "INSERT INTO user_logouts (user_id, logout_time) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $logout_time);
    $stmt->execute();
    $stmt->close();
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to home page
header("Location: " . BASE_URL . "index.php");
exit();
?>
