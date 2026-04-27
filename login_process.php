<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$role     = trim($_POST['role'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!in_array($role, ['patient', 'pharmacy', 'admin'])) {
    echo json_encode(['success' => false, 'message' => 'Please select a valid role.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}
if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required.']);
    exit;
}

// Helper: checks hashed OR plain-text password (remove plain-text check after re-seeding)
function verifyPassword($input, $stored) {
    if (password_verify($input, $stored)) return true;   // hashed (new registrations)
    if ($input === $stored)              return true;    // plain text (existing seed data)
    return false;
}

if ($role === 'patient') {

    $stmt = $conn->prepare(
        "SELECT patient_id, full_name, password, account_status FROM patient WHERE email = ?"
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($id, $full_name, $stored_pw, $status);
    $stmt->fetch();
    $stmt->close();

    if (!$id || !verifyPassword($password, $stored_pw)) {
        echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
        exit;
    }
    if ($status === 'Blocked') {
        echo json_encode(['success' => false, 'message' => 'Your account has been blocked. Please contact support.']);
        exit;
    }

    $_SESSION['user_id']   = $id;
    $_SESSION['user_name'] = $full_name;
    $_SESSION['role']      = 'patient';
    echo json_encode(['success' => true, 'redirect' => 'user-dashboard.php']);

} elseif ($role === 'pharmacy') {

    $stmt = $conn->prepare(
        "SELECT pharmacy_id, pharmacy_name, password FROM pharmacy WHERE email = ?"
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($id, $name, $stored_pw);
    $stmt->fetch();
    $stmt->close();

    if (!$id || !verifyPassword($password, $stored_pw)) {
        echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
        exit;
    }

    $_SESSION['user_id']   = $id;
    $_SESSION['user_name'] = $name;
    $_SESSION['role']      = 'pharmacy';
    echo json_encode(['success' => true, 'redirect' => 'pharmacy-dashboard.php']);

} elseif ($role === 'admin') {

    $stmt = $conn->prepare(
        "SELECT admin_id, full_name, password FROM admin WHERE email = ?"
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($id, $full_name, $stored_pw);
    $stmt->fetch();
    $stmt->close();

    if (!$id || !verifyPassword($password, $stored_pw)) {
        echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
        exit;
    }

    $_SESSION['user_id']   = $id;
    $_SESSION['user_name'] = $full_name;
    $_SESSION['role']      = 'admin';
    echo json_encode(['success' => true, 'redirect' => 'admin-dashboard.php']);
}

$conn->close();
?>