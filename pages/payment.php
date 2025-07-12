<?php
session_start();
include '../includes/config.php';

$error = '';
$student_id = $_SESSION['user_id'] ?? null;
$discount_applied = false;
$discounted_total = 0;
$original_course_fee = 0;
$discount_error = '';

if (!$student_id) {
    die("Unauthorized access. Please login.");
}

// ----------------------- Installment Payment -----------------------
if (isset($_GET['installment_id'])) {
    $installment_id = intval($_GET['installment_id']);

    $stmt = $conn->prepare("SELECT i.amount, i.enrollment_id, i.paid, e.student_id FROM installments i JOIN enrollments e ON i.enrollment_id = e.id WHERE i.id = ?");
    $stmt->bind_param("i", $installment_id);
    $stmt->execute();
    $installment = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($installment && $installment['student_id'] == $student_id) {
        if ($installment['paid']) {
            die("This installment has already been paid.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paid_amount = floatval($_POST['paid_amount']);

            if ($paid_amount != $installment['amount']) {
                $error = "You must pay exactly Rs " . number_format($installment['amount'], 2) . " for this installment.";
            } else {
                $update = $conn->prepare("UPDATE installments SET paid = 1, paid = NOW() WHERE id = ?");
                $update->bind_param("i", $installment_id);
                $update->execute();

                $conn->query("UPDATE enrollments SET paid_amount = paid_amount + $paid_amount WHERE id = {$installment['enrollment_id']}");

                header("Location: student-dashboard.php");
                exit;
            }
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Installment Payment</title>
            <style>
                body {
                    font-family: 'Segoe UI', sans-serif;
                    background-color: #f4f6f8;
                    padding: 40px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                }

                .payment-card {
                    background: #fff;
                    padding: 40px;
                    border-radius: 12px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                    width: 100%;
                    max-width: 600px;
                }

                h2 {
                    color: #800000;
                    margin-bottom: 25px;
                    text-align: center;
                }

                .info-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 12px;
                    padding-bottom: 10px;
                    border-bottom: 1px solid #eee;
                }

                .info-row strong {
                    color: #555;
                }

                .amount {
                    color: #800000;
                    font-weight: bold;
                }

                .payment-form {
                    margin-top: 30px;
                }

                .form-group {
                    margin-bottom: 18px;
                }

                label {
                    display: block;
                    margin-bottom: 10px;
                    color: #333;
                    font-weight: bold;
                }

                input[type="number"] {
                    width: calc(100% - 24px);
                    padding: 14px;
                    font-size: 16px;
                    border-radius: 8px;
                    border: 1px solid #ccc;
                    box-sizing: border-box;
                    margin-top: 6px;
                }

                button {
                    background-color: #800000;
                    color: white;
                    padding: 14px 22px;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 16px;
                    width: 100%;
                    transition: background-color 0.3s ease;
                }

                button:hover {
                    background-color: #660000;
                }

                .error {
                    color: #dc3545;
                    margin-top: 12px;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
        <div class="payment-card">
            <h2>Installment Payment</h2>
            <div class="info-row">
                <strong>Installment Amount:</strong>
                <span class="amount">Rs<?= number_format($installment['amount'], 2) ?></span>
            </div>
            <form method="POST" class="payment-form">
                <div class="form-group">
                    <label for="paid_amount">Enter Exact Amount:</label>
                    <input type="number" id="paid_amount" name="paid_amount"
                           placeholder="Rs<?= number_format($installment['amount'], 2) ?>"
                           step="1"
                           min="<?= intval($installment['amount']) ?>"
                           max="<?= intval($installment['amount']) ?>"
                           required>
                </div>
                <button type="submit">Pay Now</button>
                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
            </form>
        </div>
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
$original_course_fee = $course_fee;

// Handle discount code submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discount_code'])) {
    $discount_code = trim($_POST['discount_code']);
    
    // Validate discount code format
    if (!preg_match('/^#ies[a-zA-Z0-9]+$/', $discount_code)) {
        $discount_error = "Invalid promo code format. It must start with #ies followed by letters/numbers.";
    } else {
        // Here you would typically check the code against a database
        // For this implementation, we'll assume any valid format code gives 20% off course fee
        $discount_applied = true;
        $discount_amount = $original_course_fee * 0.20; // 20% discount on course fee
        $course_fee = $original_course_fee - $discount_amount;
        $total_amount = $registration_fee + $certificate_fee + $course_fee;
        $discounted_total = $total_amount;
    }
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paid_amount'])) {
    $paid_amount = floatval($_POST['paid_amount']);
    $original_total = floatval($course['price']);

    $check_query = "SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $student_id, $course_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('You have already paid for this course.'); window.location.href = 'student-dashboard.php';</script>";
        exit();
    }

    $min_payment = $registration_fee;
    
    if ($paid_amount < $min_payment) {
        $error = "You must pay at least the registration fee of Rs" . number_format($registration_fee, 2) . ".";
    } elseif ($paid_amount > ($discount_applied ? $discounted_total : $original_total)) {
        $error = "You cannot pay more than the " . ($discount_applied ? "discounted " : "") . "course amount of Rs" . number_format(($discount_applied ? $discounted_total : $original_total), 2) . ".";
    } else {
        $discount_code_used = $discount_applied ? $_POST['discount_code'] : null;
        $discount_amount = $discount_applied ? ($original_course_fee * 0.20) : 0;
        
        $insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id, paid_amount, enrolled_at, discount_code, discount_amount) VALUES (?, ?, ?, NOW(), ?, ?)");
        $insert->bind_param("iidsd", $student_id, $course_id, $paid_amount, $discount_code_used, $discount_amount);

        if ($insert->execute()) {
            $enrollment_id = $conn->insert_id;

            // Calculate remaining amount (using discounted total if applicable)
            $remaining = ($discount_applied ? $discounted_total : $original_total) - $paid_amount;
            $remaining = round($remaining); // Round to nearest whole number
            
            if ($remaining > 0) {
                // Split into 3 equal installments (rounded to whole numbers)
                $installment_amount = round($remaining / 3);
                $last_installment = $remaining - ($installment_amount * 2);
                
                // Ensure all installments are whole numbers
                $installments = [
                    round($installment_amount),
                    round($installment_amount),
                    round($last_installment)
                ];
                
                // Make sure sum of installments equals remaining amount
                $sum_diff = $remaining - array_sum($installments);
                $installments[2] += $sum_diff; // Adjust last installment if there's any rounding difference

                for ($i = 1; $i <= 3; $i++) {
                    $due_date = date('Y-m-d', strtotime("+$i month"));
                    $amount = $installments[$i-1];

                    $conn->query("INSERT INTO installments (enrollment_id, installment_number, amount, due_date)
                                VALUES ($enrollment_id, $i, $amount, '$due_date')");
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
            background-color: #f4f6f8;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .payment-card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 600px;
        }

        h2 {
            color: #800000;
            margin-bottom: 25px;
            text-align: center;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .info-row strong {
            color: #555;
        }

        .amount {
            color: #800000;
            font-weight: bold;
        }

        .discount-row {
            color: #28a745;
            font-weight: bold;
        }

        .payment-form {
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
        }

        input[type="number"], input[type="text"] {
            width: calc(100% - 24px);
            padding: 14px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            margin-top: 6px;
        }

        button {
            background-color: #800000;
            color: white;
            padding: 14px 22px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #660000;
        }

        .btn-secondary {
            background-color: #6c757d;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .error {
            color: #dc3545;
            margin-top: 12px;
            font-size: 14px;
        }

        .success {
            color: #28a745;
            margin-top: 12px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="payment-card">
    <h2>Payment for <?= htmlspecialchars($course_name) ?></h2>

    <div class="info-row">
        <strong>Registration Fee:</strong>
        <span class="amount">Rs<?= number_format($registration_fee, 2) ?></span>
    </div>
    <div class="info-row">
        <strong>Certificate Fee:</strong>
        <span class="amount">Rs<?= number_format($certificate_fee, 2) ?></span>
    </div>
    <div class="info-row">
        <strong>Course Fee:</strong>
        <span class="amount">Rs<?= number_format($original_course_fee, 2) ?></span>
    </div>
    <?php if ($discount_applied): ?>
        <div class="info-row discount-row">
            <strong>Discount Applied (20% on Course Fee):</strong>
            <span class="amount">- Rs<?= number_format($original_course_fee * 0.20, 2) ?></span>
        </div>
        <div class="info-row">
            <strong>Discounted Course Fee:</strong>
            <span class="amount">Rs<?= number_format($course_fee, 2) ?></span>
        </div>
    <?php endif; ?>
    <div class="info-row" style="border-bottom: 2px solid #ccc; padding-bottom: 12px; margin-bottom: 15px;">
        <strong>Total Amount:</strong>
        <span class="amount" style="color: #800000; font-size: 1.1em;">
            Rs<?= number_format($discount_applied ? $discounted_total : $total_amount, 2) ?>
            <?php if ($discount_applied): ?>
                <span style="text-decoration: line-through; color: #777; font-size: 0.9em; margin-left: 5px;">
                    Rs<?= number_format($total_amount, 2) ?>
                </span>
            <?php endif; ?>
        </span>
    </div>

    <?php if (!$discount_applied): ?>
        <form method="POST" class="payment-form">
            <div class="form-group">
                <label for="discount_code">Scholarship Eligible</label>
                <input type="text" id="discount_code" name="discount_code" 
                       placeholder="Enter the Scholarship code provided by IES">
                <button type="submit" class="btn-secondary">Apply Discount</button>
                <?php if ($discount_error): ?>
                    <div class="error"><?= htmlspecialchars($discount_error) ?></div>
                <?php endif; ?>
            </div>
        </form>
    <?php else: ?>
        <div class="success">Discount code applied successfully! (20% off course fee)</div>
    <?php endif; ?>

    <form method="POST" class="payment-form">
        <div class="form-group">
            <label for="paid_amount">Enter Amount to Pay (Rs):</label>
            <input type="number" id="paid_amount" name="paid_amount"
                   placeholder="Minimum: Rs<?= number_format($registration_fee, 2) ?>"
                   step="1"
                   min="<?= intval($registration_fee) ?>"
                   max="<?= intval($discount_applied ? $discounted_total : $total_amount) ?>"
                   required>
        </div>
        <?php if ($discount_applied): ?>
            <input type="hidden" name="discount_code" value="<?= htmlspecialchars($_POST['discount_code']) ?>">
        <?php endif; ?>
        <button type="submit">Pay Now</button>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>
</div>

</body>
</html>