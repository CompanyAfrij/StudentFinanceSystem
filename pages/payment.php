<?php
session_start();
include '../includes/config.php';

$error = '';
$student_id = $_SESSION['user_id'] ?? null;

if (!$student_id) {
    die("Unauthorized access. Please login.");
}

// ----------------------- Installment Payment -----------------------
if (isset($_GET['installment_id'])) {
    $installment_id = intval($_GET['installment_id']);

    $stmt = $conn->prepare("SELECT i.amount, i.enrollment_id, e.student_id FROM installments i JOIN enrollments e ON i.enrollment_id = e.id WHERE i.id = ?");
    $stmt->bind_param("i", $installment_id);
    $stmt->execute();
    $installment = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($installment && $installment['student_id'] == $student_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paid_amount = floatval($_POST['paid_amount']);
            if ($paid_amount < $installment['amount']) {
                $error = "You must pay at least Rs " . number_format($installment['amount'], 2);
            } else {
                $update = $conn->prepare("UPDATE installments SET paid = 1 WHERE id = ?");
                $update->bind_param("i", $installment_id);
                $update->execute();

                $conn->query("UPDATE enrollments SET paid_amount = paid_amount + $paid_amount WHERE id = {$installment['enrollment_id']}");

                header("Location: student-dashboard.php");
                exit;
            }
        }

        // HTML output for installment payment
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Installment Payment</title>
        </head>
        <body>
            <h2>Installment Payment</h2>
            <p><strong>Installment Amount:</strong> Rs<?= number_format($installment['amount'], 2) ?></p>
            <form method="POST">
                <input type="number" name="paid_amount"
                       placeholder="Enter amount (minimum Rs<?= $installment['amount'] ?>)"
                       step="0.01"
                       min="<?= $installment['amount'] ?>"
                       required>
                <button type="submit">Pay Now</button>
                <?php if ($error): ?>
                    <div class="error" style="color:red"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
            </form>
        </body>
        </html>
        <?php
        exit();
    } else {
        die("Invalid installment selected.");
    }
}

// ----------------------- First Time Course Payment -----------------------
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    die("Invalid course selected.");
}

$course_id = intval($_GET['course_id']);

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

$course_name = $course['course_name'];
$total_amount = floatval($course['price']);
$registration_fee = floatval($course['registration_fee']);
$certificate_fee = floatval($course['certificate_fee']);
$course_fee = $total_amount - ($registration_fee + $certificate_fee);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paid_amount = floatval($_POST['paid_amount']);

    // Check for duplicate enrollment
    $check_query = "SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $student_id, $course_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('You have already paid for this course.'); window.location.href = 'student-dashboard.php';</script>";
        exit();
    }

    if ($paid_amount < $registration_fee) {
        $error = "You must pay at least the registration fee of Rs" . number_format($registration_fee, 2) . ".";
    } elseif ($paid_amount > $total_amount) {
        $error = "You cannot pay more than the total course amount of Rs" . number_format($total_amount, 2) . ".";
    } else {
        $insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id, paid_amount, enrolled_at) VALUES (?, ?, ?, NOW())");
        $insert->bind_param("iid", $student_id, $course_id, $paid_amount);

        if ($insert->execute()) {
            $enrollment_id = $conn->insert_id;

            $remaining = $total_amount - $paid_amount;

            if ($remaining > 0) {
                $installment_amount = round($remaining / 3, 2);
                for ($i = 1; $i <= 3; $i++) {
                    $due_date = date('Y-m-d', strtotime("+$i month"));
                    $conn->query("INSERT INTO installments (enrollment_id, installment_number, amount, due_date) 
                                  VALUES ($enrollment_id, $i, $installment_amount, '$due_date')");
                }
            }

            $_SESSION['payment_status'] = 'paid';
            $_SESSION['paid_amount'] = $paid_amount;
            $_SESSION['course_id'] = $course_id;

            header("Location: student-dashboard.php");
            exit();
        } else {
            $error = "Error enrolling student. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Payment</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            background: #fff;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            font-size: 16px;
            margin: 6px 0;
        }

        input[type="number"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            margin-top: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #800000;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: rgb(196, 157, 4);
        }

        .error {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payment for <?= htmlspecialchars($course_name) ?></h2>

    <p><strong>Registration Fee:</strong> Rs<?= number_format($registration_fee, 2) ?></p>
    <p><strong>Certificate Fee:</strong> Rs<?= number_format($certificate_fee, 2) ?></p>
    <p><strong>Course Fee:</strong> Rs<?= number_format($course_fee, 2) ?></p>
    <p><strong>Total Amount:</strong> Rs<?= number_format($total_amount, 2) ?></p>

    <form method="POST">
        <input type="number" name="paid_amount"
               placeholder="Enter amount to pay (Rs)"
               step="0.01"
               min="<?= $registration_fee ?>"
               max="<?= $total_amount ?>"
               required>
        <button type="submit">Pay Now</button>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>
</div>

</body>
</html>
