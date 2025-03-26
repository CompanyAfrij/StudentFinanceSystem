<?php
session_start();
include '../includes/config.php'; // Includes database connection & constants

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname']; // Match input name="fullname"
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password
    $role = $_POST['role']; // Get selected role

    // Insert user into the database
    $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $fullname, $email, $password, $role);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['role'] = $role;

        // Redirect based on role
        if ($role == 'admin') {
            header("Location: " . BASE_URL . "admin/admin-dashboard.php");
        } else {
            header("Location: " . BASE_URL . "pages/courses.php");
        }
        exit();
    } else {
        echo "Registration failed. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
