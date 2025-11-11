<?php
session_start();

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
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';
    $sql = "SELECT user_id, password, fullname FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($input_password, $user['password']) || $input_password === $user['password']) {
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $input_username;
            $_SESSION['fullname'] = $user['fullname'];
            
            $message = '<div class="success-message">เข้าสู่ระบบสำเร็จ! กำลังนำทาง...</div>';
            header('refresh:2; url=index.php');
            exit();
        } else {
            $message = '<div class="error-message">รหัสผ่านไม่ถูกต้อง</div>';
        }
    } else {
        $message = '<div class="error-message">ไม่พบชื่อผู้ใช้</div>';
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .error-message { color: red; padding: 10px; border: 1px solid red; background-color: #ffeaea; margin-bottom: 15px; border-radius: 5px; }
        .success-message { color: green; padding: 10px; border: 1px solid green; background-color: #eaffea; margin-bottom: 15px; border-radius: 5px; }
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
        <h1>ลงชื่อเข้าใช้</h1>
        <?php echo $message;?>
        <form action="login.php" method="POST">
            <div>
                <label for="username" >Username</label>
                <input type="text" id="username" name="username" placeholder="กรอกชื่อผู้ใช้" required>
            </div>
            
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="กรอกรหัสผ่าน" required>
            </div>
            
            <div style="text-align: left;">
                <label>
                    <input type="checkbox">จดจำรหัสไว้
                </label>
            </div>
            
            <button type="submit" class="button">ลงชื่อเข้าใช้</button>
        </form>
        
        <p>ยังไม่ได้ลงทะเบียนใช่ไหม <a href="register.php">ลงทะเบียน..</a> </p>
    </div>
</body>
</html>