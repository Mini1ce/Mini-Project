<?php
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
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';
    
    if ($password !== $confirm_password) {
        $message = '<div class="error-message">รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน</div>';
    } else {
        $check_sql = "SELECT user_id FROM users WHERE username = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $message = '<div class="error-message">ชื่อผู้ใช้นี้มีคนใช้แล้ว กรุณาเลือกชื่อผู้ใช้อื่น</div>';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, password, fullname, email, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("ssssss", $username, $hashed_password, $fullname, $email, $phone, $address);
            
            if ($stmt_insert->execute()) {
                $message = '<div class="success-message">ลงทะเบียนสำเร็จ! กำลังนำทางไปยังหน้าเข้าสู่ระบบ...</div>';
                header('refresh:3; url=login.php');
            } else {
                $message = '<div class="error-message">เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $conn->error . '</div>';
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* CSS สำหรับข้อความแจ้งเตือน */
        .error-message { color: red; padding: 10px; border: 1px solid red; background-color: #ffeaea; margin-bottom: 15px; border-radius: 5px; }
        .success-message { color: green; padding: 10px; border: 1px solid green; background-color: #eaffea; margin-bottom: 15px; border-radius: 5px; }
        /* กำหนด method เป็น POST และใส่ name ใน input */
        .container form { display: flex; flex-direction: column; gap: 10px; }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">ShoeSpace</div>
        </nav>
    </header>
    
    <div class="container">
        <h1>ลงทะเบียนผู้ใช้ใหม่</h1>
        <?php echo $message; ?>
        <form action="register.php" method="POST">
            <div>
                <label for="fullname">ชื่อ-นามสกุล</label>
                <input type="text" id="fullname" name="fullname" placeholder="กรอกชื่อ-นามสกุล" required>
            </div>

            <div>
                <label for="email">อีเมล</label>
                <input type="email" id="email" name="email" placeholder="กรอกอีเมล" required>
            </div>

            <div>
                <label for="phone">เบอร์โทรศัพท์</label>
                <input type="tel" id="phone" name="phone" placeholder="กรอกเบอร์โทรศัพท์" required>
            </div>

            <div>
                <label for="address">ที่อยู่</label>
                <textarea id="address" name="address" placeholder="กรอกที่อยู่สำหรับจัดส่ง" rows="3" required></textarea>
            </div>
            
            <div>
                <label for="username">ชื่อผู้ใช้ (Username)</label>
                <input type="text" id="username" name="username" placeholder="ตั้งชื่อผู้ใช้" required>
            </div>
            
            <div>
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" placeholder="ตั้งรหัสผ่าน" required>
            </div>
            
            <div>
                <label for="confirm-password">ยืนยันรหัสผ่าน</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="ยืนยันรหัสผ่านอีกครั้ง" required>
            </div>
            
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" required> ยินยอมการสมัครสมาชิก
                </label>
            </div>
            
            <button type="submit" class="button">ลงทะเบียน</button>
        </form>
        
        <p>เป็นสมาชิกอยู่แล้วใช่ไหม? <a href="login.php">ลงชื่อเข้าใช้</a> </p>
    </div>
</body>
</html>