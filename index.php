<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Finance Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
      color: #333;
    }

    header {
      background-color: maroon;
      color: white;
      padding: 10px 0;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      padding: 0 10%;
      font-size: 14px;
    }

    .top-bar div {
      padding: 5px 0;
    }

    .hero {
      background: url("assets/images/fm.jpg") no-repeat center center/cover;
      height: 90vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      flex-direction: column;
    }

    .hero-content {
      background-color: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(5px);
      padding: 40px;
      border-radius: 10px;
    }

    .hero h1 {
      font-size: 48px;
      margin-bottom: 10px;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
      color: white;
    }

    .hero p {
      font-size: 18px;
      margin-bottom: 20px;
      text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
      color: white;
    }

    .hero .btn {
      padding: 12px 24px;
      background-color: maroon;
      color: white;
      border: none;
      text-decoration: none;
      margin: 0 10px;
      border-radius: 5px;
      transition: all 0.3s ease;
    }

    .hero .btn:hover {
      background-color: #600000;
      transform: translateY(-2px);
    }

    .features {
      display: flex;
      justify-content: center;
      gap: 30px;
      padding: 40px 20px;
      background-color: #f9f9f9;
      flex-wrap: wrap;
    }

    .feature-box {
      background-color: #fff;
      border: 1px solid #ddd;
      padding: 25px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      width: 300px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .feature-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .feature-box h3 {
      font-size: 22px;
      margin-bottom: 15px;
      color: #800000;
    }

    .feature-box p {
      font-size: 16px;
      color: #555;
    }

    /* Scholarship Card Styles */
    .scholarship-card {
      background-color: #800000;
      color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(128, 0, 0, 0.2);
      width: 300px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .scholarship-card:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 12px 25px rgba(128, 0, 0, 0.3);
    }

    .scholarship-card::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
      transform: rotate(30deg);
      transition: all 0.5s ease;
    }

    .scholarship-card:hover::before {
      transform: rotate(30deg) translate(20%, 20%);
    }

    .scholarship-card h3 {
      font-size: 24px;
      margin-bottom: 15px;
      color: white;
      position: relative;
    }

    .scholarship-card p {
      font-size: 16px;
      margin-bottom: 20px;
      position: relative;
    }

    .scholarship-card .btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: white;
      color: #800000;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .scholarship-card .btn:hover {
      background-color: #f0f0f0;
      transform: translateY(-2px);
    }

    .about {
      display: flex;
      padding: 60px 10%;
      background-color: #f1f1f1;
      align-items: center;
      flex-wrap: wrap;
    }

    .about-image {
      flex: 1;
      text-align: center;
      min-width: 300px;
    }

    .about-image img {
      max-width: 100%;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .about-text {
      flex: 1;
      padding: 0 30px;
      min-width: 300px;
    }

    .about-text h2 {
      color: maroon;
      margin-bottom: 15px;
    }

    .about-text p {
      font-size: 17px;
      margin-bottom: 20px;
      line-height: 1.6;
    }

    @media (max-width: 768px) {
      nav {
        flex-direction: column;
      }

      .hero h1 {
        font-size: 32px;
      }

      .features .feature-box, 
      .scholarship-card {
        width: 100%;
        max-width: 350px;
      }

      .about {
        flex-direction: column;
      }

      .about-text {
        padding: 20px 0;
      }
    }
  </style>
</head>
<body>
  <?php include 'includes/navbar.php'; ?>

  <section class="hero">
    <div class="hero-content">
      <h1>Institute of Engineering Studies</h1>
      <p>Empowering IES Campus with secure, accurate, and efficient finance Management System.</p>
      <div>
        <a href="pages/login.php" class="btn">Login</a>
        <a href="pages/register.php" class="btn">Register</a>
      </div>
    </div>
  </section>

  <section class="features">
    <div class="feature-box">
      <h3>Transaction Records</h3>
      <p>Track all financial transactions securely with transparency and audit readiness.</p>
    </div>
    
    <div class="feature-box">
      <h3>Financial Reports</h3>
      <p>Generate automated reports for better decision making and compliance.</p>
    </div>
    
    <div class="scholarship-card">
      <h3>Scholarship Opportunities</h3>
      <p>Your dreams are within reach! We offer various scholarship programs to support deserving students in their academic journey.</p>
      <p>Contact our financial aid office to verify your documents and check eligibility. Let us help you achieve your educational goals!</p>
    </div>
  </section>

  <section class="about">
    <div class="about-image">
      <img src="assets/images/home.jpg">
    </div>
    <div class="about-text">
      <h2> IES Campus </h2>
      <p>The IES Finance Management System is developed to streamline and digitize the financial activities of the campus, ensuring accurate tracking of all transactions while maintaining the highest standards of financial integrity.</p>
      <p>Our system helps administrators, staff, and finance teams to collaborate more effectively while maintaining transparency and control over financial resources. With features like scholarship management, we're committed to making education accessible to all.</p>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>