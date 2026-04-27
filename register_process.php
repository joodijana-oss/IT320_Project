<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$full_name        = trim($_POST['full_name'] ?? '');
$email            = trim($_POST['email'] ?? '');
$phone            = trim($_POST['phone_number'] ?? '');
$dob              = trim($_POST['dob'] ?? '');
$city             = 'Riyadh'; // fixed per your DB — all users are in Riyadh
$zone             = trim($_POST['zone'] ?? '');
$password         = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// --- Validation ---
if (strlen($full_name) < 3) {
    echo json_encode(['success' => false, 'message' => 'Enter a valid full name.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}
if (!preg_match('/^05\d{8}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Phone must start with 05 and be 10 digits.']);
    exit;
}
if (empty($dob)) {
    echo json_encode(['success' => false, 'message' => 'Date of birth is required.']);
    exit;
}
$birthDate = new DateTime($dob);
$today     = new DateTime();
$age       = $today->diff($birthDate)->y;
if ($age < 18) {
    echo json_encode(['success' => false, 'message' => 'You must be at least 18 years old.']);
    exit;
}
if (empty($zone)) {
    echo json_encode(['success' => false, 'message' => 'Please select a zone.']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    exit;
}
if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

// --- Check duplicate email ---
$stmt = $conn->prepare("SELECT patient_id FROM patient WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'An account with this email already exists.']);
    $stmt->close();
    exit;
}
$stmt->close();

// --- Insert ---
// NOTE: your DB stores plain passwords. We use password_hash() for security.
// You will need to update the existing sample rows to hashed passwords too,
// or switch the login check to plain comparison for now (see login.php note).
$hashed = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare(
    "INSERT INTO patient (full_name, email, password, phone_number, DOB, city, zone, account_status)
     VALUES (?, ?, ?, ?, ?, ?, ?, 'Active')"
);
$stmt->bind_param('sssssss', $full_name, $email, $hashed, $phone, $dob, $city, $zone);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Account created successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}
$stmt->close();
$conn->close();
?>