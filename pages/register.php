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
            background: radial-gradient(ellipse at bottom, #800000 0%, #2c003e 100%);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
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

        .register-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.1);
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

<div class="background-blobs">
    <div class="blob"></div>
    <div class="blob"></div>
    <div class="blob"></div>
</div>

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