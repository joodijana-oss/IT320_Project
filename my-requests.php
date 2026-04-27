<?php
session_start();

// ── SESSION GUARD ───────────────────────────────────────────
// TODO: replace 'patient' with whatever value your friend stores in $_SESSION['role']
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: login.html');
    exit;
}

$patient_id = $_SESSION['user_id'];

// ── DB CONNECTION ───────────────────────────────────────────
require_once 'db.php';

// ── FETCH THIS PATIENT'S REQUESTS ──────────────────────────
$sql = "SELECT * FROM medicationrequest WHERE patient_id = ? ORDER BY request_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$requests = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sanad — My Requests</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ── Navbar ── -->
<nav class="sn-nav">
  <div class="sn-container">
    <div class="sn-nav__inner">
      <a href="index.html" class="sn-nav__logo">
        <img class="sn-nav__logo-img" src="images/slogo.png" alt="Sanad logo">
        <span class="sn-nav__logo-name">Sanad</span>
      </a>
      <ul class="sn-nav__links">
        <li><a href="user-dashboard.html">Dashboard</a></li>
        <li><a href="profile.html">Profile</a></li>
        <li><a href="submit-request.html">Submit Request</a></li>
        <li><a href="my-requests.php" class="sn-nav--active">My Requests</a></li>
        <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ── Main ── -->
<main class="sn-main">
  <div class="sn-container">
    <a href="user-dashboard.html" class="sn-back">← Back to Dashboard</a>

    <div class="requests-page__header">
      <div>
        <h1 class="requests-page__title">My Requests</h1>
        <p class="requests-page__subtitle">Track your medication requests and pharmacy responses</p>
      </div>
      <a href="submit-request.html" class="req-submit-btn">+ New request</a>
    </div>

    <!-- ── Request Cards ── -->
    <div id="requests-list">

    <?php if ($requests->num_rows === 0): ?>
      <!-- Empty state -->
      <div class="req-empty">
        <div class="req-empty__icon">📋</div>
        <h2 class="req-empty__title">No requests yet</h2>
        <p class="req-empty__text">Submit your first medication request to get started.</p>
        <a href="submit-request.html" class="req-submit-btn">Submit a request</a>
      </div>

    <?php else: ?>
      <?php while ($req = $requests->fetch_assoc()): ?>

        <?php
          // Check if this request has any pending offers
          $req_id = $req['request_id'];
          $offer_check = $conn->prepare("SELECT COUNT(*) as cnt FROM pharmacyoffer WHERE request_id = ? AND offer_status = 'Pending'");
          $offer_check->bind_param("i", $req_id);
          $offer_check->execute();
          $offer_result = $offer_check->get_result()->fetch_assoc();
          $has_pending_offers = $offer_result['cnt'] > 0;

          // Map status to badge CSS class
          $status = $req['request_status'];
          $status_class = strtolower($status); // pending, approved, rejected, confirmed
        ?>

        <div class="req-card" data-id="<?= $req_id ?>">
          <div class="req-card__top">
            <div>
              <div class="req-card__name">Request #<?= $req_id ?></div>
              <div class="req-card__badges">
                <span class="req-badge req-badge--<?= $status_class ?>"><?= $status ?></span>
                <span class="req-badge req-badge--<?= strtolower($req['priority_level']) ?>"><?= $req['priority_level'] ?></span>
              </div>
            </div>

            <div class="req-card__actions">
              <a href="user-request-details.php?request_id=<?= $req_id ?>" class="req-view-link">View</a>

              <?php if ($status === 'Pending'): ?>
                <button class="req-btn req-btn--danger"
                  onclick="openDeleteModal(<?= $req_id ?>, '<?= htmlspecialchars($req['medication_name']) ?>')">
                  Delete
                </button>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($status === 'Approved' && $has_pending_offers): ?>
          <div class="req-card__offer-strip">
            <span>New pharmacy offer available</span>
            <a href="patient-offers.php?request_id=<?= $req_id ?>">View &amp; respond →</a>
          </div>
          <?php endif; ?>

          <div class="req-card__footer">
            <?= htmlspecialchars($req['medication_name']) ?>
          </div>
        </div>

      <?php endwhile; ?>
    <?php endif; ?>

    </div>
  </div>
</main>

<!-- ── Delete Modal ── -->
<div class="req-modal-overlay" id="delete-modal">
  <div class="req-modal">
    <h2 class="req-modal__title">Delete this request?</h2>
    <p class="req-modal__body">
      You are about to delete <strong id="modal-med-name"></strong>.
      This cannot be undone.
    </p>
    <div class="req-modal__actions">
      <button class="req-modal__cancel" onclick="closeDeleteModal()">Cancel</button>
      <form method="POST" action="delete-request.php" style="display:inline;">
        <input type="hidden" name="request_id" id="modal-request-id">
        <button type="submit" class="req-btn req-btn--danger">Yes, delete</button>
      </form>
    </div>
  </div>
</div>

<!-- ── Footer ── -->
<footer class="sn-footer">
  <div class="sn-container">
    <div class="sn-footer__inner">
      <span class="sn-footer__logo-name">Sanad</span>
      <span class="sn-footer__copy">© 2026 Sanad. Riyadh, Saudi Arabia.</span>
    </div>
  </div>
</footer>

<script>
function openDeleteModal(id, name) {
  document.getElementById('modal-med-name').textContent = name;
  document.getElementById('modal-request-id').value = id;
  document.getElementById('delete-modal').style.display = 'flex';
}
function closeDeleteModal() {
  document.getElementById('delete-modal').style.display = 'none';
}
</script>

</body>
</html>
