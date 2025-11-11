<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

$user_id = get_user_id();

$cart_items = [];
$subtotal = 0.00;
$shipping_fee = 80.00;
$total = 0.00;
$user_info = null; 

$sql_cart = "SELECT c.cart_id, c.quantity, c.size_label, 
               p.product_id, p.name, p.brand, p.price 
        FROM cart c
        JOIN product p ON c.product_id = p.product_id
        WHERE c.user_id = ?";

$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("i", $user_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

if ($result_cart->num_rows > 0) {
    while ($item = $result_cart->fetch_assoc()) {
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $subtotal += $item['subtotal'];
        $cart_items[] = $item;
    }
}
$stmt_cart->close();

$total = $subtotal + $shipping_fee;

$sql_user = "SELECT fullname, email, phone, address FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows > 0) {
    $user_info = $result_user->fetch_assoc();
}
$stmt_user->close();

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | ดำเนินการชำระเงิน</title>
    <link rel="stylesheet" href="checkout.css">
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
                        <input type="text" name="search" placeholder="Search..">
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

    <main class="checkout-page-container">
        <h1>ดำเนินการชำระเงิน</h1>
        <?php
if (isset($_SESSION['error_message'])) {
    echo '<div class="error-message-box">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']); 
}
?>

        <div class="checkout-form">
            
            <div class="checkout-left">
                <form action="process_order.php" method="POST" id="checkout-data-form">
                    
                    <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                    <input type="hidden" name="shipping_fee" value="<?php echo $shipping_fee; ?>">
                    <input type="hidden" name="total_amount" value="<?php echo $total; ?>">

                    <section class="shipping-info form-section">
                        <h2>1. ข้อมูลสำหรับจัดส่ง</h2>
                        
                        <div class="form-group">
                            <label for="fullname">ชื่อ-นามสกุล:</label>
                            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user_info['fullname'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">เบอร์โทรศัพท์:</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user_info['phone'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">อีเมล:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">ที่อยู่จัดส่ง:</label>
                            <textarea id="address" name="shipping_address" rows="4" required><?php echo htmlspecialchars($user_info['address'] ?? ''); ?></textarea>
                        </div>
                    </section>
                    
                    <section class="shipping-method form-section">
                        <h2>2. วิธีการจัดส่ง</h2>
                        <div class="shipping-options">
                            <div class="shipping-option">
                                <input type="radio" id="standard" name="shipping_method" value="Standard" checked required>
                                <label for="standard">Standard Shipping (฿<?php echo number_format($shipping_fee, 2); ?>)</label>
                            </div>
                            </div>
                    </section>

                    <section class="payment-method form-section">
                        <h2>3. วิธีการชำระเงิน</h2>
                        <div class="payment-options">
                            
                            <div class="payment-option">
                                <input type="radio" id="transfer" name="payment_method" value="Bank Transfer" required>
                                <label for="transfer">โอนเงิน/Mobile Banking</label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" id="cod" name="payment_method" value="COD" required>
                                <label for="cod">เก็บเงินปลายทาง (COD)</label>
                            </div>

                        </div>
                    </section>
                    
                </form> </div> <div class="checkout-right">
                
                <section class="order-summary-box form-section">
                    <h2>สรุปรายการสั่งซื้อ</h2>
                    
                    <div class="item-list">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="summary-item">
                                <span class="item-name"><?php echo htmlspecialchars($item['name']); ?> (ไซส์ <?php echo htmlspecialchars($item['size_label']); ?>) x <?php echo $item['quantity']; ?></span>
                                <span class="item-price">฿<?php echo number_format($item['subtotal'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>ยอดรวมสินค้า (Subtotal)</span>
                            <span>฿<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>ค่าจัดส่ง (Shipping)</span>
                            <span>฿<?php echo number_format($shipping_fee, 2); ?></span>
                        </div>
                        <div class="summary-total">
                            <span>ยอดสุทธิที่ต้องชำระ (Total)</span>
                            <span>฿<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>

                    <button type="submit" class="place-order-btn cta-button" form="checkout-data-form">ยืนยันการสั่งซื้อ</button>
                </section>
                
            </div> </div> </main>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 ShoeSpace. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>