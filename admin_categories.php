<?php
$page_title = "จัดการหมวดหมู่";
require_once 'admin_header.php';

function create_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return $text;
}

if (isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    $slug = create_slug($name);
    $stmt = $conn->prepare("INSERT INTO category (name, slug, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $slug, $description);

    if ($stmt->execute()) {
        $message = "เพิ่มหมวดหมู่ '{$name}' สำเร็จ!";
        header("Location: admin_categories.php?success=" . urlencode($message));
        exit();
    } else {
        $error = "เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่: " . $stmt->error;
        header("Location: admin_categories.php?error=" . urlencode($error));
        exit();
    }
    $stmt->close();
}

$sql_categories = "SELECT * FROM category ORDER BY name ASC";
$all_categories = $conn->query($sql_categories);
?>

            <header class="main-header">
                <h1>จัดการหมวดหมู่</h1>
            </header>

            <?php if (isset($_GET['success'])): ?>
                <div style="padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div style="padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <div class="data-tables">
                <div class="form-container">
                    <h3>เพิ่มหมวดหมู่ใหม่</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label for="name">ชื่อหมวดหมู่:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">รายละเอียด:</label>
                            <textarea id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="add_category" class="btn-submit">เพิ่ม</button>
                        </div>
                    </form>
                </div>

                <div class="table-container">
                    <h3>หมวดหมู่ทั้งหมด</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ชื่อ</th>
                                <th>รายละเอียด</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($cat = $all_categories->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $cat['category_id']; ?></td>
                                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                <td><?php echo htmlspecialchars($cat['description']); ?></td>
                                <td>
                                    <a href="edit_category.php?id=<?php echo $cat['category_id']; ?>" class="btn-edit">แก้ไข</a>
                                    <a href="delete_category.php?id=<?php echo $cat['category_id']; ?>&action=delete" 
                                       class="btn-delete" 
                                       onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่: <?php echo htmlspecialchars($cat['name']); ?>? การลบจะรวมถึงสินค้าที่เชื่อมโยงด้วย');" >
                                        ลบ
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main> </div> </body>
</html>
<?php $conn->close(); ?>