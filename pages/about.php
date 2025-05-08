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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        /* Navigation */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar a {
            text-decoration: none;
            color: #333;
            margin: 0 15px;
            font-size: 16px;
            font-weight: 600;
            transition: color 0.3s;
        }
        .navbar a:hover {
            color: maroon;
        }
        .navbar .dashboard-btn {
            background-color: maroon;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .navbar .dashboard-btn:hover {
            background-color: #800000;
            color: white;
        }
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 48px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
        }
        .hero h1 {
            animation: fadeIn 1.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* About Content */
        .about-content {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .about-content h2 {
            color: maroon;
            font-size: 36px;
            margin-bottom: 20px;
            text-align: center;
        }
        .about-content p {
            font-size: 18px;
            margin-bottom: 20px;
            text-align: justify;
        }
        /* Values Section */
        .values {
            background-color: white;
            padding: 60px 0;
        }
        .values-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .values-container h2 {
            text-align: center;
            color: maroon;
            font-size: 36px;
            margin-bottom: 40px;
        }
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .value-card {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .value-card:hover {
            transform: translateY(-10px);
        }
        .value-card h3 {
            color: maroon;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .value-card p {
            font-size: 16px;
        }
        /* Team Section */
        .team {
            padding: 60px 0;
            background-color: #f4f4f4;
        }
        .team-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .team-container h2 {
            text-align: center;
            color: maroon;
            font-size: 36px;
            margin-bottom: 40px;
        }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        .team-member {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .team-member h3 {
            color: maroon;
            font-size: 22px;
            margin-bottom: 10px;
        }
        .team-member p {
            color: #666;
            font-style: italic;
            font-size: 16px;
        }
        /* Statistics Section */
        .statistics {
            display: flex;
            justify-content: center;
            background: maroon;
            padding: 60px 0;
            text-align: center;
            flex-wrap: wrap;
        }
        .stat-box {
            width: 250px;
            background: white;
            padding: 30px 20px;
            margin: 15px;
            border-radius: 8px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }
        .stat-box:hover {
            transform: scale(1.05);
        }
        .stat-box h3 {
            font-size: 42px;
            color: maroon;
            margin-bottom: 10px;
        }
        .stat-box p {
            font-size: 16px;
            color: #666;
            font-weight: 600;
        }
        /* Footer */
        .footer {
            background: maroon;
            color: white;
            text-align: center;
            padding: 30px;
            margin-top: 20px;
        }
        .footer p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div><a href="#">IES CAMPUS</a></div>
    <a href="/FinanceManagementSystem/index.php" class="dashboard-btn">Dashboard</a>
</div>

<!-- Hero Section -->
<div class="hero">
    <h1>Shaping the Future of Engineering Education</h1>
</div>

<!-- About Content -->
<div class="about-content">
    <h2>Our Story</h2>
    <p>Founded in 2018, IES Campus has grown from a small engineering institute to a premier educational hub known for its innovative approach to technical education. What began as a vision to bridge the gap between academia and industry has now transformed into a thriving community of learners, educators, and industry partners.</p>
    
    <p>At IES, we believe that engineering education should be more than just textbooks and lectures. Our hands-on, project-based learning approach ensures that students graduate not just with degrees, but with the skills and confidence to tackle real-world challenges.</p>
    
    <p>Our state-of-the-art campus features cutting-edge laboratories, modern classrooms, and collaborative spaces designed to foster creativity and innovation. But what truly sets us apart is our dedicated faculty and staff who are committed to nurturing the next generation of engineering leaders.</p>
</div>

<!-- Values Section -->
<div class="values">
    <div class="values-container">
        <h2>Our Core Values</h2>
        <div class="values-grid">
            <div class="value-card">
                <h3>Excellence</h3>
                <p>We strive for the highest standards in education, research, and innovation, continuously pushing boundaries to achieve outstanding results.</p>
            </div>
            <div class="value-card">
                <h3>Integrity</h3>
                <p>We uphold the highest ethical standards in all our actions, fostering trust and respect within our community and beyond.</p>
            </div>
            <div class="value-card">
                <h3>Innovation</h3>
                <p>We embrace creativity and forward-thinking approaches to solve complex problems and drive technological advancement.</p>
            </div>
            <div class="value-card">
                <h3>Collaboration</h3>
                <p>We believe in the power of teamwork, both within our institution and through partnerships with industry and other academic institutions.</p>
            </div>
            <div class="value-card">
                <h3>Diversity</h3>
                <p>We celebrate differences and create an inclusive environment where all students and staff can thrive and contribute their unique perspectives.</p>
            </div>
            <div class="value-card">
                <h3>Sustainability</h3>
                <p>We are committed to environmentally responsible practices and developing solutions that address global sustainability challenges.</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="statistics">
    <div class="stat-box">
        <h3>5+</h3>
        <p>Years of Educational Excellence</p>
    </div>
    <div class="stat-box">
        <h3>300+</h3>
        <p>Students</p>
    </div>
    <div class="stat-box">
        <h3>20+</h3>
        <p>Instructors</p>
    </div>
    <div class="stat-box">
        <h3>30+</h3>
        <p>Courses</p>
    </div>
</div>

<!-- Team Section -->
<div class="team">
    <div class="team-container">
        <h2>Our Team</h2>
        <div class="team-grid">
            <div class="team-member">
                <h3>Mr. Afrij</h3>
                <p>Director</p>
            </div>
            <div class="team-member">
                <h3>Mr. Rushthy</h3>
                <p>Digital Marketing Manager</p>
            </div>
            <div class="team-member">
                <h3>Mr. Sharaf</h3>
                <p>Senior Developer</p>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; <?php echo date("Y"); ?> IES Campus Finance System. All Rights Reserved.</p>
    <p>No.80, Main Street Addalaichenai | Phone: +94 742000416 | Email: info@iescampus.edu</p>
</div>

</body>
</html>