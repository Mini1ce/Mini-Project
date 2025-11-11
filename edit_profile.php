<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "shoespace"; 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $update_fields = [];
    $update_types = '';
    $update_params = [];

    $update_fields[] = "fullname = ?";
    $update_fields[] = "phone = ?";
    $update_fields[] = "address = ?";
    $update_types .= 'sss';
    $update_params[] = $fullname;
    $update_params[] = $phone;
    $update_params[] = $address;

    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $message = '<div class="error-message">รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน</div>';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_fields[] = "password = ?";
            $update_types .= 's';
            $update_params[] = $hashed_password;
        }
    }


    if (empty($message)) {
        $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE user_id = ?";
        $update_types .= 'i';
        $update_params[] = $user_id;

        $stmt = $conn->prepare($sql);
        
        function ref_values($arr){
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }

        call_user_func_array([$stmt, 'bind_param'], ref_values(array_merge([$update_types], $update_params)));

        if ($stmt->execute()) {
            $message = '<div class="success-message">บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว!</div>';
            $_SESSION['fullname'] = $fullname;   
        } else {
            $message = '<div class="error-message">เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $conn->error . '</div>';
        }
        $stmt->close();
    }
}

$sql_fetch = "SELECT fullname, email, phone, address FROM users WHERE user_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $user_id);
$stmt_fetch->execute();
$result_fetch = $stmt_fetch->get_result();
$user = $result_fetch->fetch_assoc();
$stmt_fetch->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลส่วนตัว | ShoeSpace</title>
    <link rel="stylesheet" href="edit_profile.css">
    <style>
        .error-message { color: red; padding: 10px; border: 1px solid red; background-color: #ffeaea; margin-bottom: 15px; border-radius: 5px; }
        .success-message { color: green; padding: 10px; border: 1px solid green; background-color: #eaffea; margin-bottom: 15px; border-radius: 5px; }
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
        <div class="edit-profile-container">
            <h1>แก้ไขข้อมูลส่วนตัว</h1>
            <?php echo $message;?>
            
            <form method="POST" action="edit_profile.php" class="profile-form">
                <div class="form-section">
                    <h2>ข้อมูลติดต่อ</h2>
                    
                    <div class="form-group">
                        <label for="fullname">ชื่อ-นามสกุล:</label>
                        <input type="text" id="fullname" name="fullname" 
                               value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">อีเมล:</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly style="background-color: #eee;">
                        <small>อีเมลไม่สามารถแก้ไขได้</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">เบอร์โทรศัพท์:</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="address">ที่อยู่จัดส่ง:</label>
                        <textarea id="address" name="address" rows="4"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-section password-section">
                    <h2>เปลี่ยนรหัสผ่าน</h2>
                    <p class="note">กรุณากรอกเฉพาะเมื่อต้องการเปลี่ยนรหัสผ่าน</p>

                    <div class="form-group">
                        <label for="new_password">รหัสผ่านใหม่:</label>
                        <input type="password" id="new_password" name="new_password" placeholder="กรอกรหัสผ่านใหม่">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">ยืนยันรหัสผ่านใหม่:</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="ยืนยันรหัสผ่านใหม่อีกครั้ง">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="cta-button save-button">บันทึกการเปลี่ยนแปลง</button>
                    <a href="profile.php" class="cta-button cancel-button">ยกเลิก</a>
                </div>
            </form>
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