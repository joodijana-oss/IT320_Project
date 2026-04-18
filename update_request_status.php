<?php
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

$request_id = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : "";
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : "";

/* Temporary admin ID for demo */
$admin_id = 1;

if ($request_id <= 0 || !in_array($status, ["Approved", "Rejected"])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

/* Optional: append reason into notes if rejected */
if ($status === "Rejected" && $reason !== "") {
    $sql = "UPDATE medicationrequest 
            SET request_status = ?, admin_id = ?, notes = CONCAT(notes, '\n\n[Admin Note] ', ?)
            WHERE request_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $status, $admin_id, $reason, $request_id);
} else {
    $sql = "UPDATE medicationrequest 
            SET request_status = ?, admin_id = ?
            WHERE request_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $status, $admin_id, $request_id);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Request updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update request"]);
}