

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'session_check.php';
require_once 'db.php';

$patient_id = $_SESSION['user_id'];

/* تأكد فيه request_id */
if (!isset($_POST['request_id'])) {
    header('Location: my-requests.php');
    exit;
}

$request_id = intval($_POST['request_id']);

/* تأكد الطلب حق نفس اليوزر وحالته Pending */
$stmt = $conn->prepare("
    SELECT request_status 
    FROM medicationrequest 
    WHERE request_id = ? AND patient_id = ?
");

$stmt->bind_param("ii", $request_id, $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();

/* إذا ما لقي الطلب */
if (!$request) {
    header('Location: my-requests.php');
    exit;
}


if ($request['request_status'] !== 'Pending') {
    header('Location: my-requests.php?error=You can only delete pending requests');
    exit;
}

/* حذف */
$stmt = $conn->prepare("
    DELETE FROM medicationrequest 
    WHERE request_id = ? AND patient_id = ?
");

$stmt->bind_param("ii", $request_id, $patient_id);

if ($stmt->execute()) {
    header('Location: my-requests.php?success=Request deleted successfully');
} else {
    header('Location: my-requests.php?error=Failed to delete request');
}

exit;
?>