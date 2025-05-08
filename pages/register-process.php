<?php
session_start();
include '../includes/config.php'; // Includes database connection & constants

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname']; // Match input name="fullname"
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password
    $role = $_POST['role']; // Get selected role

    // Step 1: Insert user into the database without user_id field
    $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $fullname, $email, $password, $role);

    if ($stmt->execute()) {
        $inserted_id = $stmt->insert_id; // Auto-increment ID from DB
        $_SESSION['user_id'] = $inserted_id;
        $_SESSION['role'] = $role;

        // Step 2: Generate custom user ID
        if ($role == 'student') {
            $custom_id = 'IES' . str_pad($inserted_id, 3, '0', STR_PAD_LEFT);
        } elseif ($role == 'admin') {
            $custom_id = 'ADM' . str_pad($inserted_id, 3, '0', STR_PAD_LEFT);
        } else {
            $custom_id = null;
        }

        // Step 3: Update user with the custom ID if applicable
        if ($custom_id) {
            $update_query = "UPDATE users SET user_custom_id = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $custom_id, $inserted_id);
            $update_stmt->execute();
            $update_stmt->close();
        }

        // Step 4: Redirect based on role
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
