
<?php
$required_role = 'admin'; 
require 'session_check.php';
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
      <a href="admin-dashboard.php" class="sn-nav__logo">
        <img src="images/slogo.png" alt="Sanad Logo" class="sn-nav__logo-img" />
        <span class="sn-nav__logo-name">Sanad</span>
      </a>
      <ul class="sn-nav__links">
        <li><a href="admin-dashboard.php">Dashboard</a></li>
        <li><a href="review-requests.php" class="sn-nav--active">Review Requests</a></li>
        <li><a href="manage-users.php">Manage Users</a></li>
        <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
      </ul>
    </div>
  </header>

  <main class="sn-main ad-rd-page">
    <div class="sn-container">

      <a href="review-requests.php" class="sn-back">← Back to Review Requests</a>

      <!-- Page hero header -->
      <section class="admin-page-head">
        <span class="admin-page-head__badge">Request details</span>
        <h1 class="admin-page-head__title">Medication Request #1024</h1>
        <p class="admin-page-head__text">
          Review the request information, check the uploaded prescription, and decide whether the request should be approved or rejected.
        </p>
      </section>

      <div class="ad-details-layout">

        <!-- Left: request info -->
        <div>

          <div class="ad-details-card" style="margin-bottom:22px;">
            <h2 class="ad-details-card__title">Request Information</h2>
            <div class="ad-details-grid">

              <div class="ad-details-item">
                <span class="ad-details-item__label">Medication Name</span>
                <span class="ad-details-item__value">Augmentin 625mg</span>
              </div>

              <div class="ad-details-item">
                <span class="ad-details-item__label">Priority</span>
                <span class="ad-details-item__value">
                  <span class="ad-badge ad-badge--high">High</span>
                </span>
              </div>

              <div class="ad-details-item">
                <span class="ad-details-item__label">Request Date</span>
                <span class="ad-details-item__value">27 Mar 2026</span>
              </div>

              <div class="ad-details-item">
  <span class="ad-details-item__label">Patient Name</span>
  <span class="ad-details-item__value">Sarah Ahmed</span>
</div>

<div class="ad-details-item">
  <span class="ad-details-item__label">City</span>
  <span class="ad-details-item__value">Riyadh</span>
</div>

<div class="ad-details-item">
  <span class="ad-details-item__label">Zone</span>
  <span class="ad-details-item__value">North Riyadh</span>
</div>

              <div class="ad-details-item ad-details-item--full">
                <span class="ad-details-item__label">Submitted Note</span>
                <span class="ad-details-item__value">
                  I need this medication as soon as possible because I use it regularly and I am running out.
                </span>
              </div>

            </div>
          </div>

          <div class="ad-details-card">
            <h2 class="ad-details-card__title">Prescription</h2>
            <div class="ad-prescription-box">
              <div class="ad-prescription-box__icon">📄</div>
              <div class="ad-prescription-box__info">
                <h4>Prescription_1024.pdf</h4>
                <p>Uploaded 27 Mar 2026</p>
                <a href="images/prescription.png">View prescription →</a>
              </div>
            </div>
          </div>

        </div>

        <!-- Right: admin review panel -->
        <div>

          <div class="ad-review-panel" id="reviewForm">
            <h2 class="ad-review-panel__title">Review Decision</h2>
            <p class="ad-review-panel__sub">
              Select the appropriate action after reviewing the request and attached prescription.
            </p>

            <div class="ad-form-field">
              <label for="reviewReason">Reason <span style="color:var(--sn-text-faint);font-weight:300;">(optional)</span></label>
              <textarea id="reviewReason" rows="4" placeholder="Add a short note or reason if needed..."></textarea>
            </div>

            <div class="ad-review-actions">
              <button class="admin-btn admin-btn--approve" onclick="submitDecision('Approved')">Approve</button>
              <button class="admin-btn admin-btn--reject" onclick="submitDecision('Rejected')">Reject</button>
            </div>
          </div>

          <div class="ad-review-panel" id="reviewDone" style="display:none;">
            <div class="ad-review-panel__title">Decision Submitted</div>
            <p class="ad-review-panel__sub">The request has been reviewed successfully.</p>

            <div class="ad-closed-notice">
              <strong>Status Updated</strong>
              <p id="decisionText">The request status has been updated.</p>
            </div>

            <div class="ad-submitted-row">
              <span>Final Decision</span>
              <span id="finalDecision">—</span>
            </div>
          </div>

        </div>

      </div>

    </div>
  </main>

  <div class="ad-toast-wrap" id="toasts"></div>

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