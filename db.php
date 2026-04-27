<?php
$host     = "localhost";
$user     = "root";
$password = "root";  // MAMP's default password is "root", not empty
$dbname   = "sanad_db";

$conn = new mysqli($host, $user, $password, $dbname, 8889);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>