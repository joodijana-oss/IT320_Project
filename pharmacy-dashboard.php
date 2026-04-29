<?php
$required_role = 'pharmacy';
require 'session_check.php';
require 'db.php';

$pharmacy_id = $_SESSION['user_id'];

// Fetch pharmacy info
$stmt = $conn->prepare("SELECT pharmacy_name, zone, city FROM pharmacy WHERE pharmacy_id = ?");
$stmt->bind_param('i', $pharmacy_id);
$stmt->execute();
$stmt->bind_result($pharmacy_name, $zone, $city);
$stmt->fetch();
$stmt->close();

// Stats
$stmt = $conn->prepare("SELECT COUNT(*) FROM pharmacyoffer WHERE pharmacy_id = ?");
$stmt->bind_param('i', $pharmacy_id);
$stmt->execute();
$stmt->bind_result($total_offers);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM pharmacyoffer WHERE pharmacy_id = ? AND offer_status = 'Accepted'");
$stmt->bind_param('i', $pharmacy_id);
$stmt->execute();
$stmt->bind_result($confirmed_offers);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM pharmacyoffer WHERE pharmacy_id = ? AND offer_status = 'Pending'");
$stmt->bind_param('i', $pharmacy_id);
$stmt->execute();
$stmt->bind_result($pending_offers);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM medicationrequest WHERE request_status = 'Approved'");
$stmt->execute();
$stmt->bind_result($available_requests);
$stmt->fetch();
$stmt->close();

$recent = $conn->query("
    SELECT request_id, medication_name, priority_level, request_date
    FROM medicationrequest
    WHERE request_status = 'Approved'
    ORDER BY request_date DESC
    LIMIT 4
")->fetch_all(MYSQLI_ASSOC);

function priorityBadge($level) {
    $map = ['High' => 'ph-badge--high', 'Medium' => 'ph-badge--medium', 'Low' => 'ph-badge--low'];
    $cls = $map[$level] ?? 'ph-badge--low';
    return "<span class=\"ph-badge $cls\">$level</span>";
}

$today = date('l, j M Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sanad | Pharmacy Dashboard</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <header class="sn-nav">
    <div class="sn-container sn-nav__inner">
      <a href="pharmacy-dashboard.php" class="sn-nav__logo">
        <img src="images/slogo.png" alt="Sanad Logo" class="sn-nav__logo-img" />
        <span class="sn-nav__logo-name">Sanad</span>
      </a>
      <ul class="sn-nav__links">
        <li><a href="pharmacy-dashboard.php" class="sn-nav--active">Dashboard</a></li>
        <li><a href="pharmacy-viewRequests.php">Requests</a></li>
        <li><a href="pharmacy-reports.php">Reports</a></li>
        <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
      </ul>
    </div>
  </header>

  <main class="sn-main ph-dashboard-page">
    <div class="sn-container">

      <section class="ph-hero">
        <div class="ph-hero__overlay"></div>
        <div class="ph-hero__content">
          <span class="ph-hero__badge">Pharmacy Dashboard</span>
          <h1 class="ph-hero__title">Welcome back, <?= htmlspecialchars($pharmacy_name) ?></h1>
          <p class="ph-hero__meta"><?= htmlspecialchars($zone) ?> &nbsp;·&nbsp; <?= $today ?></p>
        </div>
      </section>

      <div class="ph-stats">
        <div class="ph-stat-card">
          <div class="ph-stat-card__value"><?= $available_requests ?></div>
          <div class="ph-stat-card__label">Available requests</div>
        </div>
        <div class="ph-stat-card" style="animation-delay:0.06s;">
          <div class="ph-stat-card__value"><?= $total_offers ?></div>
          <div class="ph-stat-card__label">Offers submitted</div>
          <div class="ph-stat-card__period">All time</div>
        </div>
        <div class="ph-stat-card" style="animation-delay:0.12s;">
          <div class="ph-stat-card__value"><?= $confirmed_offers ?></div>
          <div class="ph-stat-card__label">Confirmed offers</div>
          <div class="ph-stat-card__period">All time</div>
        </div>
        <div class="ph-stat-card" style="animation-delay:0.18s;">
          <div class="ph-stat-card__value"><?= $pending_offers ?></div>
          <div class="ph-stat-card__label">Pending offers</div>
          <div class="ph-stat-card__period">Awaiting response</div>
        </div>
      </div>

      <p class="ph-quick-label">Quick access</p>
      <div class="ph-quick-grid">
        <a href="pharmacy-viewRequests.php" class="ph-quick-card">
          <div class="ph-quick-card__icon">📋</div>
          <h2 class="ph-quick-card__title">View Requests</h2>
          <p class="ph-quick-card__text">Browse approved medication requests in your area and submit offers.</p>
          <span class="ph-quick-card__link">Browse Requests</span>
        </a>
        <a href="pharmacy-offers.php" class="ph-quick-card">
          <div class="ph-quick-card__icon">📤</div>
          <h2 class="ph-quick-card__title">My Offers</h2>
          <p class="ph-quick-card__text">View and manage all offers you have previously submitted.</p>
          <span class="ph-quick-card__link">View Offers</span>
        </a>
        <a href="pharmacy-reports.php" class="ph-quick-card">
          <div class="ph-quick-card__icon">📊</div>
          <h2 class="ph-quick-card__title">View Reports</h2>
          <p class="ph-quick-card__text">Track your offer activity and performance trends over time.</p>
          <span class="ph-quick-card__link">View Reports</span>
        </a>
      </div>

      <div class="ph-table-card">
        <div class="ph-table-card__head">
          <span class="ph-table-card__head-title">Recent Requests</span>
        </div>
        <table class="ph-table">
          <thead>
            <tr>
              <th>Medication</th>
              <th>Priority</th>
              <th>Date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent as $req): ?>
            <tr>
              <td>
                <div class="td-name"><?= htmlspecialchars($req['medication_name']) ?></div>
                <div class="td-id">#<?= $req['request_id'] ?></div>
              </td>
              <td><?= priorityBadge($req['priority_level']) ?></td>
              <td><?= date('j M Y', strtotime($req['request_date'])) ?></td>
              <td>
                <a href="pharmacy-request-details.php?id=<?= $req['request_id'] ?>" class="ph-view-link">View</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="ph-table-footer">
          <a href="pharmacy-viewRequests.php">View all requests →</a>
        </div>
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

  <script src="pharmacy.js"></script>
</body>
</html>