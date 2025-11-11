<?php
$page_title = "จัดการสินค้า";
require_once 'admin_header.php';

$search_query = $_GET['search_query'] ?? ''; 
$sql_products = "SELECT product_id, image, name, brand, price FROM product";

if (!empty($search_query)) {

    $sql_products .= " WHERE name LIKE ? OR brand LIKE ?"; 
    $search_term = "%" . $search_query . "%"; 
}

$sql_products .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql_products);

if (!empty($search_query)) {
    $stmt->bind_param("ss", $search_term, $search_term); 
}

$stmt->execute();
$all_products = $stmt->get_result();

?>

            <header class="main-header">
                <h1>จัดการสินค้า</h1>
                <a href="admin_product_form.php" class="btn-add">+ เพิ่มสินค้าใหม่</a>
            </header>

            <div class="search-container form-container"> 
                <form method="GET" action="admin_products.php">
                    <div class="form-group inline"> 
                        <label for="search_query">ค้นหาสินค้า:</label>
                        <input type="text" id="search_query" name="search_query" placeholder="ชื่อสินค้า หรือ แบรนด์..." value="<?php echo htmlspecialchars($search_query); ?>"> 
                        <button type="submit" class="btn-submit">ค้นหา</button>
                        <?php if (!empty($search_query)): ?>
                            <a href="admin_products.php" class="btn-clear-search">ล้างการค้นหา</a> 
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <?php if (!empty($search_query)): ?>
                <p class="search-results-info">ผลการค้นหาสำหรับ: <strong><?php echo htmlspecialchars($search_query); ?></strong> (พบ <?php echo $all_products->num_rows; ?> รายการ)</p>
            <?php endif; ?>


            <div class="table-container full-width">
                <table>
                    <thead>
                        <tr>
                            <th>รูป</th>
                            <th>ชื่อสินค้า</th>
                            <th>แบรนด์</th>
                            <th>ราคา</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($all_products->num_rows > 0): ?>
                            <?php while($product = $all_products->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="50">
                                    <?php else: ?>
                                        <span>No Img</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['brand']); ?></td>
                                <td>฿<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <a href="admin_product_form.php?id=<?php echo $product['product_id']; ?>" class="btn-edit">แก้ไข</a>
                                    <a href="admin_product_stock.php?id=<?php echo $product['product_id']; ?>" class="btn-stock">สต็อก/ไซส์</a>
                                    <a href="admin_product_delete.php?id=<?php echo $product['product_id']; ?>" 
                                        class="btn-delete" 
                                        onclick="return confirm('แน่ใจว่าต้องการลบสินค้า <?php echo htmlspecialchars(addslashes($product['name'])); ?>?');">
                                        ลบ
                                     </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">ไม่พบสินค้า<?php echo (!empty($search_query)) ? 'ที่ตรงกับคำค้นหา' : ''; ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main> </div> </body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>