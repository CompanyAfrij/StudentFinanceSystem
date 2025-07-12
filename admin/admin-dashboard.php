<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

$courseCount = $studentCount = $adminCount = $totalBalance = 0;

$courseResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses");
if ($courseResult) $courseCount = mysqli_fetch_assoc($courseResult)['total'];

$studentResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'student'");
if ($studentResult) $studentCount = mysqli_fetch_assoc($studentResult)['total'];

$adminResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'");
if ($adminResult) $adminCount = mysqli_fetch_assoc($adminResult)['total'];

$balanceResult = mysqli_query($conn, "SELECT SUM(paid_amount) AS total_balance FROM enrollments");
if ($balanceResult) $totalBalance = mysqli_fetch_assoc($balanceResult)['total_balance'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'], $_POST['message_id'])) {
    $reply = $_POST['reply'];
    $message_id = $_POST['message_id'];

    $stmt = $conn->prepare("UPDATE messages SET reply = ?, replied_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $reply, $message_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Reply sent successfully!'); window.location.href='admin-dashboard.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #800000;
            --primary-hover: #a00000;
            --accent-color: #ffc107;
            --light: #fff;
            --gray-light: #f5f5f5;
            --dark-bg: #121212;
            --dark-text: #e0e0e0;
            --shadow: 0 4px 8px rgba(0,0,0,0.1);
            --transition: all 0.3s ease-in-out;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: var(--gray-light);
            color: #333;
            transition: var(--transition);
        }

        body.dark {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }

        .dashboard { display: flex; min-height: 100vh; }

        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            padding-top: 30px;
            position: fixed;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transform: translateX(-100%);
            transition: var(--transition);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header { text-align: center; margin-bottom: 30px; }
        .nav-menu { list-style: none; padding: 0 20px; }
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin: 8px 0;
            color: #f1f1f1;
            text-decoration: none;
            border-radius: 4px;
        }
        .nav-link i { margin-right: 10px; }
        .nav-link:hover, .nav-link.active { background-color: rgba(255, 255, 255, 0.2); }

        .main-content {
            margin-left: 0;
            padding: 40px;
            flex: 1;
            transition: var(--transition);
        }

        .main-content.sidebar-active {
            margin-left: 250px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .burger-menu {
            font-size: 24px;
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            padding: 10px;
        }

        body.dark .burger-menu {
            color: var(--dark-text);
        }

        .dark-mode-toggle {
            background: var(--accent-color);
            color: black;
            padding: 10px;
            border: none;
            width: calc(100% - 40px);
            margin: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 20px;
            transition: var(--transition);
        }

        body.dark .card {
            background-color: #1e1e1e;
            color: var(--dark-text);
        }

        .card-header {
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .card-header i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .card-body p {
            margin-bottom: 10px;
        }

        .card-btn {
            background-color: var(--accent-color);
            color: #000;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 10px;
        }

        .logout-btn {
            background: #ff4b2b;
            color: white;
            border: none;
            padding: 12px 20px;
            margin: 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            width: calc(100% - 40px);
        }

        .messages-section {
            margin-top: 50px;
        }

        .message-box {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .message-box textarea {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
        }

        .message-box button {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        body.dark .message-box {
            background-color: #1e1e1e;
            border-color: #555;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div>
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
                <p>Welcome, Admin</p>
            </div>
            <ul class="nav-menu">
                <li><a href="#" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="manage-students.php" class="nav-link"><i class="fas fa-users"></i> Manage Students</a></li>
                <li><a href="manage-courses.php" class="nav-link"><i class="fas fa-book"></i> Manage Courses</a></li>
                <li><a href="budget-allocation.php" class="nav-link"><i class="fas fa-money-bill"></i> Budget Allocation</a></li>
                <li><a href="messages.php" class="nav-link"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="salary-transaction.php" class="nav-link"><i class="fas fa-money-check-alt"></i> Salary Transactions</a></li>
            </ul>
        </div>
        <div>
            <button class="dark-mode-toggle" onclick="toggleDarkMode()"><i class="fas fa-moon"></i> Dark Mode</button>
            <button class="logout-btn" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="header">
            <button class="burger-menu" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <h1 id="greeting">Dashboard Overview</h1>
        </div>

        <div class="cards-container">
            <div class="card">
                <div class="card-header"><i class="fas fa-user-graduate"></i> Total Students</div>
                <div class="card-body">
                    <p><?php echo $studentCount; ?> students registered</p>
                    <a href="manage-students.php" class="card-btn">View Students</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-book-open"></i> Total Courses</div>
                <div class="card-body">
                    <p><?php echo $courseCount; ?> courses available</p>
                    <a href="manage-courses.php" class="card-btn">Manage Courses</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-user-shield"></i> Total Admins</div>
                <div class="card-body">
                    <p><?php echo $adminCount; ?> admins managing</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-wallet"></i> Total Balance</div>
                <div class="card-body">
                    <p>LKR <?php echo number_format($totalBalance, 2); ?> credited</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-envelope"></i> Messages</div>
                <div class="card-body">
                    <p>View and respond to student queries</p>
                    <a href="messages.php" class="card-btn">Go to Messages</a>
                </div>
            </div>

            <div class="card">
                 <div class="card-header"><i class="fas fa-money-check-alt"></i> Salary Transactions</div>
                 <div class="card-body">
                     <p>Manage employee salary transactions</p>
                     <a href="salary-transaction.php" class="card-btn">View Transactions</a>
                </div>
            </div>


            <div class="card">
                <div class="card-header"><i class="fas fa-money-bill"></i> Budget Allocation</div>
                <div class="card-body">
                    <p>Manage budget allocations</p>
                    <a href="budget-allocation.php" class="card-btn">Go to Budget Allocation</a>
                </div>
            </div>
        </div>

        <!-- Student Messages Section -->
        <div class="messages-section">
            <h2>Student Messages</h2>
            <?php
            $result = $conn->query("SELECT * FROM messages ORDER BY sent_at DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<div class='message-box'>";
                echo "<strong>Email:</strong> " . htmlspecialchars($row['student_email']) . "<br>";
                echo "<strong>Message:</strong> " . nl2br(htmlspecialchars($row['message'])) . "<br>";
                echo "<strong>Reply:</strong> " . (isset($row['reply']) && $row['reply'] ? nl2br(htmlspecialchars($row['reply'])) : "<em>No reply yet</em>") . "<br>";

                if (!isset($row['reply']) || !$row['reply']) {
                    echo "<form method='post'>
                            <input type='hidden' name='message_id' value='" . $row['id'] . "'>
                            <textarea name='reply' placeholder='Write a reply...' required></textarea>
                            <button type='submit'>Send Reply</button>
                          </form>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>
</div>

<script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark');
        localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
    }

    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = '../pages/logout.php';
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('sidebar-active');
    }

    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark');
    }

    const hour = new Date().getHours();
    let greetingText = "Dashboard Overview";
    if (hour < 12) greetingText = "Good Morning, Admin!";
    else if (hour < 18) greetingText = "Good Afternoon, Admin!";
    else greetingText = "Good Evening, Admin!";
    document.getElementById("greeting").textContent = greetingText;
</script>

</body>
</html>