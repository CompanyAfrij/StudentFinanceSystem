<?php
session_start();
include '../includes/config.php';

// Ensure only student access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../pages/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Get student name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($student_name);
$stmt->fetch();
$stmt->close();

// Get all enrollments and course info
$query = "
    SELECT c.course_name, c.price AS total_fee, e.paid_amount, e.enrolled_at
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .header {
            background-color: #800000;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header a.logout {
            float: right;
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: bold;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #800000;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .balance {
            font-weight: bold;
            color: red;
        }
        .paid {
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>

<div class="header">
    <a href="../pages/logout.php" class="logout">Logout</a>
    <h1>Student Dashboard</h1>
</div>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($student_name) ?></h2>

    <?php if (count($enrollments)): ?>
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Total Fee (Rs)</th>
                    <th>Paid Amount (Rs)</th>
                    <th>Balance (Rs)</th>
                    <th>Enrolled At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $enroll): 
                    $balance = $enroll['total_fee'] - $enroll['paid_amount'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($enroll['course_name']) ?></td>
                        <td><?= number_format($enroll['total_fee'], 2) ?></td>
                        <td class="paid"><?= number_format($enroll['paid_amount'], 2) ?></td>
                        <td class="balance"><?= number_format($balance, 2) ?></td>
                        <td><?= date('d M Y', strtotime($enroll['enrolled_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not enrolled in any courses yet. <a href="courses.php">Browse Courses</a></p>
    <?php endif; ?>
</div>

</body>
</html>
