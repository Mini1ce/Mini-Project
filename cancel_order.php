<?php
session_start();
require_once 'db_connect.php';

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id']) || !isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
}

$order_id = (int)$_GET['order_id'];
$user_id = get_user_id();

$conn->begin_transaction();

try {
    $sql_check = "SELECT o.status, od.product_id, od.size_label, od.quantity 
                  FROM orders o
                  JOIN order_detail od ON o.order_id = od.order_id
                  WHERE o.order_id = ? AND o.user_id = ? AND o.status IN ('pending', 'paid')";
    
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $order_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        throw new Exception("คำสั่งซื้อไม่สามารถยกเลิกได้ (อาจถูกดำเนินการไปแล้ว หรือสถานะไม่ถูกต้อง)");
    }
    
    $items_to_return = $result_check->fetch_all(MYSQLI_ASSOC);
    $stmt_check->close();

    $sql_return_stock = "UPDATE product_size SET stock = stock + ? WHERE product_id = ? AND size_label = ?";
    $stmt_return = $conn->prepare($sql_return_stock);
    
    foreach ($items_to_return as $item) {
        $stmt_return->bind_param("iis", $item['quantity'], $item['product_id'], $item['size_label']);
        $stmt_return->execute();
    }
    $stmt_return->close();

    $sql_cancel = "UPDATE orders SET status = 'cancelled' WHERE order_id = ? AND user_id = ?";
    $stmt_cancel = $conn->prepare($sql_cancel);
    $stmt_cancel->bind_param("ii", $order_id, $user_id);
    $stmt_cancel->execute();
    $stmt_cancel->close();

    $conn->commit();
    
    $_SESSION['success_message'] = "ยกเลิกคำสั่งซื้อ #$order_id สำเร็จ และคืนสินค้าเข้าสู่ระบบแล้ว";
    header('Location: profile.php'); 
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = "ยกเลิกไม่สำเร็จ: " . $e->getMessage();
    header('Location: profile.php');
    exit();
}
$conn->close();
?>