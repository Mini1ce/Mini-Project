<?php
session_start();
$user_id = $_SESSION['user_id'] ?? 1;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoespace";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_orders = "SELECT order_id, order_date, total_amount, status, tracking_number 
               FROM orders 
               WHERE user_id = ? 
               ORDER BY order_date DESC";

$stmt = $conn->prepare($sql_orders);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ | Order History</title>
    <link rel="stylesheet" href="order_detail.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;700&display=swap" rel="stylesheet">
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

    <main class="order-history-container">
        <h1>📦 ประวัติการสั่งซื้อของฉัน</h1>
        <p class="back-link"><a href="profile.php">← กลับไปหน้าโปรไฟล์</a></p>

        <?php if ($orders_result->num_rows > 0): ?>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th class="text-right">ยอดรวม</th>
                            <th>สถานะ</th>
                            <th>หมายเลขติดตาม</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders_result->fetch_assoc()): 
                            $order_date_th = date('d M Y', strtotime($order['order_date']));
                        ?>
                        <tr>
                            <td data-label="Order ID">#<?php echo $order['order_id']; ?></td>
                            <td data-label="วันที่สั่งซื้อ"><?php echo $order_date_th; ?></td>
                            <td data-label="ยอดรวม" class="text-right">฿<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td data-label="สถานะ">
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </td>
                            <td data-label="Tracking"><?php echo htmlspecialchars($order['tracking_number'] ?? '-'); ?></td>
                            <td data-label="จัดการ">
                                <a href="his_detail.php?order_id=<?php echo $order['order_id']; ?>" class="btn-detail">ดูรายละเอียด</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="no-orders-message">
                <h2>คุณยังไม่มีประวัติการสั่งซื้อ</h2>
                <p>ได้เวลาเลือกซื้อคู่ใหม่แล้ว!</p>
                <a href="products.php" class="cta-button">เลือกซื้อสินค้า</a>
            </div>
        <?php endif; ?>
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
<?php $conn->close(); ?>