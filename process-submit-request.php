<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$required_role = 'patient';
require 'session_check.php';
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: submit-request.php');
    exit;
}
$patient_id = $_SESSION['user_id'];
$medication_name = trim($_POST['medication_name'] ?? '');
$priority_level  = trim($_POST['priority_level'] ?? '');
$notes           = trim($_POST['notes'] ?? '');
$city            = 'Riyadh';
$zone            = trim($_POST['zone'] ?? '');
if ($medication_name === '' || $priority_level === '' || $zone === '') {
    header('Location: submit-request.php?error=' . urlencode('Please fill all required fields.'));
    exit;
}
if (!in_array($priority_level, ['High', 'Medium', 'Low'])) {
    header('Location: submit-request.php?error=' . urlencode('Invalid priority selected.'));
    exit;
}
if (!in_array($zone, ['North Riyadh', 'South Riyadh', 'East Riyadh', 'West Riyadh'])) {
    header('Location: submit-request.php?error=' . urlencode('Invalid zone selected.'));
    exit;
}
if (!isset($_FILES['prescription_file']) || $_FILES['prescription_file']['error'] !== UPLOAD_ERR_OK) {
    header('Location: submit-request.php?error=' . urlencode('Please upload a valid prescription file.'));
    exit;
}
$allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
$original_name = $_FILES['prescription_file']['name'];
$tmp_name = $_FILES['prescription_file']['tmp_name'];
$ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
if (!in_array($ext, $allowed_ext)) {
    header('Location: submit-request.php?error=' . urlencode('Only JPG, PNG, and PDF files are allowed.'));
    exit;
}
$upload_dir = 'uploads/prescriptions/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
$new_file_name = 'prescription_' . $patient_id . '_' . time() . '.' . $ext;
$target_path = $upload_dir . $new_file_name;
if (!move_uploaded_file($tmp_name, $target_path)) {
    header('Location: submit-request.php?error=' . urlencode('File upload failed. Please try again.'));
    exit;
}
$request_status = 'Pending';
$stmt = $conn->prepare("
    INSERT INTO medicationrequest
    (patient_id, admin_id, medication_name, priority_level, request_status, notes, prescription_file, request_date, city, zone)
    VALUES (?, NULL, ?, ?, ?, ?, ?, NOW(), ?, ?)
");
$stmt->bind_param(
    "isssssss",
    $patient_id,
    $medication_name,
    $priority_level,
    $request_status,
    $notes,
    $new_file_name,
    $city,
    $zone
);
if ($stmt->execute()) {
    header('Location: my-requests.php?success=' . urlencode('Request submitted successfully.'));
    exit;
} else {
    header('Location: submit-request.php?error=' . urlencode('Could not submit request. Please try again.'));
    exit;
}
?>