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
            padding: 40px;
            max-width: 600px; /* Increased card size */
            width: 95%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .card h2 {
            margin-bottom: 15px;
            font-size: 30px; /* Larger heading */
            color: #343a40;
        }

        .card p {
            font-size: 18px; /* Slightly larger text */
            color: #555;
            line-height: 1.8;
            margin: 10px 0;
        }

        .card p strong {
            color: #000;
        }

        .price-row {
            background-color: #f8f9fa; /* Lighter background for clarity */
            padding: 15px 20px;
            border-radius: 10px;
            margin: 10px 0; /* Increased spacing between rows */
            text-align: left;
            border: 1px solid #e0e0e0; /* Subtle border for separation */
            display: flex;
            justify-content: space-between; /* Align amounts to the right */
            align-items: center;
        }

        .price-row strong {
            font-weight: 600;
            color: #2c3e50; /* Darker color for emphasis */
        }

        .price-row span {
            font-size: 18px;
            color: #800000; /* Maroon for amounts */
            font-weight: 500;
        }

        .total-row {
            background-color: #e9ecef; /* Distinct background for total */
            font-weight: bold;
            padding: 20px;
            margin-top: 20px;
            border-radius: 12px;
            border: 1px solid #d0d0d0;
        }

        .btn {
            background-color: #800000;
            color: #fff;
            padding: 15px 30px; /* Larger button */
            border: none;
            border-radius: 10px;
            font-size: 18px;
            text-decoration: none;
            display: inline-block;
            margin-top: 25px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #b8860b;
        }

        @media screen and (max-width: 600px) {
            .card {
                padding: 25px;
                max-width: 90%;
            }
            .card h2 {
                font-size: 24px;
            }
            .card p {
                font-size: 16px;
            }
            .price-row {
                padding: 12px 15px;
                font-size: 16px;
            }
            .btn {
                padding: 12px 20px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

    <div class="card">
        <h2><?= htmlspecialchars($course['course_name']) ?></h2>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($course['description'])) ?></p>

        <div class="price-row">
            <strong>Registration Fee:</strong>
            <span>Rs<?= number_format($registration_fee, 2) ?></span>
        </div>
        <div class="price-row">
            <strong>Certificate Fee:</strong>
            <span>Rs<?= number_format($certificate_fee, 2) ?></span>
        </div>
        <div class="price-row">
            <strong>Course Fee:</strong>
            <span>Rs<?= number_format($course_fee, 2) ?></span>
        </div>
        <div class="price-row total-row">
            <strong>Total Amount:</strong>
            <span>Rs<?= number_format($total_amount, 2) ?></span>
        </div>

        <a href="payment.php?course_id=<?= htmlspecialchars($course['id']) ?>" class="btn">Pay Now</a>
    </div>

</body>
</html>