<?php
session_start();
include '../includes/config.php';

// Fetch course list for dropdown
$courses = $conn->query("SELECT id, course_name FROM courses");

// Base query
$filter_query = "
    SELECT 
        e.student_id,
        u.name AS student_name,
        e.course_id,
        c.course_name,
        c.price AS total_course_fee,
        e.paid_amount,
        (c.price - e.paid_amount) AS balance,
        e.enrolled_at
    FROM enrollments e
    JOIN users u ON e.student_id = u.id
    JOIN courses c ON e.course_id = c.id
";

// Apply filters if set
$conditions = [];
$params = [];
$types = "";

if (!empty($_GET['course_id'])) {
    $conditions[] = "c.id = ?";
    $params[] = $_GET['course_id'];
    $types .= "i";
}
if (!empty($_GET['student_id'])) {
    $conditions[] = "u.id = ?";
    $params[] = $_GET['student_id'];
    $types .= "i";
}

if (!empty($conditions)) {
    $filter_query .= " WHERE " . implode(" AND ", $conditions);
}
$filter_query .= " ORDER BY e.enrolled_at DESC";

$stmt = $conn->prepare($filter_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students - Finance Overview</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f5f5;
            padding: 30px;
        }
        h2 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        select, input[type="text"] {
            padding: 10px;
            margin-right: 10px;
            font-size: 16px;
        }
        button {
            padding: 10px 16px;
            background-color: #800000;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #800000;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        #searchInput {
            float: right;
        }
    </style>
    <script>
        function filterTable() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const rows = document.querySelectorAll("#studentTable tbody tr");
            rows.forEach(row => {
                const rowText = row.innerText.toLowerCase();
                row.style.display = rowText.includes(input) ? "" : "none";
            });
        }
    </script>
</head>
<body>

<h2>Student Finance Overview</h2>

<form method="GET">
    <select name="course_id">
        <option value="">-- Filter by Course --</option>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <option value="<?= $course['id'] ?>" <?= ($_GET['course_id'] ?? '') == $course['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($course['course_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <input type="text" name="student_id" placeholder="Student ID" value="<?= htmlspecialchars($_GET['student_id'] ?? '') ?>">
    <button type="submit">Apply Filters</button>
</form>

<input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Quick Search..." />

<table id="studentTable">
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Course ID</th>
            <th>Course Name</th>
            <th>Total Fee (Rs)</th>
            <th>Paid Amount (Rs)</th>
            <th>Balance (Rs)</th>
            <th>Enrolled At</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['course_id']) ?></td>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= number_format($row['total_course_fee'], 2) ?></td>
                    <td><?= number_format($row['paid_amount'], 2) ?></td>
                    <td><?= number_format($row['balance'], 2) ?></td>
                    <td><?= htmlspecialchars($row['enrolled_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No student enrollments found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
