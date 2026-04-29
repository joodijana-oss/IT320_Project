<?php
$required_role = 'pharmacy';
require 'session_check.php';
require 'db.php';

$pharmacy_id = $_SESSION['user_id'];

$period = $_GET['period'] ?? '30d';
if (!in_array($period, ['7d','30d','90d','all'])) $period = '30d';

function dateFilter($period) {
    switch ($period) {
        case '7d':  return "AND offer_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        case '30d': return "AND offer_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        case '90d': return "AND offer_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
        default:    return "";
    }
}
function prevDateFilter($period) {
    switch ($period) {
        case '7d':  return "AND offer_date >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND offer_date < DATE_SUB(NOW(), INTERVAL 7 DAY)";
        case '30d': return "AND offer_date >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND offer_date < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        case '90d': return "AND offer_date >= DATE_SUB(NOW(), INTERVAL 180 DAY) AND offer_date < DATE_SUB(NOW(), INTERVAL 90 DAY)";
        default:    return "AND offer_date < DATE_SUB(NOW(), INTERVAL 365 DAY)";
    }
}

$df   = dateFilter($period);
$prev = prevDateFilter($period);

// Current period
$row = $conn->query("SELECT COUNT(*) AS total,
    SUM(offer_status='Accepted') AS confirmed,
    SUM(offer_status='Rejected') AS rejected,
    SUM(offer_status='Pending')  AS pending
    FROM pharmacyoffer WHERE pharmacy_id=$pharmacy_id $df")->fetch_assoc();

$total     = (int)$row['total'];
$confirmed = (int)$row['confirmed'];
$rejected  = (int)$row['rejected'];
$pending   = (int)$row['pending'];
$rate      = $total > 0 ? round($confirmed / $total * 100) : 0;

// Previous period
$prev_row = $conn->query("SELECT COUNT(*) AS total,
    SUM(offer_status='Accepted') AS confirmed,
    SUM(offer_status='Rejected') AS rejected
    FROM pharmacyoffer WHERE pharmacy_id=$pharmacy_id $prev")->fetch_assoc();

$prev_total     = (int)$prev_row['total'];
$prev_confirmed = (int)$prev_row['confirmed'];
$prev_rejected  = (int)$prev_row['rejected'];
$prev_rate      = $prev_total > 0 ? round($prev_confirmed / $prev_total * 100) : 0;

function delta($curr, $prev) {
    if ($prev == 0 && $curr == 0) return "<span class='ph-stat-card__delta delta-neu'>— No data</span>";
    if ($prev == 0) return "<span class='ph-stat-card__delta delta-up'>↑ New activity</span>";
    $pct = round(($curr - $prev) / $prev * 100);
    if ($pct > 0)  return "<span class='ph-stat-card__delta delta-up'>↑ {$pct}% vs prev period</span>";
    if ($pct < 0)  return "<span class='ph-stat-card__delta delta-down'>↓ " . abs($pct) . "% vs prev period</span>";
    return "<span class='ph-stat-card__delta delta-neu'>— Same as prev period</span>";
}

// Chart buckets
$weeks = [];
if ($period === '7d') {
    for ($i = 6; $i >= 0; $i--) {
        $label = date('D', strtotime("-$i days"));
        $date  = date('Y-m-d', strtotime("-$i days"));
        $r = $conn->query("SELECT COUNT(*) AS total, SUM(offer_status='Accepted') AS confirmed
            FROM pharmacyoffer WHERE pharmacy_id=$pharmacy_id AND DATE(offer_date)='$date'")->fetch_assoc();
        $weeks[] = ['label' => $label, 'submitted' => (int)$r['total'], 'confirmed' => (int)$r['confirmed']];
    }
} else {
    $num_weeks = ($period === '90d') ? 12 : 4;
    for ($i = $num_weeks - 1; $i >= 0; $i--) {
        $start = date('Y-m-d', strtotime("-" . (($i + 1) * 7) . " days"));
        $end   = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
        $wnum  = $num_weeks - $i;
        $r = $conn->query("SELECT COUNT(*) AS total, SUM(offer_status='Accepted') AS confirmed
            FROM pharmacyoffer WHERE pharmacy_id=$pharmacy_id
            AND offer_date >= '$start' AND offer_date < '$end'")->fetch_assoc();
        $weeks[] = ['label' => "W$wnum", 'submitted' => (int)$r['total'], 'confirmed' => (int)$r['confirmed']];
    }
}

$chart_labels    = json_encode(array_column($weeks, 'label'));
$chart_submitted = json_encode(array_column($weeks, 'submitted'));
$chart_confirmed = json_encode(array_column($weeks, 'confirmed'));
$donut_data      = json_encode([$confirmed, $rejected, $pending]);
$has_data        = $total > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sanad | Reports</title>
  <link rel="stylesheet" href="style.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
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
        <li><a href="pharmacy-viewRequests.php">Requests</a></li>
        <li><a href="pharmacy-reports.php" class="sn-nav--active">Reports</a></li>
        <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
      </ul>
    </div>
  </header>

  <main class="sn-main ph-reports-page">
    <div class="sn-container">
      <a href="pharmacy-dashboard.php" class="sn-back">← Back to Dashboard</a>

      <section class="ph-page-head">
        <span class="ph-page-head__badge">Performance reports</span>
        <h1 class="ph-page-head__title">Reports</h1>
        <p class="ph-page-head__text">
          Track your offer activity, confirmation rate, and performance trends over time.
        </p>
      </section>

      <div class="ph-period-bar">
        <a href="?period=7d"  class="ph-pill <?= $period==='7d'  ? 'active':'' ?>">Last 7 days</a>
        <a href="?period=30d" class="ph-pill <?= $period==='30d' ? 'active':'' ?>">Last 30 days</a>
        <a href="?period=90d" class="ph-pill <?= $period==='90d' ? 'active':'' ?>">Last 3 months</a>
        <a href="?period=all" class="ph-pill <?= $period==='all' ? 'active':'' ?>">All time</a>
      </div>

      <div class="ph-stat-row">
        <div class="ph-stat-card">
          <div class="ph-stat-card__value"><?= $total ?></div>
          <div class="ph-stat-card__label">Offers submitted</div>
          <?= delta($total, $prev_total) ?>
          <a href="pharmacy-offers.php" class="ph-offers-link">View all offers →</a>
        </div>
        <div class="ph-stat-card">
          <div class="ph-stat-card__value"><?= $confirmed ?></div>
          <div class="ph-stat-card__label">Confirmed offers</div>
          <?= delta($confirmed, $prev_confirmed) ?>
        </div>
        <div class="ph-stat-card">
          <div class="ph-stat-card__value"><?= $rejected ?></div>
          <div class="ph-stat-card__label">Not selected</div>
          <?= delta($rejected, $prev_rejected) ?>
        </div>
        <div class="ph-stat-card">
          <div class="ph-stat-card__value"><?= $rate ?>%</div>
          <div class="ph-stat-card__label">Confirmation rate</div>
          <?= delta($rate, $prev_rate) ?>
        </div>
      </div>

      <?php if (!$has_data): ?>
      <div class="ph-no-data">
        No data available for the selected period.
      </div>
      <?php else: ?>
      <div id="chartsSection">
        <div class="ph-charts-main">
          <div class="ph-chart-card">
            <div class="ph-chart-card__title">Offer Activity</div>
            <div class="ph-chart-card__sub">Submitted vs confirmed offers over time</div>
            <div class="ph-chart-wrap-lg">
              <canvas id="actChart"></canvas>
            </div>
          </div>
          <div class="ph-chart-card">
            <div class="ph-chart-card__title">Offer Outcomes</div>
            <div class="ph-chart-card__sub">Breakdown of all submitted offers</div>
            <div class="ph-chart-wrap">
              <canvas id="donutChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

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

  <?php if ($has_data): ?>
  <script>
    new Chart(document.getElementById('actChart'), {
      type: 'bar',
      data: {
        labels: <?= $chart_labels ?>,
        datasets: [
          { label: 'Submitted', data: <?= $chart_submitted ?>, backgroundColor: 'rgba(99,155,210,0.25)', borderRadius: 4 },
          { label: 'Confirmed', data: <?= $chart_confirmed ?>, backgroundColor: '#2d6a4f', borderRadius: 4 }
        ]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
      }
    });

    new Chart(document.getElementById('donutChart'), {
      type: 'doughnut',
      data: {
        labels: ['Confirmed', 'Not selected', 'Pending'],
        datasets: [{ data: <?= $donut_data ?>, backgroundColor: ['#2d6a4f','#9b2c2c','#b7791f'], borderWidth: 0 }]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
  </script>
  <?php endif; ?>

  <script src="pharmacy.js"></script>
</body>
</html>