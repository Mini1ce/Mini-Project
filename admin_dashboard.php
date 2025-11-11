<?php
$page_title = "แดชบอร์ด"; 
require_once 'admin_header.php'; 
$result_new_orders = $conn->query("SELECT COUNT(order_id) as count FROM orders WHERE status = 'pending'");
$new_orders_count = $result_new_orders->fetch_assoc()['count'];

$result_sales = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status IN ('paid', 'shipped', 'delivered')");
$total_sales = $result_sales->fetch_assoc()['total'];

$result_users = $conn->query("SELECT COUNT(user_id) as count FROM users");
$users_count = $result_users->fetch_assoc()['count'];

$result_products = $conn->query("SELECT COUNT(product_id) as count FROM product");
$products_count = $result_products->fetch_assoc()['count'];

$sql_recent_orders = "SELECT o.order_id, u.fullname, o.order_date, o.total_amount, o.status 
                      FROM orders o 
                      LEFT JOIN users u ON o.user_id = u.user_id 
                      ORDER BY o.order_date DESC 
                      LIMIT 5";
$recent_orders = $conn->query($sql_recent_orders);

$sql_low_stock = "SELECT p.name, ps.size_label, ps.stock 
                  FROM product_size ps
                  JOIN product p ON ps.product_id = p.product_id
                  WHERE ps.stock < 10
                  ORDER BY ps.stock ASC
                  LIMIT 5";
$low_stock_items = $conn->query($sql_low_stock);

?>
            <header class="main-header">
                <h1>แดชบอร์ด</h1>
                <p>ยินดีต้อนรับ, แอดมิน</p>
            </header>

            <div class="summary-cards">
                <div class="card">
                    <h3>ออเดอร์ใหม่</h3>
                    <p><?php echo $new_orders_count; ?></p>
                </div>
                <div class="card">
                    <h3>ยอดขายรวม</h3>
                    <p>฿<?php echo number_format($total_sales ?? 0, 2); ?></p>
                </div>
                <div class="card">
                    <h3>ลูกค้าทั้งหมด</h3>
                    <p><?php echo $users_count; ?></p>
                </div>
                <div class="card">
                    <h3>สินค้าทั้งหมด</h3>
                    <p><?php echo $products_count; ?></p>
                </div>
            </div>

            <div class="data-tables">
                <div class="table-container">
                    <h2>ออเดอร์ล่าสุด 5 รายการ</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ลูกค้า</th>
                                <th>ยอดรวม</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['fullname'] ?? 'N/A'); ?></td>
                                <td>฿<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><span class="status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h2>สินค้าใกล้หมด (น้อยกว่า 10 ชิ้น)</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>สินค้า</th>
                                <th>ไซส์</th>
                                <th>คงเหลือ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($item = $low_stock_items->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['size_label']); ?></td>
                                <td><?php echo $item['stock']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main> </div> </body>
</html>
<?php $conn->close(); ?>