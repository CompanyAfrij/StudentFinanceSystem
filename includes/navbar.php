<!-- navbar.php -->
<nav class="navbar">
  <div class="nav-left">
    <img src="/FinanceManagementSystem/assets/images/ies_campus_logo.jpg" alt="IES Campus Logo" class="logo">
    <span class="brand-text">IES Campus</span>
  </div>
  <div class="nav-links">
    <a href="/FinanceManagementSystem/pages/about.php">About</a>
    <a href="/FinanceManagementSystem/pages/contact.php">Contact</a>
    <a href="/FinanceManagementSystem/pages/login.php">Login</a>
  </div>
</nav>

<style>
  .navbar {
    background-color: maroon; /* Changed to maroon */
    padding: 15px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 18px;
    flex-wrap: wrap;
    width: 100%;
    box-sizing: border-box;
  }

  .nav-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .logo {
    height: 60px;
    width: auto;
  }

  .brand-text {
    font-size: 22px;
    font-weight: bold;
    color: white; /* Changed to white for contrast */
    white-space: nowrap;
  }

  .nav-links {
    display: flex;
    align-items: center;
    gap: 25px;
    flex-wrap: wrap;
  }

  .nav-links a {
    color: white; /* Ensures text is visible on maroon */
    text-decoration: none;
    padding: 8px 14px;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
  }

  .nav-links a:hover {
    color: #ffcc00;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    transform: scale(1.05);
  }

  @media (max-width: 768px) {
    .navbar {
      flex-direction: column;
      align-items: flex-start;
      padding: 15px;
    }

    .nav-links {
      width: 100%;
      justify-content: space-between;
      margin-top: 10px;
      flex-wrap: wrap;
    }

    .nav-links a {
      flex: 1 1 auto;
      text-align: center;
    }
  }
</style>
