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

<!-- HTML PART -->
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
            background: radial-gradient(ellipse at bottom, #800000 0%, #2c003e 100%);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .background-blobs {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: floatBlobs 20s infinite ease-in-out alternate;
        }

        .blob:nth-child(1) {
            width: 300px;
            height: 300px;
            background: #ff6b6b;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .blob:nth-child(2) {
            width: 400px;
            height: 400px;
            background: #ffb347;
            top: 30%;
            right: 15%;
            animation-delay: 3s;
        }

        .blob:nth-child(3) {
            width: 250px;
            height: 250px;
            background: #6a5acd;
            bottom: 15%;
            left: 20%;
            animation-delay: 6s;
        }

        @keyframes floatBlobs {
            0% { transform: translateY(0px) translateX(0px); }
            100% { transform: translateY(-50px) translateX(50px); }
        }

        .login-container {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 40px;
            width: 360px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #fff;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #fff;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #fff;
            color: #800000;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-btn:hover {
            background-color: #ffd2d2;
        }

        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #fff;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ff4d4d;
            font-size: 14px;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="background-blobs">
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
    </div>

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
            <button type="submit" class="login-btn">Login</button>
            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
        </form>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>