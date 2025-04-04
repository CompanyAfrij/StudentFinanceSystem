<?php
session_start();
include '../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(50)); // Secure token
        $query = "INSERT INTO password_resets (email, token) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();

        // Send email with reset link
        $resetLink = "http://localhost/FinanceManagementSystem/reset-password.php?token=" . $token;
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n\n" . $resetLink;
        $headers = "From: no-reply@yourwebsite.com";

        mail($email, $subject, $message, $headers);
        $_SESSION['success'] = "Password reset link sent to your email.";
    } else {
        $_SESSION['error'] = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <?php if (isset($_SESSION['success'])) { echo "<p style='color:green'>" . $_SESSION['success'] . "</p>"; unset($_SESSION['success']); } ?>
    <?php if (isset($_SESSION['error'])) { echo "<p style='color:red'>" . $_SESSION['error'] . "</p>"; unset($_SESSION['error']); } ?>
    <form action="forgot-password.php" method="POST">
        <label for="email">Enter Your Email</label>
        <input type="email" name="email" required>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
