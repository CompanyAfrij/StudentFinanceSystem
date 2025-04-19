<?php 
session_start();
include '../includes/database.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = trim(strtolower($user['role']));
            $_SESSION['email'] = $user['email'];

            // Redirect based on role
            if ($_SESSION['role'] == 'admin') {
                header("Location: ../admin/admin-dashboard.php");
                exit();
            } elseif ($_SESSION['role'] == 'student') {
                // Check if the student has already enrolled in a course
                $student_id = $_SESSION['user_id'];
                $check_enrollment = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ?");
                $check_enrollment->bind_param("i", $student_id);
                $check_enrollment->execute();
                $enrollment_result = $check_enrollment->get_result();

                if ($enrollment_result->num_rows > 0) {
                    // Already enrolled — go to student dashboard
                    header("Location: /FinanceManagementSystem/pages/student-dashboard.php");
                } else {
                    // Not enrolled yet — go to course selection
                    header("Location: /FinanceManagementSystem/pages/courses.php");
                }
                exit();
            } else {
                echo "Error: Role not recognized (" . htmlspecialchars($_SESSION['role']) . ")";
                exit();
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!-- HTML PART -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        h2 {
            margin-bottom: 20px;
            color: #800000;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .login-btn {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: white;
            background-color: #800000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            margin-bottom: 15px;
        }
        .login-btn:hover {
            background-color: #a00000;
        }
        .forgot-password {
            display: block;
            text-align: center;
            color: #800000;
            text-decoration: none;
            font-size: 14px;
            margin-top: 10px;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
        </form>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
