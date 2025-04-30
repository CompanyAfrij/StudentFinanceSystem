<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - IES Finance System</title>
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
        /* Navigation */
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
        /* Hero Section */
        .hero {
            background: url('background.jpg') no-repeat center center/cover;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 36px;
            font-weight: bold;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
        }
        /* Vision & Mission */
        .vision-mission {
            display: flex;
            justify-content: space-around;
            align-items: center;
            background: white;
            padding: 50px 10%;
            text-align: center;
        }
        .vision-box, .mission-box {
            width: 45%;
        }
        .vision-box img, .mission-box img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }
        .vision-box h2, .mission-box h2 {
            color: #112d4e;
            font-size: 28px;
        }
        .vision-box p, .mission-box p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin-top: 10px;
        }
        /* Statistics Section */
        .statistics {
            display: flex;
            justify-content: center;
            background: maroon;
            padding: 40px 0;
            text-align: center;
        }
        .stat-box {
            width: 25%;
            background: white;
            padding: 20px;
            margin: 10px;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .stat-box h3 {
            font-size: 32px;
            color: #112d4e;
        }
        .stat-box p {
            font-size: 14px;
            color: gray;
            margin-top: 5px;
        }
        /* Footer */
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
    <div><a href="#">IES CAMPUS</a></div>
    <div>
        <a href="#">Home</a>
        <a href="#">Schools</a>
        <a href="#">E-Learning</a>
        <a href="#">About</a>
        <a href="#">Events</a>
        <a href="#">Careers</a>
        <a href="#">Contact Us</a>
        <a href="#">Scholarship</a>
    </div>
    <a href="/FinanceManagementSystem/index.php" class="dashboard-btn">Home</a>


</div>

<!-- Hero Section -->
<div class="hero">
    About Us
</div>

<!-- Vision & Mission Section -->
<div class="vision-mission">
    <div class="vision-box">
        <h2>Vision</h2>
        <p>A leading institution dedicated to providing high-quality engineering education and fostering innovation. 
           Established with a vision to empower aspiring engineers, we take pride in our commitment to academic excellence and holistic development.</p>
    </div>
    <div class="mission-box">
        <h2>Mission</h2>
        <p>To cultivate a learning environment that nurtures creativity, critical thinking, and technical expertise, 
           preparing students for success in the ever-evolving field of engineering.</p>
    </div>
</div>

<!-- Statistics Section -->
<div class="statistics">
    <div class="stat-box">
        <h3>5+</h3>
        <p>Years of Experience</p>
    </div>
    <div class="stat-box">
        <h3>300+</h3>
        <p>Happy Students</p>
    </div>
    <div class="stat-box">
        <h3>20+</h3>
        <p>Qualified Instructors</p>
    </div>
    <div class="stat-box">
        <h3>30+</h3>
        <p>Courses</p>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    &copy; <?php echo date("Y"); ?> IES Campus Finance System. All Rights Reserved.
</div>

</body>
</html>