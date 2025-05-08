<?php
session_start();
include '../includes/database.php';

// Only admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

// Handle admin reply
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'], $_POST['message_id'], $_POST['student_email'])) {
    $reply = mysqli_real_escape_string($conn, $_POST['reply']);
    $message_id = (int)$_POST['message_id'];
    $student_email = filter_var($_POST['student_email'], FILTER_VALIDATE_EMAIL);

    // Update reply in DB
    $stmt = $conn->prepare("UPDATE messages SET reply = ?, replied_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $reply, $message_id);
    $stmt->execute();
    $stmt->close();

    // Send email to student
    if ($student_email) {
        $subject = "Reply to your message at IES Campus";
        $headers = "From: no-reply@iescampus.edu.in\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        mail($student_email, $subject, $reply, $headers);
    }

    header("Location: messages.php");
    exit();
}

// Fetch all messages
$result = mysqli_query($conn, "SELECT * FROM messages ORDER BY sent_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Messages - Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 30px;
            background: #f4f4f4;
        }

        h1 {
            color: maroon;
            margin-bottom: 30px;
        }

        .message-card {
            background: white;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 6px solid maroon;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        .message-card h3 {
            margin-top: 0;
            font-size: 20px;
            color: #333;
        }

        .message-meta {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        .message-body {
            font-size: 15px;
            margin-bottom: 15px;
        }

        .reply-box {
            margin-top: 15px;
        }

        .reply-box textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            font-size: 14px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .reply-box button {
            padding: 10px 20px;
            background-color: maroon;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .reply-box button:hover {
            background-color: #a00000;
        }

        .reply-text {
            background: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>

<h1>Student Messages</h1>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div class="message-card">
        <h3><?php echo htmlspecialchars($row['student_name']); ?> (<?php echo htmlspecialchars($row['student_email']); ?>)</h3>
        <div class="message-meta">
            Sent: <?php echo date("F j, Y, g:i a", strtotime($row['sent_at'])); ?>
        </div>

        <div class="message-body">
            <strong>Subject:</strong> <?php echo htmlspecialchars($row['subject']); ?><br><br>
            <strong>Message:</strong><br>
            <?php echo nl2br(htmlspecialchars($row['message'])); ?>
        </div>

        <div>
            <?php if (!empty($row['reply'])): ?>
                <strong>Reply:</strong>
                <div class="reply-text">
                    <?php echo nl2br(htmlspecialchars($row['reply'])); ?>
                </div>
                <div class="message-meta">
                    Replied at: <?php echo date("F j, Y, g:i a", strtotime($row['replied_at'])); ?>
                </div>
            <?php else: ?>
                <div class="reply-box">
                    <p><strong>Reply will be sent to:</strong> <?php echo htmlspecialchars($row['student_email']); ?></p>
                    <form method="post">
                        <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="student_email" value="<?php echo htmlspecialchars($row['student_email']); ?>">
                        <textarea name="reply" required placeholder="Write your reply here..."></textarea>
                        <button type="submit">Send Reply</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endwhile; ?>

</body>
</html>