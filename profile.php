<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user = null;
$message = '';
$is_admin = false;
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoespace";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT user_id, username, fullname, email, phone, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
}

$stmt->close();

$sql_admin = "SELECT 1 FROM admin WHERE user_id = ?";
$stmt_admin = $conn->prepare($sql_admin);
$stmt_admin->bind_param("i", $user_id);
$stmt_admin->execute();
$stmt_admin->store_result();
if ($stmt_admin->num_rows > 0) {
    $is_admin = true;
}
$stmt_admin->close();
$conn->close();

$display_fullname = $user['fullname'] ?? 'ผู้ใช้ไม่ระบุชื่อ';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👤 Profile | ข้อมูลส่วนตัว</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .profile-details-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        .profile-detail-item {
            display: flex;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px dashed #eee;
        }

        .profile-detail-item:last-child {
            border-bottom: none;
        }

        .profile-detail-item strong {
            width: 150px;
            color: var(--orange);
        }
    </style>
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

    <main>
        <div class="profile-container">
            <?php if ($message): ?>
                <div class="error-message" style="text-align:center;"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="profile-header">
                <span class="profile-avatar">👤</span>
                <h1>ข้อมูลบัญชี (Account Profile)</h1>
                <p>ยินดีต้อนรับกลับ <br> คุณ<?php echo htmlspecialchars($display_fullname); ?></p>
            </div>

            <div class="profile-details-card">
                <h2>ข้อมูลส่วนตัว</h2>
                <div class="profile-detail-item">
                    <strong>ชื่อผู้ใช้ (Username)</strong>
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="profile-detail-item">
                    <strong>ชื่อ-นามสกุล</strong>
                    <span><?php echo htmlspecialchars($user['fullname']); ?></span>
                </div>
                <div class="profile-detail-item">
                    <strong>อีเมล</strong>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="profile-detail-item">
                    <strong>เบอร์โทรศัพท์</strong>
                    <span><?php echo htmlspecialchars($user['phone']); ?></span>
                </div>
                <div class="profile-detail-item">
                    <strong>ที่อยู่จัดส่ง</strong>
                    <span><?php echo nl2br(htmlspecialchars($user['address'])); ?></span>
                </div>
            </div>
            <div class="profile-menu">
                <a href="edit_profile.php" class="profile-menu-item">
                    <span>📝 แก้ไขข้อมูลส่วนตัว</span>
                    <span>&gt;</span>
                </a>

            </div>
            <div class="profile-menu">
                <a href="order_detail.php" class="profile-menu-item">
                    <span>📦 รายการคำสั่งซื้อ (Orders)</span>
                    <span>&gt;</span>
                </a>
            </div>

            <?php if ($is_admin): ?>
                <a href="admin_dashboard.php" class="profile-menu-item" style="background-color: #1abc9c; color: white;">
                    <span>⚙️ Admin Dashboard</span>
                    <span>&gt;</span>
                </a>
            <?php endif; ?>

            <button class="logout-btn" onclick="window.location.href='logout.php'">ออกจากระบบ (Logout)</button>
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