<?php
require_once 'admin_header.php'; 

if (!isset($_GET['id'])) {
    header("Location: admin_categories.php?error=" . urlencode("ไม่ระบุ ID หมวดหมู่ที่ต้องการลบ"));
    exit();
}

$category_id = (int)$_GET['id'];
$stmt = $conn->prepare("DELETE FROM category WHERE category_id = ?");
$stmt->bind_param("i", $category_id);

if ($stmt->execute()) {
    header("Location: admin_categories.php?success=" . urlencode("ลบหมวดหมู่ ID: {$category_id} สำเร็จแล้ว!"));
} else {
    header("Location: admin_categories.php?error=" . urlencode("ลบหมวดหมู่ ID: {$category_id} ล้มเหลว: " . $stmt->error));
}
$stmt->close();

$conn->close();
exit();
?>