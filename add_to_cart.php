<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

$user_id = get_user_id();

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "shoespace"; 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    
    $size_label = $_POST['size'] ?? '40'; 
    $quantity = (int)($_POST['quantity'] ?? 1); 

    if ($quantity < 1) {
        $quantity = 1; 
    }

    $check_sql = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND size_label = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("iis", $user_id, $product_id, $size_label);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        $update_sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ii", $new_quantity, $cart_item['cart_id']);
        $stmt_update->execute();
        $stmt_update->close();
        
    } else {
        $insert_sql = "INSERT INTO cart (user_id, product_id, size_label, quantity) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("iisi", $user_id, $product_id, $size_label, $quantity);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    
    $stmt->close();
}

$conn->close();

header('Location: cart.php'); 
exit();
?>