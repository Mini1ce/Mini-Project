<?php
$page_title = "แก้ไขหมวดหมู่";
require_once 'admin_header.php'; 

function create_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return $text;
}

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current_category = null;
$error = '';

if (isset($_POST['update_category'])) {
    $new_name = $_POST['name'];
    $new_description = $_POST['description'];
    $new_slug = create_slug($new_name);
    $stmt = $conn->prepare("UPDATE category SET name = ?, slug = ?, description = ? WHERE category_id = ?");
    $stmt->bind_param("sssi", $new_name, $new_slug, $new_description, $category_id);

    if ($stmt->execute()) {
        $message = "แก้ไขหมวดหมู่ '{$new_name}' สำเร็จแล้ว!";
        header("Location: admin_categories.php?success=" . urlencode($message));
        exit();
    } else {
        $error = "เกิดข้อผิดพลาดในการแก้ไข: " . $stmt->error;
    }
    $stmt->close();
}

if ($category_id > 0) {
    $stmt = $conn->prepare("SELECT name, description FROM category WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $current_category = $result->fetch_assoc();
    } else {
        header("Location: admin_categories.php?error=" . urlencode("ไม่พบ ID หมวดหมู่ที่ต้องการแก้ไข"));
        exit();
    }
    $stmt->close();
} else {
    header("Location: admin_categories.php?error=" . urlencode("กรุณาระบุ ID หมวดหมู่"));
    exit();
}
?>

            <header class="main-header">
                <h1>แก้ไขหมวดหมู่: <?php echo htmlspecialchars($current_category['name']); ?></h1>
            </header>

            <?php if (!empty($error)): ?>
                <div style="padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="form-container full-width">
                <h3>แก้ไขข้อมูลหมวดหมู่</h3>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $category_id; ?>"> 
                    
                    <div class="form-group">
                        <label for="name">ชื่อหมวดหมู่:</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo htmlspecialchars($current_category['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">รายละเอียด:</label>
                        <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($current_category['description']); ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="update_category" class="btn-submit">บันทึกการแก้ไข</button>
                        <a href="admin_categories.php" class="btn-cancel">ยกเลิก</a>
                    </div>
                </form>
            </div>

        </main> </div> </body>
</html>
<?php $conn->close(); ?>