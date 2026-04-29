<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my-requests.php');
    exit;
}

require_once 'db.php';

$offer_id   = intval($_POST['offer_id']);
$request_id = intval($_POST['request_id']);
$action     = $_POST['action']; // 'accept' or 'reject'

$patient_id = $_SESSION['user_id'];
$check = $conn->prepare("SELECT request_id FROM medicationrequest WHERE request_id = ? AND patient_id = ?");
$check->bind_param("ii", $request_id, $patient_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    header('Location: my-requests.php');
    exit;
}

if ($action === 'accept') {

    $stmt1 = $conn->prepare("UPDATE pharmacyoffer SET offer_status = 'Accepted' WHERE offer_id = ?");
    $stmt1->bind_param("i", $offer_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("UPDATE pharmacyoffer SET offer_status = 'Rejected' WHERE request_id = ? AND offer_id != ?");
    $stmt2->bind_param("ii", $request_id, $offer_id);
    $stmt2->execute();

    $stmt3 = $conn->prepare("UPDATE medicationrequest SET request_status = 'Confirmed' WHERE request_id = ?");
    $stmt3->bind_param("i", $request_id);
    $stmt3->execute();
}

elseif ($action === 'reject') {

    // Set this offer to Rejected
    $stmt = $conn->prepare("UPDATE pharmacyoffer SET offer_status = 'Rejected' WHERE offer_id = ?");
    $stmt->bind_param("i", $offer_id);
    $stmt->execute();
}

header("Location: patient-offers.php?request_id=$request_id");
exit;
?>
