<?php
$page_title = "เพิ่มสินค้าใหม่";
require_once 'admin_header.php';

$product_name = "";
$product_brand = "";
$product_price = "";
$product_desc = "";
$product_image = "";
$product_id = null;
$is_edit_mode = false;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_edit_mode = true;
    $product_id = $_GET['id'];
    $page_title = "แก้ไขสินค้า";

    $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $product_name = $product['name'];
        $product_brand = $product['brand'];
        $product_price = $product['price'];
        $product_desc = $product['description'];
        $product_image = $product['image'];
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $image_filename = $_POST['current_image'] ?? null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'imgs/';
        $tmp_name = $_FILES['image']['tmp_name'];
        
        $image_filename = basename($_FILES['image']['name']); 
        $target_file = $upload_dir . $image_filename;

        if (move_uploaded_file($tmp_name, $target_file)) {
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ');</script>";
            $image_filename = $_POST['current_image'] ?? null; 
        }
    }

    $product_id_post = $_POST['product_id'] ?? null;
    $product_name = $_POST['name'];
    $product_brand = $_POST['brand'];
    $product_price = $_POST['price'];
    $product_desc = $_POST['description'];
    
    if ($product_id_post) {
        $sql = "UPDATE product SET name = ?, brand = ?, price = ?, description = ?, image = ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", $product_name, $product_brand, $product_price, $product_desc, $image_filename, $product_id_post);
    } else {
        $sql = "INSERT INTO product (name, brand, price, description, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdss", $product_name, $product_brand, $product_price, $product_desc, $image_filename);
    }
    
    if ($stmt->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ!'); window.location.href='admin_products.php';</script>";
        exit();
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

?>

            <header class="main-header">
                <h1><?php echo $page_title; ?></h1>
            </header>

            <div class="form-container">
                <form method="POST" action="admin_product_form.php" enctype="multipart/form-data">
                    
                    <?php if ($is_edit_mode): ?>
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">ชื่อสินค้า:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product_name); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="brand">แบรนด์:</label>
                        <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product_brand); ?>">
                    </div>

                    <div class="form-group">
                        <label for="price">ราคา:</label>
                        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product_price); ?>" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="image">รูปภาพสินค้า:</label>
                        <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/webp">
                        
                        <?php if ($is_edit_mode && !empty($product_image)): ?>
                            <div class="current-image-preview">
                                <p>รูปปัจจุบัน:</p>
                                <img src="images/<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product_name); ?>">
                                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product_image); ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="description">รายละเอียดสินค้า:</label>
                        <textarea id="description" name="description" rows="8"><?php echo htmlspecialchars($product_desc); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">บันทึกสินค้า</button>
                    </div>
                </form>
            </div>

        </main> </div> </body>
</html>
<?php $conn->close(); ?>