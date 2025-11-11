<?php
require_once 'db_connect.php'; 
$page_title = "แก้ไขออเดอร์"; 
$page_specific_css = "admin_order_edit.css"; 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<h1>Error: ไม่พบ Order ID</h1>";
    exit();
}
$order_id = (int)$_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id_post = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $tracking_number = $_POST['tracking_number'];

    $sql_update = "UPDATE orders SET status = ?, tracking_number = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssi", $status, $tracking_number, $order_id_post);
    
    if ($stmt->execute()) {
        echo "<script>alert('อัปเดตข้อมูลออเดอร์สำเร็จ!'); window.location.href='admin_orders.php';</script>";
        exit();
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดต');</script>";
    }
    $stmt->close();
}

$sql_order = "SELECT o.*, u.fullname, u.email, u.phone 
              FROM orders o 
              LEFT JOIN users u ON o.user_id = u.user_id 
              WHERE o.order_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();

if (!$order) {
    echo "<h1>Error: ไม่พบออเดอร์ ID #$order_id</h1>";
    exit();
}

$sql_details = "SELECT od.*, p.name 
                FROM order_detail od 
                JOIN product p ON od.product_id = p.product_id 
                WHERE od.order_id = ?";
$stmt_details = $conn->prepare($sql_details);
$stmt_details->bind_param("i", $order_id);
$stmt_details->execute();
$order_details = $stmt_details->get_result();

$statuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - ShoeSpace Admin</title>
    
    <link rel="stylesheet" href="admin_style.css">
    
    <?php if ($page_specific_css): ?>
        <link rel="stylesheet" href="<?php echo $page_specific_css; ?>">
    <?php endif; ?>
</head>
<body>

    <div class="admin-layout">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>ShoeSpace Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="admin_dashboard.php" class="<?php echo ($page_title == 'แดชบอร์ด') ? 'active' : ''; ?>">📊 แดชบอร์ด</a>
                <a href="admin_orders.php" class="<?php echo ($page_title == 'จัดการออเดอร์' || $page_title == 'แก้ไขออเดอร์') ? 'active' : ''; ?>">🚚 จัดการออเดอร์</a>
                <a href="admin_products.php" class="<?php echo ($page_title == 'จัดการสินค้า') ? 'active' : ''; ?>">📦 จัดการสินค้า</a>
                <a href="admin_categories.php" class="<?php echo ($page_title == 'จัดการหมวดหมู่') ? 'active' : ''; ?>">🗂️ จัดการหมวดหมู่</a>
                <a href="admin_users.php" class="<?php echo ($page_title == 'จัดการผู้ใช้งาน') ? 'active' : ''; ?>">👨‍💼 จัดการผู้ใช้งาน</a>
                <a href="index.php" class="logout">🚪 กลับหน้าหลัก</a>
            </nav>
        </aside>

        <main class="main-content">
            
            <header class="main-header">
                <h1>แก้ไขออเดอร์ #<?php echo $order['order_id']; ?></h1>
            </header>

            <div class="edit-order-layout">
                
                <div class="card">
                    <h3>ข้อมูลลูกค้า</h3>
                    <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($order['fullname'] ?? 'N/A'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
                    <p><strong>เบอร์โทร:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
                    <hr>
                    <p><strong>ที่อยู่สำหรับจัดส่ง:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? '')); ?></p>
                </div>

                <div class="form-container">
                    <h3>อัปเดตสถานะ</h3>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        
                        <div class="form-group">
                            <label for="status">สถานะออเดอร์:</label>
                            <select id="status" name="status">
                                <?php foreach ($statuses as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php if ($order['status'] == $s) echo 'selected'; ?>>
                                        <?php echo ucfirst($s); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tracking_number">เลขพัสดุ (Tracking):</label>
                            <input type="text" id="tracking_number" name="tracking_number" 
                                   value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-submit">บันทึกการเปลี่ยนแปลง</button>
                        </div>
                    </form>
                </div>

            </div>

            <div class="table-container full-width" style="margin-top: 20px;">
                <h2>รายการสินค้าในออเดอร์</h2>
                <table>
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th>ไซส์</th>
                            <th>จำนวน</th>
                            <th>ราคาต่อหน่วย</th>
                            <th>ราคารวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = $order_details->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['size_label']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>฿<?php echo number_format($item['unit_price'], 2); ?></td>
                            <td>฿<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: bold;">ยอดรวมทั้งหมด:</td>
                            <td style="font-weight: bold;">฿<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </main> </div> </body>
</html>
<?php 
$stmt_order->close();
$stmt_details->close();
$conn->close(); 
?>