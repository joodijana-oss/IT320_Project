<?php
header('Content-Type: application/json');
require_once 'db.php';

/* For demo, fixed pharmacy */
$pharmacy_id = isset($_GET['pharmacy_id']) ? (int)$_GET['pharmacy_id'] : 1;

/* Totals */
$response = [
    "offers_submitted" => 0,
    "confirmed_offers" => 0,
    "rejected_offers" => 0,
    "pending_offers" => 0
];

$q1 = $conn->prepare("SELECT COUNT(*) AS total FROM pharmacyoffer WHERE pharmacy_id = ?");
$q1->bind_param("i", $pharmacy_id);
$q1->execute();
$response["offers_submitted"] = $q1->get_result()->fetch_assoc()["total"];

$q2 = $conn->prepare("SELECT COUNT(*) AS total FROM pharmacyoffer WHERE pharmacy_id = ? AND offer_status = 'Accepted'");
$q2->bind_param("i", $pharmacy_id);
$q2->execute();
$response["confirmed_offers"] = $q2->get_result()->fetch_assoc()["total"];

$q3 = $conn->prepare("SELECT COUNT(*) AS total FROM pharmacyoffer WHERE pharmacy_id = ? AND offer_status = 'Rejected'");
$q3->bind_param("i", $pharmacy_id);
$q3->execute();
$response["rejected_offers"] = $q3->get_result()->fetch_assoc()["total"];

$q4 = $conn->prepare("SELECT COUNT(*) AS total FROM pharmacyoffer WHERE pharmacy_id = ? AND offer_status = 'Pending'");
$q4->bind_param("i", $pharmacy_id);
$q4->execute();
$response["pending_offers"] = $q4->get_result()->fetch_assoc()["total"];

/* Monthly chart data */
$response["chart_labels"] = [];
$response["chart_submitted"] = [];
$response["chart_confirmed"] = [];

$sql = "SELECT 
            DATE_FORMAT(offer_date, '%b %Y') AS month_label,
            COUNT(*) AS total_submitted,
            SUM(CASE WHEN offer_status = 'Accepted' THEN 1 ELSE 0 END) AS total_confirmed
        FROM pharmacyoffer
        WHERE pharmacy_id = ?
        GROUP BY YEAR(offer_date), MONTH(offer_date)
        ORDER BY YEAR(offer_date), MONTH(offer_date)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pharmacy_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $response["chart_labels"][] = $row["month_label"];
    $response["chart_submitted"][] = (int)$row["total_submitted"];
    $response["chart_confirmed"][] = (int)$row["total_confirmed"];
}

echo json_encode($response);