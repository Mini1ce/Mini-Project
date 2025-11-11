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

$cart_items = [];
$subtotal = 0.00;
$shipping_fee = 80.00;
$total = 0.00;

$sql = "SELECT c.cart_id, c.quantity, c.size_label, 
               p.product_id, p.name, p.brand, p.price, p.image 
        FROM cart c
        JOIN product p ON c.product_id = p.product_id
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_total = $row['price'] * $row['quantity'];
        $subtotal += $item_total;
        
        $cart_items[] = $row;
    }
}
$stmt->close();
$conn->close();

$total = $subtotal + $shipping_fee;

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | ตะกร้าสินค้า</title>
    <link rel="stylesheet" href="cart.css">
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

    <main class="cart-page-container">
        <h1>ตะกร้าสินค้าของคุณ</h1>

        <?php if (!empty($cart_items)): ?>
            <div class="cart-layout">
                <div class="cart-items-list">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="cart-item-details">
                                <p class="cart-item-brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                                <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="cart-item-size">ไซส์: <?php echo htmlspecialchars($item['size_label']); ?></p>
                                <p class="cart-item-price">฿<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="cart-item-quantity">
                                <label>จำนวน:</label>
                                <input type="number" value="<?php echo $item['quantity']; ?>" min="1" data-cart-id="<?php echo $item['cart_id']; ?>" style="width: 50px;">
                            </div>
                            <div class="cart-item-total">
                                <p>฿<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                            <div class="cart-item-remove">
                                <a href="remove.php?cart_id=<?php echo $item['cart_id']; ?>" title="ลบสินค้า">&times;</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <h3>สรุปยอดสั่งซื้อ</h3>
                    <div class="summary-row">
                        <span>ยอดรวม (Subtotal)</span>
                        <span>฿<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>ค่าจัดส่ง (Shipping)</span>
                        <span>฿<?php echo number_format($shipping_fee, 2); ?></span>
                    </div>
                    <div class="summary-total">
                        <span>ยอดสุทธิ (Total)</span>
                        <span>฿<?php echo number_format($total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="checkout-btn cta-button">ดำเนินการชำระเงิน</a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-empty">
                <p>🛒</p>
                <h2>ตะกร้าของคุณว่างเปล่า</h2>
                <p>ดูเหมือนว่าคุณยังไม่ได้เพิ่มสินค้าลงในตะกร้าเลย</p>
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