<?php
session_start();

// ── SESSION GUARD ───────────────────────────────────────────
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: login.html');
    exit;
}

// ── ONLY ACCEPT POST REQUESTS ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my-requests.php');
    exit;
}

// ── DB CONNECTION ───────────────────────────────────────────
require_once 'db.php';

// ── GET POST DATA ───────────────────────────────────────────
$offer_id   = intval($_POST['offer_id']);
$request_id = intval($_POST['request_id']);
$action     = $_POST['action']; // 'accept' or 'reject'

// ── SECURITY: make sure this request belongs to this patient ─
$patient_id = $_SESSION['user_id'];
$check = $conn->prepare("SELECT request_id FROM medicationrequest WHERE request_id = ? AND patient_id = ?");
$check->bind_param("ii", $request_id, $patient_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    header('Location: my-requests.php');
    exit;
}

// ── HANDLE ACCEPT ───────────────────────────────────────────
if ($action === 'accept') {

    // 1. Set this offer to Accepted
    $stmt1 = $conn->prepare("UPDATE pharmacyoffer SET offer_status = 'Accepted' WHERE offer_id = ?");
    $stmt1->bind_param("i", $offer_id);
    $stmt1->execute();

    // 2. Set all OTHER offers for this request to Rejected
    $stmt2 = $conn->prepare("UPDATE pharmacyoffer SET offer_status = 'Rejected' WHERE request_id = ? AND offer_id != ?");
    $stmt2->bind_param("ii", $request_id, $offer_id);
    $stmt2->execute();

    // 3. Set the request status to Confirmed
    $stmt3 = $conn->prepare("UPDATE medicationrequest SET request_status = 'Confirmed' WHERE request_id = ?");
    $stmt3->bind_param("i", $request_id);
    $stmt3->execute();
}

// ── HANDLE REJECT ───────────────────────────────────────────
elseif ($action === 'reject') {

    // Set this offer to Rejected
    $stmt = $conn->prepare("UPDATE pharmacyoffer SET offer_status = 'Rejected' WHERE offer_id = ?");
    $stmt->bind_param("i", $offer_id);
    $stmt->execute();
}

// ── REDIRECT BACK ───────────────────────────────────────────
header("Location: patient-offers.php?request_id=$request_id");
exit;
?>
