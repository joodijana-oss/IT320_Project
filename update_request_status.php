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

$admin_id = 1;

if ($request_id <= 0 || !in_array($status, ["Approved", "Rejected"])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$check = $conn->prepare("SELECT request_status FROM medicationrequest WHERE request_id = ?");
$check->bind_param("i", $request_id);
$check->execute();
$result = $check->get_result()->fetch_assoc();

if (!$result) {
    echo json_encode(["success" => false, "message" => "Request not found"]);
    exit;
}

if ($result['request_status'] !== 'Pending') {
    echo json_encode(["success" => false, "message" => "This request has already been " . $result['request_status'] . " and cannot be changed."]);
    exit;
}

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
?>