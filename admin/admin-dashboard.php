<?php
session_start();
include '../includes/database.php'; // Ensure database connection

// Ensure only admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            width: 300px;
            margin: 15px;
        }
        h2 {
            color: #333;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #800000;
            color: white;
            border-radius: 5px;
            margin-top: 10px;
        }
        .btn:hover {
            background-color:rgb(167, 12, 12);
        }
        .logout-btn {
            background-color: #800000;
        }
        .logout-btn:hover {
            background-color:rgb(192, 25, 25);
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Welcome, Admin!</h2>
        
        <div class="card">
            <h3>Manage Courses</h3>
            <p>Add, modify, or delete courses</p>
            <a href="manage-courses.php" class="btn">Go to Manage Courses</a>
        </div>

        <div class="card">
            <h3>Manage Students</h3>
            <p>View and manage student records</p>
            <a href="manage-students.php" class="btn">Go to Manage Students</a>
        </div>

        <div class="card">
            <h3>Settings</h3>
            <p>Manage system settings</p>
            <a href="manage-settings.php" class="btn">Go to Settings</a>
        </div>

        <br>
        <a href="../pages/logout.php" class="btn logout-btn">Logout</a>
    </div>

</body>
</html>
