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
      background-color: rgba(0, 0, 0, 0.4); /* semi-transparent black */
      backdrop-filter: blur(5px); /* blur effect */
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
    }

    .hero .btn:hover {
      background-color: #600000;
    }

    .features {
      display: flex;
      justify-content: space-around;
      background-color: #ffffff;
      padding: 50px 10%;
      flex-wrap: wrap;
    }

    .feature-box {
      width: 30%;
      background-color: #f9f9f9;
      padding: 25px;
      border-radius: 8px;
      margin: 15px 0;
      box-shadow: 0 2px 5px rgba(128,0,0,0.1);
    }

    .feature-box h3 {
      color: maroon;
      margin-bottom: 10px;
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
    }

    .about-image img {
      max-width: 100%;
      border-radius: 10px;
    }

    .about-text {
      flex: 1;
      padding: 0 30px;
    }

    .about-text h2 {
      color: maroon;
      margin-bottom: 15px;
    }

    .about-text p {
      font-size: 17px;
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      nav {
        flex-direction: column;
      }

      .hero h1 {
        font-size: 32px;
      }

      .features .feature-box {
        width: 100%;
      }

      .about {
        flex-direction: column;
      }

      .about-text {
        padding: 20px 0;
      }

      .about-image img {
        max-width: 100%;
        border-radius: 10px;
      }
    }
  </style>
</head>
<body>
  <?php include 'includes/navbar.php'; ?>

  <section class="hero">
    <div class="hero-content">
      <h1>Institute of Engneering Studies</h1>
      <p>Empowering IES Campus with secure, accurate, and efficient finance Management System.</p>
      <div>
        <a href="pages/login.php" class="btn">Login</a>
        <a href="pages/register.php" class="btn">Register</a>
      </div>
    </div>
  </section>

  <style>
  .features {
    display: flex;
    justify-content: center;
    gap: 30px;
    padding: 40px 20px;
    background-color: #f9f9f9;
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
</style>

<section class="features">
  <div class="feature-box">
    <h3>Transaction Records</h3>
    <p>Track all financial transactions securely with transparency and audit readiness.</p>
  </div>
  <div class="feature-box">
    <h3>Financial Reports</h3>
    <p>Generate automated reports for better decision making and compliance.</p>
  </div>
</section>

  <section class="about">
    <div class="about-image">
      <img src="assets/images/home.jpg">
    </div>
    <div class="about-text">
      <h2>About IES Campus Finance System</h2>
      <p>The IES Finance Management System is developed to streamline and digitize the financial activities of the campus, ensuring accurate tracking.</p>
      <p>It helps administrators, staff, and finance teams to collaborate more effectively while maintaining transparency and control over financial resources.</p>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>