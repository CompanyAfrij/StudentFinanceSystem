<?php
session_start();
include '../includes/database.php'; // Ensure database connection

// Debugging session issues
if (!isset($_SESSION['role'])) {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    exit("Session not set. Check your login system.");
}

// Ensure only admin access
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #800000;
            --primary-hover: #a00;
            --secondary-color: #2c3e50;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: var(--secondary-color);
            color: white;
            padding: 20px 0;
            box-shadow: var(--shadow);
            position: fixed;
            height: 100%;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: #b3b3b3;
            font-size: 0.9rem;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #b3b3b3;
            text-decoration: none;
            transition: var(--transition);
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .header h1 {
            color: var(--secondary-color);
            font-size: 2rem;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .logout-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }

        .logout-btn:hover {
            background: var(--primary-hover);
        }

        .logout-btn i {
            margin-right: 5px;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
        }

        .card-header i {
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .card-body {
            padding: 20px;
        }

        .card-body p {
            color: #666;
            margin-bottom: 20px;
        }

        .card-btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            transition: var(--transition);
            width: 100%;
            text-align: center;
        }

        .card-btn:hover {
            background: var(--primary-hover);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .cards-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Dashboard</h3>
                <p>Welcome back, Admin</p>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage-courses.php" class="nav-link">
                        <i class="fas fa-book"></i>
                        Manage Courses
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage-students.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        Manage Students
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage-settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Dashboard Overview</h1>
                <button class="logout-btn" onclick="window.location.href='../pages/logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>

            <div class="cards-container">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book"></i>
                        <h3>Manage Courses</h3>
                    </div>
                    <div class="card-body">
                        <p>Add, modify, or delete courses in the system. Manage course content, schedules, and instructors.</p>
                        <a href="manage-courses.php" class="card-btn">Go to Courses</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users"></i>
                        <h3>Manage Students</h3>
                    </div>
                    <div class="card-body">
                        <p>View and manage student records, enrollments, and academic progress.</p>
                        <a href="manage-students.php" class="card-btn">Go to Students</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-cog"></i>
                        <h3>System Settings</h3>
                    </div>
                    <div class="card-body">
                        <p>Configure system preferences, user permissions, and application settings.</p>
                        <a href="manage-settings.php" class="card-btn">Go to Settings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>