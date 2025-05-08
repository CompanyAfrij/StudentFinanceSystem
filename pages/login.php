<?php 
session_start();
include '../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = trim(strtolower($user['role']));
            $_SESSION['email'] = $user['email'];

            if ($_SESSION['role'] == 'admin') {
                header("Location: ../admin/admin-dashboard.php");
                exit();
            } elseif ($_SESSION['role'] == 'student') {
                $student_id = $_SESSION['user_id'];
                $check_enrollment = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ?");
                $check_enrollment->bind_param("i", $student_id);
                $check_enrollment->execute();
                $enrollment_result = $check_enrollment->get_result();

                if ($enrollment_result->num_rows > 0) {
                    header("Location: /FinanceManagementSystem/pages/student-dashboard.php");
                } else {
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IES Campus</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            background: url('../assets/images/ies.webp') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: rgba(128, 0, 0, 0.7); /* Maroon (#800000) with 70% opacity */
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 40px;
            width: 360px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #fff;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #fff;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            background-color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        input:focus {
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background-color: #fff;
            color: #800000;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .login-btn:hover {
            background-color: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            color: #ffcccc;
            font-size: 14px;
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background-color: rgba(255, 0, 0, 0.2);
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to IES</h2>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="" required>
            </div>
            <button type="submit" class="login-btn">LOGIN</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>