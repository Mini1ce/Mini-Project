<?php
session_start();
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "shoespace"; 
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$product_name = "ไม่พบสินค้า";
$product = null;
$all_product_sizes = []; 
$selected_size = false; 
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // (ดึงข้อมูลสินค้า)
    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $product_name = htmlspecialchars($product['name']);

        // ดึงทุกไซส์ของสินค้านี้ พร้อมจำนวนสต็อก
        $size_sql = "SELECT size_label, stock FROM product_size WHERE product_id = ? ORDER BY size_label";
        $size_stmt = $conn->prepare($size_sql);
        $size_stmt->bind_param("i", $product_id);
        $size_stmt->execute();
        $size_result = $size_stmt->get_result();
        
        while ($row = $size_result->fetch_assoc()) {
            $all_product_sizes[] = $row; 
        }
        $size_stmt->close();

    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product_name; ?> | ShoeSpace</title>
    <link rel="stylesheet" href="product-detail.css"> 
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
            </ul>
            <div class = "nav-icons">
                <li>
                    <form action="products.php" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search.." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit" style="display:none;"></button>
                    </form>
                </li>
                <a href="profile.php">👤Profile</a>
                <a href="cart.php">🛒Cart</a>
            </div>
        </nav>
    </header>

    <main class="page-container">
        <?php if ($product):?>
            
            <div class="product-detail-container">
                
                <div class="product-detail-image">
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo $product_name; ?>">
                </div>

                <div class="product-detail-info">
                    <p class="detail-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                    <h1 class="detail-name"><?php echo $product_name; ?></h1>
                    <p class="detail-price">฿<?php echo number_format($product['price'], 2); ?></p>
                    <p class="detail-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    
                    <form action="add_to_cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                        <?php if (!empty($all_product_sizes)): ?>
                            
                            <div class="form-group">
                                <label>เลือกไซส์:</label>
                                <div class="size-grid">
                                    <?php 
                                    $selected_size_flag = false;
                                    foreach ($all_product_sizes as $size): 
                                        $size_label = htmlspecialchars($size['size_label']);
                                        $stock = (int)$size['stock'];
                                        $is_checked = false;
                                        
                                        if ($stock > 0 && !$selected_size_flag) {
                                            $is_checked = true;
                                            $selected_size_flag = true;
                                        }
                                    ?>
                                        <input 
                                            type="radio" 
                                            id="size-<?php echo $size_label; ?>" 
                                            name="size" 
                                            value="<?php echo $size_label; ?>"
                                            <?php if ($stock == 0) echo 'disabled'; ?>
                                            <?php if ($is_checked) echo 'checked'; ?>
                                            required
                                        >
                                        <label 
                                            for="size-<?php echo $size_label; ?>"
                                            class="<?php if ($stock == 0) echo 'out-of-stock-btn'; ?>"
                                        >
                                            <?php echo $size_label; ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="quantity">จำนวน:</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="10"> 
                            </div>

                            <button type="submit" class="detail-add-to-cart">เพิ่มลงตะกร้า</button>

                        <?php else:?>
                            
                            <p class="out-of-stock">สินค้านี้ยังไม่มีการกำหนดไซส์</p>
                            <button type="button" class="detail-add-to-cart" disabled>เพิ่มลงตะกร้า</button>

                        <?php endif; ?>
                    </form>
                    </div>
            </div>

        <?php else:?>
            <div class="product-not-found">
                <h2>404 - ไม่พบสินค้า</h2>
                <p>ขออภัย ไม่พบสินค้าที่คุณกำลังค้นหา</p>
                <a href="products.php" class="cta-button">กลับไปหน้าสินค้าทั้งหมด</a>
            </div>
        <?php endif; ?>
    </main>

    <?php $conn->close(); ?>
</body>
</html>