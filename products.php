<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>products | สินค้าทั้งหมด</title>
    <link rel="stylesheet" href="products.css">
    
</head>

<body>
    <header>
        <nav>
            <div class="logo">ShoeSpace</div>
            <ul class="nav-links">
                <li><a href="index.php">หน้าแรก</a></li>
                <li><a href="products.php">สินค้าทั้งหมด</a></li>
                <li><a href="promotion.php">โปรโมชั่น</a></li>
                <li><a href="about.php">เกี่ยวกับเรา</a></li>
                <li>
                    <form action="products.php" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search.." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit" style="display:none;"></button>
                    </form>
                </li>
            </ul>
            <div class="nav-icons">
                <a href="profile.php">👤Profile</a>
                <a href="cart.php">🛒Cart</a>
            </div>
        </nav>
    </header>

    <div class="page-container">
        <aside class="filters">
            <h3>ตัวกรองสินค้า</h3>

            <div class="filter-group">
                <h4>หมวดหมู่</h4>
                <ul class="filter-list">
                    <?php
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "shoespace";
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $category_sql = "SELECT * FROM category ORDER BY name ASC";
                    $category_result = $conn->query($category_sql);
                    if ($category_result->num_rows > 0) {
                        while ($cat_row = $category_result->fetch_assoc()) {
                            echo '<li><a href="products.php?category=' . $cat_row['category_id'] . '">' . htmlspecialchars($cat_row['name']) . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>

            <div class="filter-group">
                <h4>ช่วงราคา</h4>
                <ul class="filter-list">
                    <li><a href="products.php?price=0-3000">฿0 - ฿3,000</a></li>
                    <li><a href="products.php?price=3001-5000">฿3,001 - ฿5,000</a></li>
                    <li><a href="products.php?price=5001-10000">฿5,001 - ฿10,000</a></li>
                    <li><a href="products.php?price=10001-99999">มากกว่า ฿10,000</a></li>
                </ul>
            </div>

            <a href="products.php" class="clear-filter-btn">ล้างตัวกรองทั้งหมด</a>
        </aside>
        <main class="product-listing">
            <div class="product-grid">
                <?php
                function ref_values($arr){
                    if (strnatcmp(phpversion(),'5.3') >= 0) 
                    {
                        $refs = array();
                        foreach($arr as $key => $value)
                            $refs[$key] = &$arr[$key];
                        return $refs;
                    }
                    return $arr;
                }
    
                $base_sql = "SELECT DISTINCT p.* FROM product p
                             LEFT JOIN product_category pc ON p.product_id = pc.product_id
                             LEFT JOIN category c ON pc.category_id = c.category_id";
                             
                $where_clauses = [];
                $params = [];
                $types = '';
                if (!empty($_GET['category'])) {
                    $where_clauses[] = "pc.category_id = ?";
                    $params[] = $_GET['category'];
                    $types .= 'i';
                }

                if (!empty($_GET['price'])) {
                    list($min_price, $max_price) = explode('-', $_GET['price']);
                    $where_clauses[] = "p.price BETWEEN ? AND ?";
                    $params[] = (float)$min_price;
                    $params[] = (float)$max_price;
                    $types .= 'dd'; 
                }
                if (!empty($_GET['search'])) {
                    $search_query = '%' . $_GET['search'] . '%'; 
                    $where_clauses[] = "(p.name LIKE ? OR p.brand LIKE ? OR c.name LIKE ?)";
                    
                    $params[] = $search_query;
                    $params[] = $search_query;
                    $params[] = $search_query;
                    $types .= 'sss';
                }


                if (!empty($where_clauses)) {
                    $base_sql .= " WHERE " . implode(" AND ", $where_clauses);
                }
                
                $base_sql .= " ORDER BY p.product_id DESC";
                if (isset($conn)) $conn->close();
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

                $stmt = $conn->prepare($base_sql);
                if (!empty($params)) {
                    $bind_params = array_merge([$types], $params);
                    call_user_func_array([$stmt, 'bind_param'], ref_values($bind_params));
                }

                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="product-card">';
                        echo '  <div class="product-image"><img src="images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '"></div>';
                        echo '  <div class="product-info">';
                        echo '    <p class="product-brand">' . htmlspecialchars($row['brand']) . '</p>';
                        echo '    <h3 class="product-name">' . htmlspecialchars($row['name']) . '</h3>';
                        echo '    <p class="product-price">฿' . number_format($row['price'], 2) . '</p>';
                        echo '    <a href="product-detail.php?id=' . $row['product_id'] . '" class="product-details-btn">รายละเอียด</a>';
                        echo '    <form action="add_to_cart.php" method="POST">';
                        echo '      <input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
                        echo '      <input type="hidden" name="size" value="40">'; 
                        echo '      <input type="hidden" name="quantity" value="1">'; 
                        echo '      <button type="submit" class="add-to-cart-btn">เพิ่มลงตะกร้า</button>';
                        echo '    </form>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-results">ไม่พบสินค้าที่ตรงกับเงื่อนไข</p>';
                }
                $stmt->close();
                $conn->close();
                ?>
            </div>
        </main>
    </div>
</body>

</html>