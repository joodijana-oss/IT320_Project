<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT full_name, email, phone_number, DOB, city, zone, account_status 
     FROM patient WHERE patient_id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $phone, $dob, $city, $zone, $status);
$stmt->fetch();
$stmt->close();
$conn->close();

if (!$full_name) {
    echo json_encode(['success' => false, 'message' => 'Patient not found.']);
    exit;
}

// Build initials from first and last name
$parts    = explode(' ', trim($full_name));
$initials = strtoupper(substr($parts[0], 0, 1));
if (count($parts) > 1) {
    $initials .= strtoupper(substr(end($parts), 0, 1));
}

echo json_encode([
    'success'   => true,
    'full_name' => $full_name,
    'email'     => $email,
    'phone'     => $phone,
    'dob'       => date('j M Y', strtotime($dob)),
    'city'      => $city,
    'zone'      => $zone,
    'status'    => $status,
    'initials'  => $initials
]);
?>