

<?php
$required_role = 'admin'; 
require 'session_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sanad | Admin Dashboard</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <header class="sn-nav">
    <div class="sn-container sn-nav__inner">
      <a href="admin-dashboard.php" class="sn-nav__logo">
        <img src="images/slogo.png" alt="Sanad Logo" class="sn-nav__logo-img" />
        <span class="sn-nav__logo-name">Sanad</span>
      </a>
      <ul class="sn-nav__links">
        <li><a href="admin-dashboard.php" class="sn-nav--active">Dashboard</a></li>
        <li><a href="review-requests.php">Review Requests</a></li>
        <li><a href="manage-users.php">Manage Users</a></li>
        <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
      </ul>
    </div>
  </header>

  <main class="sn-main ad-dashboard-page">
    <div class="sn-container">

        <section class="ad-hero"  > 
                <div class="ad-hero__overlay"></div>
        <div class="ad-hero__content">
          <span class="ad-hero__badge">Admin Dashboard</span>
          <h1 class="ad-hero__title">Good morning, Admin</h1>
          <p class="ad-hero__meta">Riyadh Administrative Panel</p>
        </div>
      </section>

      <div class="ad-stats">
        <div class="ad-stat-card">
          <div class="ad-stat-card__value">0</div>
          <div class="ad-stat-card__label">Pending requests</div>
        </div>
        <div class="ad-stat-card">
          <div class="ad-stat-card__value">0</div>
          <div class="ad-stat-card__label">Approved requests</div>
          <div class="ad-stat-card__period">Current total</div>
        </div>
        <div class="ad-stat-card">
          <div class="ad-stat-card__value">0</div>
          <div class="ad-stat-card__label">Rejected requests</div>
          <div class="ad-stat-card__period">Current total</div>
        </div>
        <div class="ad-stat-card">
          <div class="ad-stat-card__value">0</div>
          <div class="ad-stat-card__label">Blocked users</div>
          <div class="ad-stat-card__period">Current total</div>
        </div>
      </div>

      <div class="ad-table-card">
        <div class="ad-table-card__head">
          <span class="ad-table-card__head-title">Recent Pending Requests</span>
          <a href="review-requests.php" class="ad-more-link">More</a>
        </div>

        <table class="ad-table">
          <thead>
            <tr>
              <th>Medication</th>
              <th>Priority</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <!-- Recent requests will be loaded here -->
          </tbody>
        </table>
      </div>

      <div class="ad-table-card ad-table-card--users">
        <div class="ad-table-card__head">
          <span class="ad-table-card__head-title">Recent Users</span>
          <a href="manage-users.php" class="ad-more-link">More</a>
        </div>

        <table class="ad-table">
          <thead>
            <tr>
              <th>User</th>
              <th>Email</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <!-- Recent users will be loaded here -->
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <footer class="sn-footer">
    <div class="sn-container">
      <div class="sn-footer__inner">
        <span class="sn-footer__logo-name">Sanad</span>
        <span class="sn-footer__copy">© 2026 Sanad. Riyadh, Saudi Arabia.</span>
      </div>
    </div>
  </footer>

  <script src="admin.js"></script>
</body>
</html>