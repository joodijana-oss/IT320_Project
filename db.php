<?php
$host     = "localhost";
$user     = "root";
$password = "root";
$dbname   = "sanad_db";

$conn = new mysqli($host, $user, $password, $dbname, 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>