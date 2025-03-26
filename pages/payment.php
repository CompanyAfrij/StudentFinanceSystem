<?php
session_start();
include '../includes/config.php';

// Check if course ID is provided
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    die("Invalid course selected.");
}

$course_id = $_GET['course_id'];

// Fetch course details
$query = "SELECT * FROM courses WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Course not found.");
}

$course = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-top: 50px;
        }
        h2 {
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payment for <?= $course['course_name'] ?></h2>
    <p>Amount to Pay: $<?= $course['price'] ?></p>
    
    <p>Payment Integration Coming Soon...</p>
</div>

</body>
</html>
