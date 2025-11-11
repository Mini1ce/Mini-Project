<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}
$user_id = get_user_id();

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header('Location: profile.php');
    exit();
}

$order_id = (int)$_GET['order_id'];

$order_info = null;
$order_items = [];
$sql_order = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows === 1) {
    $order_info = $result_order->fetch_assoc();
    $sql_items = "SELECT od.*, p.name, p.brand, p.image 
                  FROM order_detail od
                  JOIN product p ON od.product_id = p.product_id
                  WHERE od.order_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $order_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_items->close();

} else {
    header('Location: profile.php');
    exit();
}
$stmt_order->close();
$conn->close();

$current_status = strtolower($order_info['status']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคำสั่งซื้อ #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="his_detail.css"> 
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
        <div class="order-detail-container">
            <h1>รายละเอียดคำสั่งซื้อ #<?php echo $order_id; ?></h1>
            <div class="order-actions-group">
                <p><a href="profile.php" class="back-link">← กลับสู่หน้าโปรไฟล์</a></p>
                
                <?php 
                if ($current_status === 'pending' || $current_status === 'paid'): 
                ?>
                    <a href="cancel_order.php?order_id=<?php echo $order_id; ?>" 
                       class="btn-cancel-order" 
                       onclick="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกคำสั่งซื้อนี้? เมื่อยกเลิกแล้วจะไม่สามารถกู้คืนได้');">
                        ❌ ยกเลิกคำสั่งซื้อ
                    </a>
                <?php endif; ?>
            </div>

            <div class="order-summary">
                <h2>สรุปข้อมูล</h2>
                <div class="summary-row"><span>วันที่สั่งซื้อ:</span><span><?php echo date('d M Y, H:i', strtotime($order_info['order_date'])); ?></span></div>
                <div class="summary-row"><span>สถานะ:</span>
                    <span class="status-badge status-<?php echo $current_status; ?>">
                        <?php echo htmlspecialchars(ucfirst($order_info['status'])); ?>
                    </span>
                </div>
                <div class="summary-row"><span>ที่อยู่จัดส่ง:</span><span><?php echo nl2br(htmlspecialchars($order_info['shipping_address'])); ?></span></div>
                <div class="summary-row"><span>เลขติดตามพัสดุ:</span><span><?php echo htmlspecialchars($order_info['tracking_number'] ?? '-'); ?></span></div>
                <div class="summary-row"><span>วิธีชำระเงิน:</span><span><?php echo htmlspecialchars($order_info['payment_method'] ?? 'N/A'); ?></span></div>
            </div>

            <h2>รายการสินค้าที่สั่งซื้อ</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th>ไซส์</th>
                        <th>ราคาต่อหน่วย</th>
                        <th>จำนวน</th>
                        <th style="text-align: right;">รวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $subtotal = 0;
                    foreach ($order_items as $item): 
                        $item_total = $item['unit_price'] * $item['quantity'];
                        $subtotal += $item_total;
                    ?>
                    <tr>
                        <td>
                            <img src="images/<?php echo htmlspecialchars($item['image'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['size_label']); ?></td>
                        <td>฿<?php echo number_format($item['unit_price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;">฿<?php echo number_format($item_total, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary-total" style="text-align: right; margin-top: 30px;">
                <p>ยอดสุทธิ: ฿<?php echo number_format($order_info['total_amount'], 2); ?></p>
            </div>
            
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