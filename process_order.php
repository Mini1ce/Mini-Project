<?php
session_start();
require_once 'db_connect.php';
$user_id = get_user_id();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: checkout.php");
    exit();
}

$fullname = $_POST['fullname'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$shipping_address = $_POST['shipping_address'];
$payment_method = $_POST['payment_method'];
$shipping_method = $_POST['shipping_method'];
$total_amount = $_POST['total_amount']; 
$shipping_fee = $_POST['shipping_fee']; 
$subtotal = $_POST['subtotal'];

$conn->begin_transaction();
$new_order_id = null;


try {
    $cart_items = [];
    $sql_cart = "SELECT c.quantity, c.size_label, p.product_id, p.price 
                FROM cart c
                JOIN product p ON c.product_id = p.product_id
                WHERE c.user_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    if ($result_cart->num_rows == 0) {
        throw new Exception("ตะกร้าสินค้าว่างเปล่าหรือสินค้าถูกลบไปแล้ว");
    }

    while ($item = $result_cart->fetch_assoc()) {
        $cart_items[] = $item;
    }
    $stmt_cart->close();

    $sql_order = "INSERT INTO orders (user_id, order_date, total_amount, shipping_address, payment_method, status) 
                  VALUES (?, NOW(), ?, ?, ?, 'pending')";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("idss", 
        $user_id, 
        $total_amount, 
        $shipping_address, 
        $payment_method
    );
    $stmt_order->execute();
    $new_order_id = $conn->insert_id;
    $stmt_order->close();

    $sql_detail = "INSERT INTO order_detail (order_id, product_id, size_label, quantity, unit_price) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);

    $sql_stock_update = "UPDATE product_size SET stock = stock - ? WHERE product_id = ? AND size_label = ? AND stock >= ?";
    $stmt_stock = $conn->prepare($sql_stock_update);
    
    foreach ($cart_items as $item) {
        $stmt_stock->bind_param("iisi", 
            $item['quantity'], 
            $item['product_id'], 
            $item['size_label'], 
            $item['quantity']
        );
        $stmt_stock->execute();
        if ($conn->affected_rows === 0) {
            throw new Exception("สินค้า " . $item['product_id'] . " ไซส์ " . $item['size_label'] . " มีสต็อกไม่พอ.");
        }

       $stmt_detail->bind_param("iisid", $new_order_id, $item['product_id'], $item['size_label'], $item['quantity'], $item['price']);
        $stmt_detail->execute();
    }

    $stmt_detail->close();
    $stmt_stock->close();
    $sql_clear_cart = "DELETE FROM cart WHERE user_id = ?";
    $stmt_clear_cart = $conn->prepare($sql_clear_cart);
    $stmt_clear_cart->bind_param("i", $user_id);
    $stmt_clear_cart->execute();
    $stmt_clear_cart->close();

    $conn->commit();
    header("Location: order_confirm.php?id=" . $new_order_id);
    exit();


} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการสั่งซื้อ: " . $e->getMessage();

    header("Location: checkout.php");
    exit();
}

$conn->close();
?>