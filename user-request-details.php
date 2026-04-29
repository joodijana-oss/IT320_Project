<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$required_role = 'patient';
require 'session_check.php';
require_once 'db.php';

$patient_id = $_SESSION['user_id'];

if (!isset($_GET['request_id'])) {
    header('Location: my-requests.php');
    exit;
}

$request_id = intval($_GET['request_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medication_name = trim($_POST['medication_name'] ?? '');
    $priority_level  = trim($_POST['priority_level'] ?? '');
    $zone            = trim($_POST['zone'] ?? '');
    $notes           = trim($_POST['notes'] ?? '');

    $check = $conn->prepare("SELECT request_status, prescription_file FROM medicationrequest WHERE request_id = ? AND patient_id = ?");
    $check->bind_param("ii", $request_id, $patient_id);
    $check->execute();
    $current = $check->get_result()->fetch_assoc();

    if (!$current || $current['request_status'] !== 'Pending') {
        header("Location: user-request-details.php?request_id=$request_id&error=Only pending requests can be edited.");
        exit;
    }

    $prescription_file = $current['prescription_file'];

    if (isset($_FILES['prescription_file']) && $_FILES['prescription_file']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
        $ext = strtolower(pathinfo($_FILES['prescription_file']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            header("Location: user-request-details.php?request_id=$request_id&error=Only JPG, PNG, and PDF files are allowed.");
            exit;
        }

        $upload_dir = 'uploads/prescriptions/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $prescription_file = 'prescription_' . $patient_id . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['prescription_file']['tmp_name'], $upload_dir . $prescription_file);
    }

    $stmt = $conn->prepare("
        UPDATE medicationrequest
        SET medication_name = ?, priority_level = ?, zone = ?, notes = ?, prescription_file = ?
        WHERE request_id = ? AND patient_id = ? AND request_status = 'Pending'
    ");

    $stmt->bind_param(
        "sssssii",
        $medication_name,
        $priority_level,
        $zone,
        $notes,
        $prescription_file,
        $request_id,
        $patient_id
    );

    if ($stmt->execute()) {
        header("Location: user-request-details.php?request_id=$request_id&success=Changes saved successfully.");
        exit;
    } else {
        header("Location: user-request-details.php?request_id=$request_id&error=Could not save changes.");
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM medicationrequest WHERE request_id = ? AND patient_id = ?");
$stmt->bind_param("ii", $request_id, $patient_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    header('Location: my-requests.php');
    exit;
}

$is_pending = $request['request_status'] === 'Pending';

$file = $request['prescription_file'];
$file_path = 'uploads/prescriptions/' . $file;

if (!file_exists($file_path)) {
    $file_path = 'images/' . $file;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Request Details</title>
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
      <li><a href="submit-request.php">Submit Request</a></li>
      <li><a href="my-requests.php" class="sn-nav--active">My Requests</a></li>
      <li><a href="logout.php" class="sn-nav--logout">Log out</a></li>
    </ul>
  </div>
</header>

<main class="sn-main submit-request-page">
  <div class="sn-container">

    <a href="my-requests.php" class="sn-back">← Back to My Requests</a>

    <section class="admin-page-head admin-page-head--small">
      <span class="admin-page-head__badge">Request Details</span>
      <h1 class="admin-page-head__title">Request #<?= htmlspecialchars($request['request_id']) ?></h1>
    </section>

    <?php if (isset($_GET['success'])): ?>
      <div class="success-box" style="display:block;">
        <?= htmlspecialchars($_GET['success']) ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div class="success-box" style="display:block; background:#fde8e8; color:#8b2020; border:1px solid #f5c2c2;">
        <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

    <form id="editRequestForm" class="submit-card" method="POST" enctype="multipart/form-data">

      <div class="form-grid">

        <div class="form-field">
          <label>Medication Name</label>
          <input
            type="text"
            name="medication_name"
            id="medication"
            value="<?= htmlspecialchars($request['medication_name']) ?>"
            <?= $is_pending ? '' : 'readonly' ?>
            required
          >
        </div>

        <div class="form-field">
          <label>Priority</label>
          <select name="priority_level" id="priority" <?= $is_pending ? '' : 'disabled' ?> required>
            <option value="High" <?= $request['priority_level'] === 'High' ? 'selected' : '' ?>>High</option>
            <option value="Medium" <?= $request['priority_level'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
            <option value="Low" <?= $request['priority_level'] === 'Low' ? 'selected' : '' ?>>Low</option>
          </select>
        </div>

        <div class="form-field">
          <label>City</label>
          <input type="text" value="<?= htmlspecialchars($request['city']) ?>" disabled>
        </div>

        <div class="form-field">
          <label>Zone</label>
          <select name="zone" id="zone" <?= $is_pending ? '' : 'disabled' ?> required>
            <option value="North Riyadh" <?= $request['zone'] === 'North Riyadh' ? 'selected' : '' ?>>North Riyadh</option>
            <option value="South Riyadh" <?= $request['zone'] === 'South Riyadh' ? 'selected' : '' ?>>South Riyadh</option>
            <option value="East Riyadh" <?= $request['zone'] === 'East Riyadh' ? 'selected' : '' ?>>East Riyadh</option>
            <option value="West Riyadh" <?= $request['zone'] === 'West Riyadh' ? 'selected' : '' ?>>West Riyadh</option>
          </select>
        </div>

        <div class="form-field">
          <label>Status</label>
          <input type="text" value="<?= htmlspecialchars($request['request_status']) ?>" disabled>
        </div>

        <div class="form-field form-full">
          <label>Notes</label>
          <textarea name="notes" id="notes" <?= $is_pending ? '' : 'readonly' ?> required><?= htmlspecialchars($request['notes']) ?></textarea>
        </div>

        <div class="form-field form-full">
          <label>Prescription</label>
          <p>
            <a href="<?= htmlspecialchars($file_path) ?>" target="_blank">View current prescription</a>
          </p>

          <?php if ($is_pending): ?>
            <input type="file" name="prescription_file" id="file" accept="image/*,.pdf">
            <small>You can upload a new prescription if needed.</small>
          <?php endif; ?>
        </div>

      </div>

      <?php if ($is_pending): ?>
        <button type="submit" class="admin-btn admin-btn--primary">Save Changes</button>
      <?php else: ?>
        <p class="about-card__text">This request can no longer be edited because its status is <?= htmlspecialchars($request['request_status']) ?>.</p>
      <?php endif; ?>

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

</body>
</html>