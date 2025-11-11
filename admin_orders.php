<?php
require_once 'db_connect.php'; 

$page_title = "จัดการออเดอร์"; 

$sql_orders = "SELECT o.*, u.fullname 
               FROM orders o 
               LEFT JOIN users u ON o.user_id = u.user_id 
               ORDER BY o.order_date DESC";
$all_orders = $conn->query($sql_orders);
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
            
            <header class="main-header">
                <h1>จัดการออเดอร์</h1>
            </header>

            <div class="table-container full-width">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>ลูกค้า</th>
                            <th>วันที่</th>
                            <th>ยอดรวม</th>
                            <th>สถานะ</th>
                            <th>ที่อยู่</th>
                            <th>Tracking</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $all_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($order['fullname'] ?? 'N/A'); ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></td>
                            <td>฿<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><span class="status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                            <td><?php echo htmlspecialchars($order['shipping_address'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($order['tracking_number'] ?? '-'); ?></td>
                            <td>
                                <a href="admin_order_edit.php?id=<?php echo $order['order_id']; ?>" class="btn-edit">อัปเดต</a>
                                
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </main> </div> </body>
</html>
<?php $conn->close(); ?>