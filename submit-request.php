<?php
$required_role = 'patient'; 
require 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Request</title>
  <link rel="stylesheet" href="style.css">
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
      <li><a href="submit-request.php" class="sn-nav--active">Submit Request</a></li>
      <li><a href="my-requests.php">My Requests</a></li>
      <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
    </ul>
  </div>
</header>

<main class="sn-main submit-request-page">
  <div class="sn-container">
    <a href="user-dashboard.php" class="sn-back">← Back to Dashboard</a>

    <section class="admin-page-head admin-page-head--small">
      <span class="admin-page-head__badge">New Request</span>
      <h1 class="admin-page-head__title">Submit Medication Request</h1>
      <p class="admin-page-head__text">
        Fill all required fields and upload prescription.
      </p>
    </section>

    <?php if (isset($_GET['error'])): ?>
      <div class="success-box" style="display:block; background:#fde8e8; color:#8b2020; border:1px solid #f5c2c2;">
        <?php echo htmlspecialchars($_GET['error']); ?>
      </div>
    <?php endif; ?>

    <form id="requestForm" class="submit-card" method="POST" action="process-submit-request.php" enctype="multipart/form-data">

      <div class="form-grid">

        <div class="form-field">
          <label>Medication Name</label>
          <input type="text" id="medication" name="medication_name" required>
          <span class="error-msg"></span>
        </div>

        <div class="form-field">
          <label>Priority</label>
          <select id="priority" name="priority_level" required>
            <option value="">Select</option>
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
          </select>
          <span class="error-msg"></span>
        </div>

        <div class="form-field">
          <label>City</label>
          <select name="city" readonly>
            <option value="Riyadh">Riyadh</option>
          </select>
          <span class="error-msg"></span>
        </div>
        
        <div class="form-field">
          <label>Zone</label>
          <select id="zone" name="zone" required>
            <option value="">Select Zone</option>
            <option value="North Riyadh">North Riyadh</option>
            <option value="South Riyadh">South Riyadh</option>
            <option value="East Riyadh">East Riyadh</option>
            <option value="West Riyadh">West Riyadh</option>
          </select>
          <span class="error-msg"></span>
        </div>

        <div class="form-field form-full">
          <label>Notes</label>
          <textarea id="notes" name="notes" rows="4"></textarea>
        </div>

        <div class="form-field form-full">
          <label>Upload Prescription</label>
          <input type="file" id="file" name="prescription_file" accept="image/*,.pdf" required>
          <span class="error-msg"></span>
        </div>

      </div>

      <button type="submit" class="admin-btn admin-btn--primary">
        Submit Request
      </button>

    </form>
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

<script src="valdiation.js"></script>
</body>
</html>