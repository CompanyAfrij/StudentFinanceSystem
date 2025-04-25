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
$total_amount = isset($course['price']) && $course['price'] !== '' ? floatval($course['price']) : 0;
$registration_fee = 5000;
$certificate_fee = 5000;
$course_fee = max(0, $total_amount - ($registration_fee + $certificate_fee));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .card {
            background-color: #fff;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .card h2 {
            margin-bottom: 10px;
            font-size: 26px;
            color: #343a40;
        }

        .card p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin: 8px 0;
        }

        .card p strong {
            color: #000;
        }

        .price-row {
            background-color: #f1f1f1;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: left;
        }

        .btn {
            background-color: #800000;
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #b8860b;
        }

        @media screen and (max-width: 600px) {
            .card {
                padding: 20px;
            }
            .card h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <div class="card">
        <h2><?= htmlspecialchars($course['course_name']) ?></h2>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($course['description'])) ?></p>

        <div class="price-row">
            
            <p><strong>Registration Fee:</strong> Rs<?= number_format($registration_fee, 2) ?></p>
            <p><strong>Certificate Fee:</strong> Rs<?= number_format($certificate_fee, 2) ?></p>
            <p><strong>Course Fee:</strong> Rs<?= number_format($course_fee, 2) ?></p>
            <p><strong>Total Amount:</strong> Rs<?= number_format($total_amount, 2) ?></p>
        </div>

        <a href="payment.php?course_id=<?= htmlspecialchars($course['id']) ?>" class="btn">Pay Now</a>
    </div>

</body>
</html>
