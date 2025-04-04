<?php 
include '../includes/config.php';

// Check if course ID is provided
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid course selected.");
}

$course_id = intval($_GET['id']);

// Fetch course details securely
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
    <title>Course Details</title>
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
        .btn {
            background-color: #800000;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color:rgb(196, 157, 4);
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($course['course_name']) ?></h2>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($course['description'])) ?></p>
    <p><strong>Price:</strong> $<?= isset($course['price']) && $course['price'] !== '' ? htmlspecialchars($course['price']) : 'N/A' ?></p>

    <a href="payment.php?course_id=<?= htmlspecialchars($course['id']) ?>" class="btn">Pay Now</a>
</div>

</body>
</html>
