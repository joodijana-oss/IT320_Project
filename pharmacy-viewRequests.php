<?php
$required_role = 'pharmacy';
require 'session_check.php';
require 'db.php';

$pharmacy_id = $_SESSION['user_id'];

$sql = "
    SELECT r.request_id, r.medication_name, r.priority_level, r.request_date, r.city
    FROM medicationrequest r
    WHERE r.request_status = 'Approved'
    ORDER BY r.request_date DESC
";
$result = $conn->query($sql);
$requests = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

function priorityBadge($level) {
    $map = ['High' => 'ph-badge--high', 'Medium' => 'ph-badge--medium', 'Low' => 'ph-badge--low'];
    $cls = $map[$level] ?? 'ph-badge--low';
    return "<span class=\"ph-badge $cls\">$level</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sanad | View Requests</title>
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
        <li><a href="pharmacy-dashboard.php">Dashboard</a></li>
        <li><a href="pharmacy-viewRequests.php" class="sn-nav--active">Requests</a></li>
        <li><a href="pharmacy-reports.php">Reports</a></li>
        <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
      </ul>
    </div>
  </header>

  <main class="sn-main ph-requests-page">
    <div class="sn-container">
      <a href="pharmacy-dashboard.php" class="sn-back">← Back to Dashboard</a>

      <section class="ph-page-head">
        <span class="ph-page-head__badge">Pharmacy Requests</span>
        <h1 class="ph-page-head__title">View Requests</h1>
        <p class="ph-page-head__text">
          Browse approved medication requests in your assigned area and respond when the medication is available.
        </p>
      </section>

      <!-- Search + filters -->
      <div class="ph-toolbar">
        <div class="ph-search-wrap">
          <span class="ph-search-icon">🔍</span>
          <input
            type="text"
            id="requestSearchInput"
            placeholder="Search by medication name or request ID..."
            oninput="filterPharmacyRequests()"
          />
        </div>

        <div class="ph-toolbar__controls">
          <select id="priorityFilter" onchange="filterPharmacyRequests()">
            <option value="">All Priorities</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>

          <select id="sortRequests" onchange="sortPharmacyRequests()">
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="priority">Priority</option>
          </select>
        </div>
      </div>

      <p class="ph-count" id="phRequestsCount">Showing <strong><?= count($requests) ?></strong> requests</p>

      <div class="ph-table-wrap">
        <table class="ph-table">
          <thead>
            <tr>
              <th>Medication</th>
              <th>Request ID</th>
              <th>Priority</th>
              <th>City</th>
              <th>Date submitted</th>
              <th></th>
            </tr>
          </thead>

          <tbody id="phRequestsBody">
            <?php foreach ($requests as $req): ?>
            <tr
              data-med="<?= strtolower(htmlspecialchars($req['medication_name'])) ?>"
              data-id="<?= $req['request_id'] ?>"
              data-priority="<?= strtolower($req['priority_level']) ?>"
              data-date="<?= date('Y-m-d', strtotime($req['request_date'])) ?>"
            >
              <td class="td-med"><?= htmlspecialchars($req['medication_name']) ?></td>
              <td class="td-id">#<?= $req['request_id'] ?></td>
              <td><?= priorityBadge($req['priority_level']) ?></td>
              <td><?= htmlspecialchars($req['city']) ?></td>
              <td><?= date('j M Y', strtotime($req['request_date'])) ?></td>
              <td>
                <a href="pharmacy-request-details.php?id=<?= $req['request_id'] ?>" class="ph-view-link">
                  View request
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="ph-empty" id="phRequestsEmpty" style="display:none;">
          No requests match your search or selected filters.
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