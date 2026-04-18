<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = [];

/* Stats */
$data['pending_requests'] = 0;
$data['approved_requests'] = 0;
$data['rejected_requests'] = 0;
$data['blocked_users'] = 0;

$q1 = $conn->query("SELECT COUNT(*) AS total FROM medicationrequest WHERE request_status = 'Pending'");
if ($q1) $data['pending_requests'] = $q1->fetch_assoc()['total'];

$q2 = $conn->query("SELECT COUNT(*) AS total FROM medicationrequest WHERE request_status = 'Approved'");
if ($q2) $data['approved_requests'] = $q2->fetch_assoc()['total'];

$q3 = $conn->query("SELECT COUNT(*) AS total FROM medicationrequest WHERE request_status = 'Rejected'");
if ($q3) $data['rejected_requests'] = $q3->fetch_assoc()['total'];

$q4 = $conn->query("SELECT COUNT(*) AS total FROM patient WHERE account_status = 'Blocked'");
if ($q4) $data['blocked_users'] = $q4->fetch_assoc()['total'];

/* Recent pending requests */
$data['recent_requests'] = [];
$sql_requests = "SELECT mr.request_id, mr.medication_name, mr.priority_level, mr.request_status
                 FROM medicationrequest mr
                 WHERE mr.request_status = 'Pending'
                 ORDER BY mr.request_date DESC
                 LIMIT 3";
$rq = $conn->query($sql_requests);
if ($rq) {
    while ($row = $rq->fetch_assoc()) {
        $data['recent_requests'][] = $row;
    }
}

/* Recent users */
$data['recent_users'] = [];
$sql_users = "SELECT patient_id, full_name, email, account_status
              FROM patient
              ORDER BY patient_id DESC
              LIMIT 3";
$ru = $conn->query($sql_users);
if ($ru) {
    while ($row = $ru->fetch_assoc()) {
        $data['recent_users'][] = $row;
    }
}

echo json_encode($data);