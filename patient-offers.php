<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$required_role = 'patient';
require 'session_check.php';
require_once 'db.php';

$patient_id = $_SESSION['user_id'];

// ── GET REQUEST ID FROM URL ─────────────────────────────────
if (!isset($_GET['request_id'])) {
    header('Location: my-requests.php');
    exit;
}
$request_id = intval($_GET['request_id']);

// ── FETCH REQUEST (make sure it belongs to this patient) ────
$req_stmt = $conn->prepare(
    "SELECT * FROM medicationrequest WHERE request_id = ? AND patient_id = ?"
);
$req_stmt->bind_param("ii", $request_id, $patient_id);
$req_stmt->execute();
$request = $req_stmt->get_result()->fetch_assoc();

if (!$request) {
    header('Location: my-requests.php');
    exit;
}

// ── FETCH OFFERS WITH PHARMACY INFO ────────────────────────
// Note: pharmacyoffer has no price column in your DB — using message only
$offers_stmt = $conn->prepare("
    SELECT po.offer_id, po.offer_status, po.message, po.offer_date,
           p.pharmacy_name, p.address, p.zone, p.phone
    FROM pharmacyoffer po
    JOIN pharmacy p ON po.pharmacy_id = p.pharmacy_id
    WHERE po.request_id = ?
    ORDER BY po.offer_date ASC
");
$offers_stmt->bind_param("i", $request_id);
$offers_stmt->execute();
$offers = $offers_stmt->get_result();

$is_confirmed = $request['request_status'] === 'Confirmed';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sanad | Pharmacy Offers</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <header class="sn-nav">
    <div class="sn-container sn-nav__inner">
      <a href="index.php" class="sn-nav__logo">
        <img src="images/slogo.png" alt="Sanad Logo" class="sn-nav__logo-img" />
        <span class="sn-nav__logo-name">Sanad</span>
      </a>
      <ul class="sn-nav__links">
        <li><a href="user-dashboard.php">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="submit-request.php">Submit Request</a></li>
        <li><a href="my-requests.php" class="sn-nav--active">My Requests</a></li>
        <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
      </ul>
    </div>
  </header>

  <main class="sn-main patient-offers-page">
    <div class="sn-container">

      <a href="my-requests.php" class="sn-back">← Back to My Requests</a>

      <section class="admin-page-head admin-page-head--small">
        <span class="admin-page-head__badge">Pharmacy offers</span>
        <h1 class="admin-page-head__title">Offers for Your Request</h1>
        <p class="admin-page-head__text">
          Review offers from pharmacies and accept the one that suits you best.
        </p>
      </section>

      <!-- Request summary strip -->
      <div class="offer-req-strip">
        <div>
          <div class="offer-req-strip__title"><?= htmlspecialchars($request['medication_name']) ?></div>
          <div class="offer-req-strip__meta">
            <span>Request #<?= $request['request_id'] ?></span>
            <span><?= htmlspecialchars($request['zone']) ?></span>
            <span><?= date('j M Y', strtotime($request['request_date'])) ?></span>
          </div>
        </div>
        <div class="offer-req-strip__badges">
          <span class="request-card__status request-card__status--<?= strtolower($request['request_status']) ?>">
            <?= htmlspecialchars($request['request_status']) ?>
          </span>
          <span class="request-card__status" style="background:#fff3cd;color:#8a6d1f;">
            <?= htmlspecialchars($request['priority_level']) ?>
          </span>
        </div>
      </div>

      <!-- Confirmed bar -->
      <?php if ($is_confirmed): ?>
      <div class="offer-confirmed-bar">
        ✓ &nbsp;You accepted an offer. This request is now <strong>Confirmed</strong> — no further offers can be accepted.
      </div>
      <?php endif; ?>

      <!-- Offer cards -->
      <div id="offersList">

        <?php if ($offers->num_rows === 0): ?>
          <div class="offer-empty">
            No offers yet. Pharmacies in your area haven't responded to this request.
          </div>

        <?php else: ?>
          <?php while ($offer = $offers->fetch_assoc()): ?>

            <div class="offer-card">
              <div class="offer-card__top">
                <div class="offer-pharm-row">
                  <div class="offer-pharm-avatar">
                    <div style="width:44px;height:44px;border-radius:50%;background:#e8f0e9;display:flex;align-items:center;justify-content:center;font-weight:600;color:#2d6a4f;">
                      <?= strtoupper(substr($offer['pharmacy_name'], 0, 2)) ?>
                    </div>
                  </div>
                  <div>
                    <div class="offer-pharm-name"><?= htmlspecialchars($offer['pharmacy_name']) ?></div>
                    <div class="offer-pharm-sub"><?= htmlspecialchars($offer['zone']) ?></div>
                  </div>
                </div>

                <?php if ($offer['offer_status'] === 'Accepted'): ?>
                  <span class="req-badge req-badge--confirmed">✓ Accepted</span>
                <?php elseif ($offer['offer_status'] === 'Rejected'): ?>
                  <span class="req-badge req-badge--rejected">Rejected</span>
                <?php endif; ?>
              </div>

              <div class="offer-data-grid">
                <div>
                  <div class="offer-data-item__label">Offer date</div>
                  <div class="offer-data-item__value">
                    <?= date('j M Y', strtotime($offer['offer_date'])) ?>
                  </div>
                </div>
              </div>

              <?php if ($offer['message']): ?>
              <p class="offer-note">"<?= htmlspecialchars($offer['message']) ?>"</p>
              <?php endif; ?>

              <!-- Accept/Reject buttons — only if offer is Pending and request not Confirmed -->
              <?php if ($offer['offer_status'] === 'Pending' && !$is_confirmed): ?>
              <div class="offer-actions-row">
                <div style="margin-left:auto;display:flex;gap:10px;">

                  <form method="POST" action="process-offer-response.php">
                    <input type="hidden" name="offer_id" value="<?= $offer['offer_id'] ?>">
                    <input type="hidden" name="request_id" value="<?= $request_id ?>">
                    <input type="hidden" name="action" value="accept">
                    <button type="submit" class="admin-btn admin-btn--approve admin-btn--sm">Accept Offer</button>
                  </form>

                  <form method="POST" action="process-offer-response.php">
                    <input type="hidden" name="offer_id" value="<?= $offer['offer_id'] ?>">
                    <input type="hidden" name="request_id" value="<?= $request_id ?>">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="admin-btn admin-btn--secondary admin-btn--sm">Reject</button>
                  </form>

                </div>
              </div>
              <?php endif; ?>

            </div>

          <?php endwhile; ?>
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

</body>
</html>