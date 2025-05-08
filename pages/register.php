<?php
include '../includes/header.php';
include '../includes/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IES Campus</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            background-image: url('../assets/images/ies.webp');
            background-size: cover;
            background-position: center;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .register-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
            background: rgba(128, 0, 0, 0.7); /* Blured card color */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #fff;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: #000;
            font-size: 15px;
        }

        input:focus, select:focus {
            outline: none;
            background-color: #fff;
        }

        .btn {
            width: 100%;
            background-color: #fff;
            color: #800000;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: #ffd2d2;
        }

        .login-text {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #fff;
        }

        .login-text a {
            color: #00ffff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Create Your Account</h2>
    <form action="register-process.php" method="POST">
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Create Password</label>
            <input type="password" id="password" name="password" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="role">Select Role</label>
            <select id="role" name="role" required>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="btn">Register</button>

        <p class="login-text">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>
    </form>
</div>

</body>
</html>