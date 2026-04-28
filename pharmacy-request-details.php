<?php
$required_role = 'pharmacy';
require 'session_check.php';
require 'db.php';

$pharmacy_id = $_SESSION['user_id'];

// Get request_id from URL
$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle offer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_offer'])) {
    $price   = floatval($_POST['price'] ?? 0);
    $notes   = trim($_POST['notes'] ?? '');
    $message = $notes !== '' ? $notes : 'The medication is available at our pharmacy.';

    // Check if pharmacy already submitted an offer for this request
    $check = $conn->prepare("SELECT offer_id FROM pharmacyoffer WHERE request_id = ? AND pharmacy_id = ?");
    $check->bind_param('ii', $request_id, $pharmacy_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->close();
        $error = 'You have already submitted an offer for this request.';
    } else {
        $check->close();
        $stmt = $conn->prepare(
            "INSERT INTO pharmacyoffer (request_id, pharmacy_id, offer_status, message, offer_date)
             VALUES (?, ?, 'Pending', ?, NOW())"
        );
        $stmt->bind_param('iis', $request_id, $pharmacy_id, $message);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: pharmacy-reports.php?offer_submitted=1');
            exit;
        } else {
            $stmt->close();
            $error = 'Failed to submit offer. Please try again.';
        }
    }
}

// Fetch request details
$request = null;
if ($request_id > 0) {
    $stmt = $conn->prepare(
        "SELECT r.request_id, r.medication_name, r.priority_level, r.request_date,
                r.city, r.notes, r.prescription_file, r.request_status,
                p.full_name AS patient_name
         FROM medicationrequest r
         JOIN patient p ON p.patient_id = r.patient_id
         WHERE r.request_id = ? AND r.request_status = 'Approved'"
    );
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    $stmt->close();
}

// Check if already offered
$already_offered = false;
if ($request_id > 0) {
    $chk = $conn->prepare("SELECT offer_id FROM pharmacyoffer WHERE request_id = ? AND pharmacy_id = ?");
    $chk->bind_param('ii', $request_id, $pharmacy_id);
    $chk->execute();
    $chk->store_result();
    $already_offered = $chk->num_rows > 0;
    $chk->close();
}

// Priority badge helper
function priorityBadge($level) {
    $map = [
        'High'   => 'ph-badge--high',
        'Medium' => 'ph-badge--medium',
        'Low'    => 'ph-badge--low',
    ];
    $cls = $map[$level] ?? 'ph-badge--low';
    return "<span class=\"ph-badge $cls\">$level</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sanad | Request Details</title>
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

  <main class="sn-main ph-rd-page">
    <div class="sn-container">

      <a href="pharmacy-viewRequests.php" class="sn-back">← Back to Requests</a>

      <?php if (!$request): ?>
        <section class="ph-page-head">
          <h1 class="ph-page-head__title">Request Not Found</h1>
          <p class="ph-page-head__text">This request does not exist or is not available.</p>
        </section>
      <?php else: ?>

      <!-- Page hero header -->
      <section class="ph-page-head">
        <span class="ph-page-head__badge">Request details</span>
        <h1 class="ph-page-head__title">Medication Request #<?= $request['request_id'] ?></h1>
        <p class="ph-page-head__text">
          Review the request information and submit an offer if the medication is available.
        </p>
      </section>

      <?php if (isset($error)): ?>
        <div class="ph-alert ph-alert--error" style="margin-bottom:16px;padding:12px 16px;background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;color:#b91c1c;">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <div class="ph-details-layout">

        <!-- Left: request info -->
        <div>

          <div class="ph-details-card" style="margin-bottom:22px;">
            <h2 class="ph-details-card__title">Request Information</h2>
            <div class="ph-details-grid">

              <div class="ph-details-item">
                <span class="ph-details-item__label">Medication Name</span>
                <span class="ph-details-item__value"><?= htmlspecialchars($request['medication_name']) ?></span>
              </div>

              <div class="ph-details-item">
                <span class="ph-details-item__label">Priority</span>
                <span class="ph-details-item__value">
                  <?= priorityBadge($request['priority_level']) ?>
                </span>
              </div>

              <div class="ph-details-item">
                <span class="ph-details-item__label">Request Date</span>
                <span class="ph-details-item__value">
                  <?= date('j M Y', strtotime($request['request_date'])) ?>
                </span>
              </div>

              <div class="ph-details-item">
                <span class="ph-details-item__label">City</span>
                <span class="ph-details-item__value"><?= htmlspecialchars($request['city']) ?></span>
              </div>

              <div class="ph-details-item ph-details-item--full">
                <span class="ph-details-item__label">Notes</span>
                <span class="ph-details-item__value">
                  <?= htmlspecialchars($request['notes']) ?>
                </span>
              </div>

            </div>
          </div>

          <?php if ($request['prescription_file']): ?>
          <div class="ph-details-card">
            <h2 class="ph-details-card__title">Prescription</h2>
            <div class="ph-prescription-box">
              <div class="ph-prescription-box__icon">📄</div>
              <div class="ph-prescription-box__info">
                <h4><?= htmlspecialchars($request['prescription_file']) ?></h4>
                <p>Uploaded <?= date('j M Y', strtotime($request['request_date'])) ?> · Verified by admin</p>
                <a href="uploads/<?= htmlspecialchars($request['prescription_file']) ?>" target="_blank">View prescription →</a>
              </div>
            </div>
          </div>
          <?php endif; ?>

        </div>

        <!-- Right: offer panel -->
        <div>

          <?php if ($already_offered): ?>
          <!-- Already submitted -->
          <div class="ph-offer-panel" id="offerSent">
            <div class="ph-offer-panel__title">Offer Submitted</div>
            <p class="ph-offer-panel__sub">Your offer has been sent to the patient.</p>
            <div class="ph-closed-notice">
              <strong>Awaiting patient response</strong>
              <p>You'll be notified once the patient reviews all offers.</p>
            </div>
          </div>

          <?php else: ?>
          <!-- Offer form -->
          <div class="ph-offer-panel" id="offerForm">
            <h2 class="ph-offer-panel__title">Submit an Offer</h2>
            <p class="ph-offer-panel__sub">Respond to this request with your availability and price.</p>

            <form method="POST" action="pharmacy-request-details.php?id=<?= $request_id ?>">
              <input type="hidden" name="submit_offer" value="1" />

              <div class="ph-form-field">
                <label>Price (SAR)</label>
                <input type="number" name="price" id="priceInput" placeholder="0.00" min="0" step="0.01" required />
              </div>

              <div class="ph-form-field">
                <label>Notes <span style="color:var(--ph-text-faint);font-weight:300;">(optional)</span></label>
                <textarea name="notes" id="notesInput" rows="3" placeholder="Any additional information for the patient…"></textarea>
              </div>

              <div class="ph-offer-actions">
                <button type="submit" class="ph-btn ph-btn--submit">Submit Offer</button>
                <a href="pharmacy-viewRequests.php" class="ph-btn ph-btn--cancel">Cancel</a>
              </div>
            </form>
          </div>
          <?php endif; ?>

        </div>

      </div>

      <?php endif; ?>

    </div>
  </main>

  <div class="ph-toast-wrap" id="toasts"></div>

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