<?php 
session_start();
include '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../pages/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($student_name, $student_email);
$stmt->fetch();
$stmt->close();

$query = "
    SELECT e.id AS enrollment_id, c.course_name, c.duration, c.price AS total_fee, e.paid_amount, e.enrolled_at
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.student_id = ?
    ORDER BY e.enrolled_at DESC
";
$stmt2 = $conn->prepare($query);
$stmt2->bind_param("i", $student_id);
$stmt2->execute();
$result = $stmt2->get_result();

$enrollments = [];
while ($row = $result->fetch_assoc()) {
    $enrollments[] = $row;
}
$stmt2->close();

// Fetch messages AND replies for this student (using student_id for accuracy)
$messages_stmt = $conn->prepare("
    SELECT subject, message, reply, replied_at, sent_at 
    FROM messages 
    WHERE student_id = ? 
    ORDER BY replied_at DESC, sent_at DESC
");
$messages_stmt->bind_param("i", $student_id);
$messages_stmt->execute();
$messages_result = $messages_stmt->get_result();

$messages = [];
while ($row = $messages_result->fetch_assoc()) {
    $messages[] = $row;
}
$messages_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
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
            transition: var(--transition);
        }
        .nav-link i { margin-right: 10px; }
        .nav-link:hover, .nav-link.active { 
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
            flex: 1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
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
            transition: var(--transition);
        }

        .dark-mode-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
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
            transition: var(--transition);
        }

        .card-btn:hover {
            background-color: #ffab00;
            transform: translateY(-2px);
        }

        .logout-btn {
            background: #ff4b2b;
            color: white;
            border: none;
            padding: 12px 20px;
            margin: 20px;
            width: calc(100% - 40px);
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: #ff5e3a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .profile-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-right: 20px;
        }

        .welcome-message {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .email {
            color: #777;
        }

        body.dark .email {
            color: #aaa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        body.dark table {
            background-color: #1e1e1e;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        body.dark th, body.dark td {
            border-bottom: 1px solid #444;
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        body.dark th {
            background-color: #600000;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        body.dark tr:hover {
            background-color: #2a2a2a;
        }

        .paid {
            color: #44aa44;
            font-weight: bold;
        }

        .balance {
            color: #ff4444;
            font-weight: bold;
        }

        .installment-table {
            margin-top: 30px;
        }

        .installment-table form button {
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .installment-table form button:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .no-courses {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        body.dark .no-courses {
            background-color: #1e1e1e;
        }

        .message-item {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
        }

        body.dark .message-item {
            background-color: #1e1e1e;
            border-color: #444;
        }

        .message-item h4 {
            color: maroon;
            margin-top: 0;
        }

        .message-item hr {
            margin: 10px 0;
            border: 0;
            border-top: 1px solid #eee;
        }

        body.dark .message-item hr {
            border-top-color: #444;
        }

        .reply-text {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            white-space: pre-line;
        }

        body.dark .reply-text {
            background: #2a2a2a;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <div class="sidebar-header">
                <h3>Student Portal</h3>
                <p>Welcome, <?= htmlspecialchars($student_name) ?></p>
            </div>
            <ul class="nav-menu">
                <li><a href="#" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="courses.php" class="nav-link"><i class="fas fa-book"></i> Browse Courses</a></li>
                <li><a href="contact.php" class="nav-link"><i class="fas fa-envelope"></i> Contact Us</a></li>
            </ul>
        </div>
        <div>
            <button class="dark-mode-toggle" onclick="toggleDarkMode()"><i class="fas fa-moon"></i> Dark Mode</button>
            <button class="logout-btn" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1 id="greeting">Dashboard Overview</h1>
        </div>

        <div class="profile-info">
            <div class="profile-pic">
                <?= strtoupper(substr($student_name, 0, 1)) ?>
            </div>
            <div>
                <div class="welcome-message">Welcome back, <?= htmlspecialchars($student_name) ?>!</div>
                <div class="email"><?= htmlspecialchars($student_email) ?></div>
            </div>
        </div>

        <?php if (count($enrollments)): ?>
            <?php 
            $total_paid = 0;
            $total_balance = 0;
            foreach ($enrollments as $enroll) {
                $total_paid += $enroll['paid_amount'];
                $total_balance += ($enroll['total_fee'] - $enroll['paid_amount']);
            }
            ?>

            <div class="cards-container">
                <div class="card">
                    <div class="card-header"><i class="fas fa-book-open"></i> Enrolled Courses</div>
                    <div class="card-body">
                        <p><?= count($enrollments) ?> active courses</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><i class="fas fa-wallet"></i> Total Paid</div>
                    <div class="card-body">
                        <p>LKR <?= number_format($total_paid, 2) ?></p>
                        <p class="paid">Payment received</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><i class="fas fa-money-bill-wave"></i> Total Balance</div>
                    <div class="card-body">
                        <p>LKR <?= number_format($total_balance, 2) ?></p>
                        <p class="balance">Pending payment</p>
                    </div>
                </div>
            </div>

            <?php foreach ($enrollments as $enroll): 
                $balance = $enroll['total_fee'] - $enroll['paid_amount'];
            ?>
                <div class="card" style="margin-top: 30px;">
                    <div class="card-header"><i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($enroll['course_name']) ?></div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Total Fee (Rs)</th>
                                    <th>Paid Amount (Rs)</th>
                                    <th>Balance (Rs)</th>
                                    <th>Enrolled At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= number_format($enroll['total_fee'], 2) ?></td>
                                    <td class="paid"><?= number_format($enroll['paid_amount'], 2) ?></td>
                                    <td class="balance"><?= number_format($balance, 2) ?></td>
                                    <td><?= date('d M Y', strtotime($enroll['enrolled_at'])) ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <?php
                        $enrollment_id = $enroll['enrollment_id'];
                        $installments = $conn->prepare("SELECT id, installment_number, amount, due_date, paid FROM installments WHERE enrollment_id = ?");
                        $installments->bind_param("i", $enrollment_id);
                        $installments->execute();
                        $result = $installments->get_result();
                        ?>

                        <?php if ($result->num_rows > 0): ?>
                            <h4 style="margin-top: 20px;">Installments</h4>
                            <table class="installment-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['installment_number'] ?></td>
                                        <td>LKR <?= number_format($row['amount'], 2) ?></td>
                                        <td><?= date('d M Y', strtotime($row['due_date'])) ?></td>
                                        <td><?= $row['paid'] ? '<span class="paid">Paid</span>' : '<span class="balance">Unpaid</span>' ?></td>
                                        <td>
                                            <?php if (!$row['paid']): ?>
                                                <form action="payment.php" method="GET" style="margin: 0;" onsubmit="return confirmPayment();">
                                                    <input type="hidden" name="installment_id" value="<?= $row['id'] ?>">
                                                    <button type="submit">Pay Now</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card no-courses">
                <div class="card-body">
                    <p>You have not enrolled in any courses yet.</p>
                    <a href="courses.php" class="card-btn">Browse Available Courses</a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Admin Replies Section -->
        <?php if (count($messages) > 0): ?>
            <div class="card" style="margin-top: 40px;">
                <div class="card-header">
                    <i class="fas fa-envelope"></i> Your Messages & Admin Replies
                </div>
                <div class="card-body">
                    <div class="messages-container">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-item">
                                <h4><?= htmlspecialchars($msg['subject']) ?></h4>
                                <p><strong>Your Message:</strong></p>
                                <p style="white-space: pre-line;"><?= htmlspecialchars($msg['message']) ?></p>
                                <p style="font-size: 12px; color: #777;">
                                    Sent on: <?= date("M j, Y g:i A", strtotime($msg['sent_at'])) ?>
                                </p>
                                
                                <?php if (!empty($msg['reply'])): ?>
                                    <hr>
                                    <p><strong>Admin Reply:</strong></p>
                                    <div class="reply-text">
                                        <?= htmlspecialchars($msg['reply']) ?>
                                    </div>
                                    <p style="font-size: 12px; color: #777;">
                                        Replied on: <?= date("M j, Y g:i A", strtotime($msg['replied_at'])) ?>
                                    </p>
                                <?php else: ?>
                                    <p style="font-style: italic; color: #999;">No reply yet.</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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

    function confirmPayment() {
        return confirm("Are you sure you want to proceed with this payment?");
    }

    // Set theme on page load
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark');
    }

    // Greeting based on time
    const hour = new Date().getHours();
    let greetingText = "Dashboard Overview";
    if (hour < 12) greetingText = "Good Morning, <?= htmlspecialchars($student_name) ?>!";
    else if (hour < 18) greetingText = "Good Afternoon, <?= htmlspecialchars($student_name) ?>!";
    else greetingText = "Good Evening, <?= htmlspecialchars($student_name) ?>!";
    document.getElementById("greeting").textContent = greetingText;
</script>

</body>
</html>