<?php
session_start();
require '../includes/config.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php"); // Ensure correct login path
    exit();
}


// Fetch current settings from database
$sql = "SELECT * FROM settings LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $settings = $result->fetch_assoc();
} else {
    $settings = [
        "system_name" => "",
        "contact_email" => "",
        "default_currency" => "USD",
        "enable_registration" => 0,
        "max_courses" => 0
    ];
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $system_name = $_POST['system_name'];
    $contact_email = $_POST['contact_email'];
    $default_currency = $_POST['default_currency'];
    $enable_registration = isset($_POST['enable_registration']) ? 1 : 0;
    $max_courses = $_POST['max_courses'];

    // Update settings in database
    $sql = "UPDATE settings SET system_name='$system_name', contact_email='$contact_email', 
            default_currency='$default_currency', enable_registration='$enable_registration', 
            max_courses='$max_courses' WHERE id=1";

    if ($conn->query($sql) === TRUE) {
        $message = "Settings updated successfully!";
    } else {
        $message = "Error updating settings: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Settings</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>


    <div class="settings-container">
        <h2>Manage System Settings</h2>
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

        <form method="POST">
            <label>System Name:</label>
            <input type="text" name="system_name" value="<?php echo $settings['system_name']; ?>" required>

            <label>Contact Email:</label>
            <input type="email" name="contact_email" value="<?php echo $settings['contact_email']; ?>" required>

            <label>Default Currency:</label>
            <select name="default_currency">
                <option value="USD" <?php if ($settings['default_currency'] == "USD") echo "selected"; ?>>USD</option>
                <option value="EUR" <?php if ($settings['default_currency'] == "EUR") echo "selected"; ?>>EUR</option>
                <option value="LKR" <?php if ($settings['default_currency'] == "LKR") echo "selected"; ?>>LKR</option>
            </select>

            <label>Enable User Registration:</label>
            <input type="checkbox" name="enable_registration" <?php if ($settings['enable_registration']) echo "checked"; ?>>

            <label>Max Courses Per Student:</label>
            <input type="number" name="max_courses" value="<?php echo $settings['max_courses']; ?>" required>

            <button type="submit" class="btn">Save Settings</button>
        </form>
    </div>
</body>
</html>
