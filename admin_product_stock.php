<?php
$page_title = "จัดการสต็อกและไซส์";
require_once 'admin_header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<h1>Error: ไม่พบ Product ID</h1>";
    exit();
}
$product_id = (int)$_GET['id'];

$message = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_size'])) {
    $new_size_label = trim($_POST['new_size_label']);
    $new_stock = (int)$_POST['new_stock'];

    if (!empty($new_size_label) && $new_stock >= 0) {
        $stmt_check = $conn->prepare("SELECT size_id FROM product_size WHERE product_id = ? AND size_label = ?");
        $stmt_check->bind_param("is", $product_id, $new_size_label);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows == 0) {
            $stmt_add = $conn->prepare("INSERT INTO product_size (product_id, size_label, stock) VALUES (?, ?, ?)");
            $stmt_add->bind_param("isi", $product_id, $new_size_label, $new_stock);
            if ($stmt_add->execute()) {
                $message = "<p class='msg-success'>เพิ่มไซส์ '$new_size_label' สำเร็จ</p>";
            } else {
                $message = "<p class='msg-error'>เกิดข้อผิดพลาดในการเพิ่มไซส์: " . $conn->error . "</p>";
            }
            $stmt_add->close();
        } else {
            $message = "<p class='msg-error'>ไซส์ '$new_size_label' มีอยู่แล้วสำหรับสินค้านี้</p>";
        }
        $stmt_check->close();
    } else {
         $message = "<p class='msg-error'>กรุณากรอกไซส์และจำนวนสต็อกให้ถูกต้อง</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stock'])) {
    $success_count = 0;
    $error_count = 0;
    $stmt_update = $conn->prepare("UPDATE product_size SET stock = ? WHERE size_id = ? AND product_id = ?");

    foreach ($_POST['stock'] as $size_id => $new_stock) {
        $stock_value = (int)$new_stock;
        if ($stock_value >= 0) {
            $stmt_update->bind_param("iii", $stock_value, $size_id, $product_id);
            if ($stmt_update->execute()) {
                $success_count++;
            } else {
                $error_count++;
            }
        } else {
             $error_count++;
        }
    }
    $stmt_update->close();
    if ($success_count > 0) {
         $message .= "<p class='msg-success'>อัปเดตสต็อกสำเร็จ $success_count รายการ</p>";
    }
     if ($error_count > 0) {
         $message .= "<p class='msg-error'>เกิดข้อผิดพลาดในการอัปเดต $error_count รายการ</p>";
    }
}


if (isset($_GET['delete_size']) && is_numeric($_GET['delete_size'])) {
     $size_id_to_delete = (int)$_GET['delete_size'];
     $stmt_delete = $conn->prepare("DELETE FROM product_size WHERE size_id = ? AND product_id = ?");
     $stmt_delete->bind_param("ii", $size_id_to_delete, $product_id);
     if ($stmt_delete->execute()) {
          $message = "<p class='msg-success'>ลบไซส์สำเร็จ</p>";
     } else {
          $message = "<p class='msg-error'>เกิดข้อผิดพลาดในการลบไซส์: " . $conn->error . "</p>";
     }
     $stmt_delete->close();
}

$stmt_product = $conn->prepare("SELECT name, brand FROM product WHERE product_id = ?");
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$product = $stmt_product->get_result()->fetch_assoc();
$stmt_product->close();

if (!$product) {
    echo "<h1>Error: ไม่พบสินค้า ID #$product_id</h1>";
    exit();
}

$sql_sizes = "SELECT size_id, size_label, stock FROM product_size WHERE product_id = ? ORDER BY size_label ASC";
$stmt_sizes = $conn->prepare($sql_sizes);
$stmt_sizes->bind_param("i", $product_id);
$stmt_sizes->execute();
$existing_sizes = $stmt_sizes->get_result();

?>

            <header class="main-header">
                <h1>จัดการสต็อก: <?php echo htmlspecialchars($product['name']); ?></h1>
                <a href="admin_products.php" class="btn-back">← กลับไปหน้ารายการสินค้า</a>
            </header>

            <?php echo $message;?>

            <div class="stock-management-layout">

                <div class="form-container">
                    <h3>อัปเดตสต็อกปัจจุบัน</h3>
                    <?php if ($existing_sizes->num_rows > 0): ?>
                        <form method="POST">
                            <table class="stock-table">
                                <thead>
                                    <tr>
                                        <th>ไซส์</th>
                                        <th>จำนวนสต็อกปัจจุบัน</th>
                                        <th>ลบ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($size = $existing_sizes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($size['size_label']); ?></td>
                                        <td>
                                            <input type="number" 
                                                   name="stock[<?php echo $size['size_id']; ?>]" 
                                                   value="<?php echo $size['stock']; ?>" 
                                                   min="0" 
                                                   required>
                                        </td>
                                        <td>
                                            <a href="admin_product_stock.php?id=<?php echo $product_id; ?>&delete_size=<?php echo $size['size_id']; ?>" 
                                               class="btn-delete" 
                                               onclick="return confirm('แน่ใจว่าต้องการลบไซส์ <?php echo htmlspecialchars($size['size_label']); ?>?');">
                                               ลบ
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <div class="form-actions">
                                <button type="submit" name="update_stock" class="btn-submit">บันทึกการเปลี่ยนแปลงสต็อก</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <p>ยังไม่มีการกำหนดไซส์สำหรับสินค้านี้</p>
                    <?php endif; ?>
                </div>

                <div class="form-container">
                    <h3>เพิ่มไซส์ใหม่</h3>
                    <form method="POST">
                        <div class="form-group inline">
                            <label for="new_size_label">ไซส์:</label>
                            <input type="text" id="new_size_label" name="new_size_label" placeholder="เช่น 40, US 9, Free" required>
                        </div>
                         <div class="form-group inline">
                            <label for="new_stock">จำนวนเริ่มต้น:</label>
                            <input type="number" id="new_stock" name="new_stock" value="0" min="0" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="add_size" class="btn-add">เพิ่มไซส์</button>
                        </div>
                    </form>
                </div>

            </div>

        </main> </div> </body>
</html>
<?php 
$stmt_sizes->close();
$conn->close(); 
?>