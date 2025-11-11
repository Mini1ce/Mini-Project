<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions | โปรโมชั่น</title>
    <link rel="stylesheet" href="promotion.css">
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

    <main class="promotion-page-container">
        <h1>โปรโมชั่นสุดพิเศษ</h1>

        <section class="promo-hero">
            <div class="promo-hero-content">
                <h2>END OF SEASON SALE!</h2>
                <p>ลดราคาสินค้าครั้งใหญ่ส่งท้ายซีซั่น ลดสูงสุดถึง 50%</p>
                <a href="products.php?promo=season_sale" class="promo-cta">ช้อปเลย!</a>
            </div>
        </section>

        <section class="promo-grid-section">
            <div class="promo-grid">

                <a href="products.php?category=1" class="promo-card">
                    <h3>Running Week</h3>
                    <p>ส่วนลดพิเศษ 20% สำหรับรองเท้าวิ่งทุกรุ่นที่ร่วมรายการ</p>
                    <span class="promo-card-cta">ดูสินค้าที่ร่วมรายการ</span>
                </a>

                <a href="products.php?brand=Nike" class="promo-card">
                    <h3>Nike Special</h3>
                    <p>ซื้อสินค้าแบรนด์ Nike ครบ 3,000 บาท รับส่วนลดเพิ่มทันที 300 บาท</p>
                    <span class="promo-card-cta">ดูสินค้าที่ร่วมรายการ</span>
                </a>

                <a href="#" class="promo-card">
                    <h3>ส่งฟรี!</h3>
                    <p>บริการจัดส่งฟรีทั่วประเทศ เมื่อช้อปครบ 2,500 บาทขึ้นไป</p>
                    <span class="promo-card-cta">ดูรายละเอียดเพิ่มเติม</span>
                </a>

                <a href="products.php?category=3" class="promo-card">
                    <h3>Fashion Deal</h3>
                    <p>ซื้อรองเท้าแฟชั่น 2 คู่ในราคาพิเศษ เริ่มต้นเพียง 4,000 บาท</p>
                    <span class="promo-card-cta">ดูสินค้าที่ร่วมรายการ</span>
                </a>

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