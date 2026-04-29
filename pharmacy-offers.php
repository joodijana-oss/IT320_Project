<?php
$required_role = 'pharmacy';
require 'session_check.php';
require 'db.php';

$pharmacy_id = $_SESSION['user_id'];

$flash = '';
if (isset($_GET['offer_submitted']) && $_GET['offer_submitted'] === '1') {
    $flash = 'Your offer has been submitted successfully.';
}

$stmt = $conn->prepare("
    SELECT
        o.offer_id,
        o.request_id,
        o.offer_status,
        o.message,
        o.offer_date,
        r.medication_name,
        r.priority_level
    FROM pharmacyoffer o
    JOIN medicationrequest r ON r.request_id = o.request_id
    WHERE o.pharmacy_id = ?
    ORDER BY o.offer_date DESC
");
$stmt->bind_param('i', $pharmacy_id);
$stmt->execute();
$result = $stmt->get_result();
$offers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function statusBadge($status) {
    $map = [
        'Pending'  => 'ph-badge--pending',
        'Accepted' => 'ph-badge--confirmed',
        'Rejected' => 'ph-badge--rejected',
    ];
    $label = [
        'Pending'  => 'Pending',
        'Accepted' => 'Confirmed',
        'Rejected' => 'Not selected',
    ];
    $cls = $map[$status] ?? 'ph-badge--pending';
    $lbl = $label[$status] ?? $status;
    return "<span class=\"ph-badge $cls\">$lbl</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sanad | My Offers</title>
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
        <li><a href="pharmacy-viewRequests.php">Requests</a></li>
        <li><a href="pharmacy-reports.php" class="sn-nav--active">Reports</a></li>
        <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
      </ul>
    </div>
  </header>

  <main class="sn-main ph-offers-page">
    <div class="sn-container">

      <a href="pharmacy-dashboard.php" class="sn-back">← Back to Dashboard</a>

      <section class="ph-page-head">
        <span class="ph-page-head__badge">Offer history</span>
        <h1 class="ph-page-head__title">My Offers</h1>
        <p class="ph-page-head__text">
          View all offers you have submitted to medication requests.
        </p>
      </section>

      <?php if ($flash): ?>
        <div style="margin-bottom:18px;padding:12px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;color:#166534;font-weight:500;">
          ✓ <?= htmlspecialchars($flash) ?>
        </div>
      <?php endif; ?>

      <div class="ph-search-wrap">
        <span class="ph-search-icon">🔍</span>
        <input type="text" id="searchInput" placeholder="Search by medication name or request ID…" oninput="filterOffers()" />
      </div>

      <p class="ph-count" id="countLabel">Showing <strong><?= count($offers) ?></strong> offers</p>

      <div class="ph-table-wrap">
        <table class="ph-table">
          <thead>
            <tr>
              <th>Medication</th>
              <th>Request</th>
              <th>Date submitted</th>
              <th>Message</th>
              <th>Outcome</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="offersBody">
            <?php foreach ($offers as $offer): ?>
            <tr
              data-med="<?= strtolower(htmlspecialchars($offer['medication_name'])) ?>"
              data-id="<?= $offer['request_id'] ?>"
            >
              <td class="td-med"><?= htmlspecialchars($offer['medication_name']) ?></td>
              <td class="td-id">#<?= $offer['request_id'] ?></td>
              <td><?= date('j M Y', strtotime($offer['offer_date'])) ?></td>
              <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($offer['message']) ?>">
                <?= htmlspecialchars(mb_strimwidth($offer['message'], 0, 40, '…')) ?>
              </td>
              <td><?= statusBadge($offer['offer_status']) ?></td>
              <td>
                <a href="pharmacy-request-details.php?id=<?= $offer['request_id'] ?>" class="ph-view-link">
                  View request
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <?php if (empty($offers)): ?>
        <div class="ph-empty" id="emptyState">
          You haven't submitted any offers yet.
        </div>
        <?php else: ?>
        <div class="ph-empty" id="emptyState" style="display:none;">
          No offers match your search.
        </div>
        <?php endif; ?>
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