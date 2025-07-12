<?php 
session_start();
include '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../pages/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($student_name, $student_email);
$stmt->fetch();
$stmt->close();

$query = "
    SELECT e.id AS enrollment_id, c.course_name, c.duration, c.price AS total_fee, e.paid_amount, e.enrolled_at, 
           COALESCE(e.completion_percentage, 0) AS completion_percentage
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.student_id = ?
    ORDER BY e.enrolled_at DESC
";
$stmt2 = $conn->prepare($query);
if ($stmt2 === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt2->bind_param("i", $student_id);
$stmt2->execute();
$result = $stmt2->get_result();

$enrollments = [];
while ($row = $result->fetch_assoc()) {
    $enrollments[] = $row;
}
$stmt2->close();

// Fetch messages AND replies for this student
$messages_stmt = $conn->prepare("
    SELECT subject, message, reply, replied_at, sent_at 
    FROM messages 
    WHERE student_id = ? 
    ORDER BY replied_at DESC, sent_at DESC
");
if ($messages_stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$messages_stmt->bind_param("i", $student_id);
$messages_stmt->execute();
$messages_result = $messages_stmt->get_result();

$messages = [];
while ($row = $messages_result->fetch_assoc()) {
    $messages[] = $row;
}
$messages_stmt->close();

// Count unread messages
$unread_count = 0;
foreach ($messages as $msg) {
    if (empty($msg['replied_at']) || empty($msg['reply'])) {
        $unread_count++;
    }
}
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
            margin-bottom: 30px;
        }

        .burger-menu {
            font-size: 24px;
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            padding: 10px;
            display: block;
        }

        body.dark .burger-menu {
            color: var(--dark-text);
        }

        .notification-bell {
            position: relative;
            font-size: 20px;
            cursor: pointer;
        }

        .notification-bell .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
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
            width: calc(100% - 40px);
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
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

        .progress-bar {
            width: 100%;
            background-color: #ddd;
            border-radius: 5px;
            margin-top: 10px;
        }

        .progress {
            height: 20px;
            background-color: var(--accent-color);
            border-radius: 5px;
            text-align: center;
            color: white;
            line-height: 20px;
        }

        .calendar {
            width: 100%;
            margin-top: 20px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .calendar-nav {
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 18px;
            cursor: pointer;
        }

        body.dark .calendar-nav {
            color: var(--dark-text);
        }

        .calendar-nav:disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
        }

        .calendar-day {
            padding: 10px;
            border: 1px solid #ddd;
        }

        body.dark .calendar-day {
            border-color: #444;
        }

        .calendar-day-header {
            font-weight: bold;
            background-color: var(--primary-color);
            color: white;
        }

        body.dark .calendar-day-header {
            background-color: #600000;
        }

        .calendar-day.event {
            background-color: rgba(255, 99, 71, 0.3);
        }

        body.dark .calendar-day.event {
            background-color: rgba(255, 99, 71, 0.5);
        }

        .calendar-day.other-month {
            color: #999;
        }

        body.dark .calendar-day.other-month {
            color: #666;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div>
            <div class="sidebar-header">
                <h3>Student Dashboard</h3>
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
    <div class="main-content" id="main-content">
        <div class="header">
            <button class="burger-menu" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <h1 id="greeting">Dashboard Overview</h1>
            <div class="notification-bell" onclick="alert('Notifications: <?= $unread_count ?> unread messages')">
                <i class="fas fa-bell"></i>
                <?php if ($unread_count > 0): ?>
                    <span class="badge"><?= $unread_count ?></span>
                <?php endif; ?>
            </div>
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
                        if ($installments === false) {
                            die("Prepare failed: " . $conn->error);
                        }
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

        <!-- Academic Calendar -->
        <div class="card" style="margin-top: 40px;">
            <div class="card-header"><i class="fas fa-calendar-alt"></i> Academic Calendar</div>
            <div class="card-body">
                <ul>
                    <li>Exam Period: July 15-20, 2025</li>
                    <li>Fee Payment Deadline: July 10, 2025</li>
                    <li>Next Semester Start: August 01, 2025</li>
                </ul>
            </div>
        </div>

        <!-- Progress Tracker -->
        <?php if (count($enrollments)): ?>
            <div class="card" style="margin-top: 40px;">
                <div class="card-header"><i class="fas fa-chart-line"></i> Progress Tracker</div>
                <div class="card-body">
                    <?php foreach ($enrollments as $enroll): ?>
                        <p><?= htmlspecialchars($enroll['course_name']) ?> Progress:</p>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?= $enroll['completion_percentage'] ?>%;"><?= $enroll['completion_percentage'] ?>%</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Interactive Calendar -->
        <div class="card" style="margin-top: 40px;">
            <div class="card-header"><i class="fas fa-calendar"></i> Interactive Calendar</div>
            <div class="card-body">
                <div class="calendar" id="calendar"></div>
            </div>
        </div>

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
        console.log("Dark mode toggled");
    }

    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = '../pages/logout.php';
        }
    }

    function confirmPayment() {
        return confirm("Are you sure you want to proceed with this payment?");
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('sidebar-active');
        console.log("Sidebar toggled");
    }

    // Greeting based on time
    const hour = new Date().getHours();
    let greetingText = "Dashboard Overview";
    if (hour < 12) greetingText = "Good Morning, <?= htmlspecialchars($student_name) ?>!";
    else if (hour < 18) greetingText = "Good Afternoon, <?= htmlspecialchars($student_name) ?>!";
    else greetingText = "Good Evening, <?= htmlspecialchars($student_name) ?>!";
    document.getElementById("greeting").textContent = greetingText;
    console.log("Greeting set to: " + greetingText);

    // Calendar Logic
    const calendar = document.getElementById('calendar');
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    const events = [
        { date: new Date(2025, 6, 10), title: "Fee Payment Deadline" }, // July 10, 2025
        { date: new Date(2025, 6, 15), title: "Exam Period Start" },    // July 15, 2025
        { date: new Date(2025, 6, 20), title: "Exam Period End" },      // July 20, 2025
        { date: new Date(2025, 7, 1), title: "Next Semester Start" }    // August 01, 2025
    ];

    function renderCalendar(month, year) {
        calendar.innerHTML = '';
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = firstDay.getDay();

        const header = document.createElement('div');
        header.className = 'calendar-header';
        header.innerHTML = `
            <button class="calendar-nav" onclick="changeMonth(-1)" ${month === 0 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>
            <h3>${monthNames[month]} ${year}</h3>
            <button class="calendar-nav" onclick="changeMonth(1)" ${month === 11 ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>
        `;
        calendar.appendChild(header);

        const daysHeader = document.createElement('div');
        daysHeader.className = 'calendar-days';
        days.forEach(day => {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day calendar-day-header';
            dayElement.textContent = day;
            daysHeader.appendChild(dayElement);
        });
        calendar.appendChild(daysHeader);

        const daysContainer = document.createElement('div');
        daysContainer.className = 'calendar-days';
        let dayCount = 1;
        for (let i = 0; i < 6; i++) {
            for (let j = 0; j < 7; j++) {
                if (i === 0 && j < startingDay) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'calendar-day other-month';
                    daysContainer.appendChild(emptyDay);
                } else if (dayCount <= daysInMonth) {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';
                    const currentDate = new Date(year, month, dayCount);
                    if (events.some(event => event.date.toDateString() === currentDate.toDateString())) {
                        dayElement.className += ' event';
                        const event = events.find(event => event.date.toDateString() === currentDate.toDateString());
                        dayElement.title = event.title;
                    }
                    dayElement.textContent = dayCount;
                    daysContainer.appendChild(dayElement);
                    dayCount++;
                }
            }
            if (dayCount > daysInMonth) break;
        }
        calendar.appendChild(daysContainer);
    }

    function changeMonth(delta) {
        currentMonth += delta;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentMonth, currentYear);
    }

    // Initial render
    renderCalendar(currentMonth, currentYear);
</script>

</body>
</html>