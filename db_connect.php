<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoespace";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

function get_user_id() {
    return $_SESSION['user_id'] ?? 1; 
}
?>