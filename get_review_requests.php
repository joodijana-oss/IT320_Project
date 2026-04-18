<?php
header('Content-Type: application/json');
require_once 'db.php';

$sql = "SELECT 
            mr.request_id,
            mr.medication_name,
            mr.priority_level,
            mr.request_status,
            mr.notes,
            mr.request_date,
            mr.city,
            mr.zone,
            p.full_name AS patient_name
        FROM medicationrequest mr
        JOIN patient p ON mr.patient_id = p.patient_id
        ORDER BY mr.request_date DESC";

$result = $conn->query($sql);

$requests = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

echo json_encode($requests);