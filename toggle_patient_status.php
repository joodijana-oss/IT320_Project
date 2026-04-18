<?php
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

$patient_id = isset($_POST['patient_id']) ? (int) $_POST['patient_id'] : 0;

if ($patient_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid patient ID"]);
    exit;
}

/* Get current status */
$sql = "SELECT account_status FROM patient WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Patient not found"]);
    exit;
}

$row = $result->fetch_assoc();
$new_status = ($row['account_status'] === 'Blocked') ? 'Active' : 'Blocked';

/* Update */
$update = "UPDATE patient SET account_status = ? WHERE patient_id = ?";
$stmt2 = $conn->prepare($update);
$stmt2->bind_param("si", $new_status, $patient_id);

if ($stmt2->execute()) {
    echo json_encode([
        "success" => true,
        "new_status" => $new_status
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update patient status"]);
}