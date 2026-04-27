<?php
$required_role = 'patient'; 
require 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sanad | User Dashboard</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- Navbar -->
  <header class="sn-nav">
    <div class="sn-container sn-nav__inner">
      <a href="index.php" class="sn-nav__logo">
        <img src="images/slogo.png" alt="Sanad Logo" class="sn-nav__logo-img" />
        <span class="sn-nav__logo-name">Sanad</span>
      </a>

      <ul class="sn-nav__links">
        <li><a href="user-dashboard.php" class="sn-nav--active">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="submit-request.php">Submit Request</a></li>
        <li><a href="my-requests.php">My Requests</a></li>
        <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
      </ul>
    </div>
  </header>

  <!-- Main -->
  <main class="sn-main user-dashboard-page">
    <div class="sn-container">

      <!-- Welcome Section -->
      <section class="user-dash-hero">
        <div class="user-dash-hero__overlay"></div>

        <div class="user-dash-hero__content">
          <span class="user-dash-hero__badge">User Dashboard</span>
          <h1 class="user-dash-hero__title">Welcome back to Sanad</h1>
          <p class="user-dash-hero__text">
            Manage your medication journey in one place. From here, you can view your profile,
            submit a new medication request, or track your current requests and offers.
          </p>
        </div>
      </section>

      <!-- Quick Actions -->
      <section class="user-dash-actions">
        <a href="profile.php" class="user-dash-card">
          <div class="user-dash-card__icon">👤</div>
          <h2 class="user-dash-card__title">Profile</h2>
          <p class="user-dash-card__text">
            View your personal information and account details.
          </p>
          <span class="user-dash-card__link">Go to Profile</span>
        </a>

        <a href="submit-request.php" class="user-dash-card">
          <div class="user-dash-card__icon">📝</div>
          <h2 class="user-dash-card__title">Submit a Request</h2>
          <p class="user-dash-card__text">
            Create a new medication request and upload your prescription.
          </p>
          <span class="user-dash-card__link">Create Request</span>
        </a>

        <a href="my-requests.php" class="user-dash-card">
          <div class="user-dash-card__icon">📋</div>
          <h2 class="user-dash-card__title">My Requests</h2>
          <p class="user-dash-card__text">
            Check your submitted requests, statuses, and related pharmacy offers.
          </p>
          <span class="user-dash-card__link">View Requests</span>
        </a>
      </section>

    </div>
  </main>

  <!-- Footer -->
  <footer class="sn-footer">
    <div class="sn-container">
      <div class="sn-footer__inner">
          <span class="sn-footer__logo-name">Sanad</span>
        <span class="sn-footer__copy">© 2026 Sanad. Riyadh, Saudi Arabia.</span>
      </div>
    </div>
  </footer>

</body>
</html>