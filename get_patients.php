<?php
header('Content-Type: application/json');
require_once 'db.php';

$sql = "SELECT patient_id, full_name, email, city, account_status
        FROM patient
        ORDER BY patient_id ASC";

$result = $conn->query($sql);

$patients = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

echo json_encode($patients);