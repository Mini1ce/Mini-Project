<?php
session_start();
if (!isset($_GET['cart_id']) || !is_numeric($_GET['cart_id'])) {
    header('Location: cart.php');
    exit();
}

$cart_id_to_remove = (int)$_GET['cart_id'];
$user_id = $_SESSION['user_id'] ?? 1;
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "shoespace"; 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id_to_remove, $user_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: cart.php');
exit();
?>