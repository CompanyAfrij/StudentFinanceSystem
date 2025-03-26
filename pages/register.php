<?php
include '../includes/header.php';
include '../includes/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <?php include '../includes/navbar.php'; ?>

    <div class="register-container">
        <h2>Register</h2>
        <form action="register-process.php" method="POST">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" class="btn">Register</button>

            <p class="login-text" style="color: black;">
    Already have an account? <a href="login.php" style="color: blue; text-decoration: underline;">Login here</a>
</p>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
