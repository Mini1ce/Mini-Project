<?php
// 1. เชื่อมต่อ DB
require_once 'db_connect.php';

// เริ่ม Session และสร้างฟังก์ชันสำหรับตรวจสอบสถานะ Admin
session_start();

/**
 * ตรวจสอบสถานะผู้ดูแลระบบโดยใช้ user_id และตาราง admin
 * @param int $user_id
 * @param mysqli $conn
 * @return bool
 */
function isAdmin($user_id, $conn) {
    if (!$user_id) {
        return false;
    }
    
    // ตรวจสอบในตาราง admin
    $sql = "SELECT admin_id FROM admin WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $is_admin = $stmt->num_rows > 0;
        $stmt->close();
        return $is_admin;
    }
    return false;
}

if (!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'], $conn)) {
    header('Location: index.php');
    exit();
}

$page_title = $page_title ?? "Admin Panel"; 

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - ShoeSpace Admin</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

    <div class="admin-layout">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>ShoeSpace Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="admin_dashboard.php" class="<?php echo ($page_title == 'แดชบอร์ด') ? 'active' : ''; ?>">📊 แดชบอร์ด</a>
                <a href="admin_orders.php" class="<?php echo ($page_title == 'จัดการออเดอร์') ? 'active' : ''; ?>">🚚 จัดการออเดอร์</a>
                <a href="admin_products.php" class="<?php echo ($page_title == 'จัดการสินค้า') ? 'active' : ''; ?>">📦 จัดการสินค้า</a>
                <a href="admin_categories.php" class="<?php echo ($page_title == 'จัดการหมวดหมู่') ? 'active' : ''; ?>">🗂️ จัดการหมวดหมู่</a>
                <a href="admin_users.php" class="<?php echo ($page_title == 'จัดการผู้ใช้งาน') ? 'active' : ''; ?>">👨‍💼 จัดการผู้ใช้งาน</a>
                <a href="index.php" class="logout">🚪 กลับหน้าหลัก</a>
            </nav>
        </aside>

        <main class="main-content">