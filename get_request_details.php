<?php
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["error" => "Invalid request ID"]);
    exit;
}

$request_id = (int) $_GET['id'];

$sql = "SELECT 
            mr.request_id,
            mr.medication_name,
            mr.priority_level,
            mr.request_status,
            mr.notes,
            mr.prescription_file,
            mr.request_date,
            mr.city,
            mr.zone,
            p.full_name AS patient_name
        FROM medicationrequest mr
        JOIN patient p ON mr.patient_id = p.patient_id
        WHERE mr.request_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Request not found"]);
    exit;
}

echo json_encode($result->fetch_assoc());