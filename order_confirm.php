<?php
session_start();
require_once 'db_connect.php'; 

$order_id = $_GET['id'] ?? null;
$user_id = get_user_id();
$order_details = null;

if ($order_id) {
    $sql = "SELECT order_id, total_amount, order_date, status, payment_method, shipping_address
            FROM orders 
            WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order_details = $result->fetch_assoc();
        $sql_count = "SELECT SUM(quantity) as item_count FROM order_detail WHERE order_id = ?";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->bind_param("i", $order_id);
        $stmt_count->execute();
        $order_items_count = $stmt_count->get_result()->fetch_assoc()['item_count'] ?? 0;
        $stmt_count->close();
    }
    $stmt->close();
}
$conn->close();

$is_successful = ($order_details !== null);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | ยืนยันคำสั่งซื้อ</title>
    <link rel="stylesheet" href="order_confirm.css"> 
</head>
<body>
    <header>
        <nav>
            <div class="logo">ShoeSpace</div>
            <ul class="nav-links">
                <li><a href="index.php">หน้าแรก</a></li>
                <li><a href="products.php">สินค้าทั้งหมด</a></li>
                <li><a href="promotion.php">โปรโมชั่น</a></li>
                <li><a href="about.php">เกี่ยวกับเรา</a></li>
                <li>
                    <form action="products.php" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search.." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit" style="display:none;"></button>
                    </form>
                </li>
            </ul>
            <div class="nav-icons">
                <a href="profile.php">👤Profile</a>
                <a href="cart.php">🛒Cart</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="confirmation-container">
            <?php if ($is_successful): ?>
            <div class="icon">✅</div>
            <h1>ชำระเงินสำเร็จ!</h1>
            <p>ขอบคุณสำหรับการสั่งซื้อสินค้ากับ ShoeSpace คำสั่งซื้อของคุณได้รับการยืนยันแล้ว</p>
            
            <div class="confirmation-details">
                <div class="detail-row">
                    <span class="detail-label">หมายเลขคำสั่งซื้อ:</span>
                    <span>#<?php echo htmlspecialchars($order_details['order_id']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">จำนวนสินค้า:</span>
                    <span><?php echo $order_items_count; ?> รายการ</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">จัดส่งไปที่:</span>
                    <span><?php echo htmlspecialchars($order_details['shipping_address']); ?></span>
                </div>
                <div class="detail-total">
                    ยอดสุทธิ: ฿<?php echo number_format($order_details['total_amount'], 2); ?>
                </div>
            </div>

            <div class="cta-button-group">
                <a href="order_detail.php?id=<?php echo $order_details['order_id']; ?>" class="cta-button-primary">ดูรายละเอียดคำสั่งซื้อ</a>
                <a href="profile.php" class="cta-button-secondary">กลับไปหน้าโปรไฟล์</a>
            </div>

            <?php else: ?>
            
            <h1>⚠️ เกิดข้อผิดพลาด</h1>
            <p>ไม่พบข้อมูลคำสั่งซื้อ หรือคำสั่งซื้อยังไม่ได้รับการยืนยัน</p>
            <div class="cta-button-group">
                <a href="profile.php" class="cta-button-secondary">กลับไปหน้าโปรไฟล์</a>
            </div>

            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <div class="footer-content">
            <p>&copy; 2025 ShoeSpace. All rights reserved.</p>
            <div class="social-links">
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">Twitter</a>
            </div>
        </div>
    </footer>
</body>
</html>