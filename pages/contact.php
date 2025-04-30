<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - IES Campus</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f4f4f4;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar a {
            text-decoration: none;
            color: #333;
            margin: 0 15px;
            font-size: 16px;
            font-weight: bold;
        }
        .navbar .dashboard-btn {
            background-color: maroon;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .hero {
            height: 200px;
            background: maroon;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
        }
        .contact-section {
            background: white;
            padding: 40px 10%;
            display: flex;
            justify-content: space-between;
            gap: 40px;
        }
        .contact-info {
            width: 40%;
        }
        .contact-info h2 {
            color: maroon;
            margin-bottom: 20px;
        }
        .contact-info p {
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .contact-form {
            width: 55%;
        }
        .contact-form h2 {
            color: maroon;
            margin-bottom: 20px;
        }
        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .contact-form button {
            background: maroon;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .footer {
            background: maroon;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div><a href="/FinanceManagementSystem/index.php">IES CAMPUS</a></div>
    <div>
        <a href="/FinanceManagementSystem/index.php">Home</a>
        <a href="#">Schools</a>
        <a href="#">E-Learning</a>
        <a href="about.php">About</a>
        <a href="#">Events</a>
        <a href="#">Careers</a>
        <a href="contact.php">Contact Us</a>
        <a href="#">Scholarship</a>
    </div>
    <a href="/FinanceManagementSystem/index.php" class="dashboard-btn">Home</a>
</div>

<!-- Hero -->
<div class="hero">Contact Us</div>

<!-- Contact Section -->
<div class="contact-section">
    <div class="contact-info">
        <h2>Get in Touch</h2>
        <p><strong>Address:</strong> IES Campus, Near Tech Park, Indore, MP, India</p>
        <p><strong>Phone:</strong> +91-9876543210</p>
        <p><strong>Email:</strong> info@iescampus.edu.in</p>
        <p><strong>Hours:</strong> Mon - Fri: 9:00 AM to 5:00 PM</p>
    </div>
    <div class="contact-form">
        <h2>Request about finance state</h2>
        <form action="#" method="post">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <input type="text" name="subject" placeholder="Subject" required>
            <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    &copy; <?php echo date("Y"); ?> IES Campus Finance System. All Rights Reserved.
</div>

</body>
</html>