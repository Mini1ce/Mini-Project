<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShoeSpace - Find Your Perfect Pair</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<script src="js/script.js" defer></script>

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
        <section class="hero">
            <div class="hero-content">
                <h1>ก้าวไปกับสไตล์ที่เป็นคุณ</h1>
                <p>ค้นหารองเท้าคู่โปรดที่ใช่สำหรับทุกกิจกรรมของคุณ</p>
                <a href="products.php">
                    <button class="cta-button">เลือกซื้อเลย</button>
                </a>

            </div>
        </section>

        <section class="categories">
            <h2>หมวดหมู่สินค้า</h2>
            <div class="category-container">

                <a href="products.php?category=1" class="category-card" style="background-color: #FFD46C;">
                    <h3>Running</h3>
                    <p>รองเท้าวิ่งสำหรับคุณ</p>
                </a>

                <a href="products.php?category=2" class="category-card" style="background-color: #A8E2E1;">
                    <h3>Basketball</h3>
                    <p>เต็มที่ทุกสนาม</p>
                </a>

                <a href="products.php?category=3" class="category-card" style="background-color: #FF9B51;">
                    <h3>Fashion</h3>
                    <p>สไตล์ที่โดดเด่น</p>
                </a>

                <a href="products.php?category=4" class="category-card" style="background-color: #FDF4EE; color: #333;">
                    <h3>Casual</h3>
                    <p>สบายๆ ทุกวัน</p>
                </a>

            </div>
        </section>

        <section class="featured-products">
            <h2>สินค้ายอดนิยม</h2>

            <div class="slider-container">
                <button class="slider-btn" id="prev-btn">&lt;</button>

                <div class="slider-viewport">
                    <div class="slider-track">
                        <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "shoespace";
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if (!$conn->connect_error) {
                            $sql = "SELECT * FROM product LIMIT 5";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<div class="product-card">';
                                    echo '  <div class="product-image">';
                                    echo '    <img src="images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                                    echo '  </div>';
                                    echo '  <div class="product-info">';
                                    echo '    <p class="product-brand">' . htmlspecialchars($row['brand']) . '</p>';
                                    echo '    <h3 class="product-name">' . htmlspecialchars($row['name']) . '</h3>';
                                    echo '    <p class="product-price">฿' . number_format($row['price'], 2) . '</p>';
                                    echo '    <a href="product-detail.php?id=' . $row['product_id'] . '" class="product-details-btn">รายละเอียด</a>';
                                    echo '    <form action="add_to_cart.php" method="POST">';
                                    echo '      <input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
                                    echo '      <input type="hidden" name="size" value="40">'; 
                                    echo '      <input type="hidden" name="quantity" value="1">'; 
                                    echo '      <button type="submit" class="add-to-cart-btn">เพิ่มลงตะกร้า</button>';
                                    echo '    </form>';
                                    echo '  </div>';
                                    echo '</div>';
                                }
                            }
                            $conn->close();
                        }
                        ?>
                    </div>
                </div>

                <button class="slider-btn" id="next-btn">&gt;</button>
            </div>
        </section>

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