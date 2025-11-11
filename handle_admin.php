<?php
require_once 'db_connect.php';
session_start(); 

if (!isset($_GET['user_id']) || !isset($_GET['action'])) {
    header("Location: admin_users.php?error=missing_params");
    exit();
}

$user_id = $_GET['user_id'];
$action = $_GET['action'];

if ($action == 'grant') {
    $check_sql = "SELECT admin_id FROM admin WHERE user_id = $user_id";
    $result = $conn->query($check_sql);

    if ($result->num_rows == 0) {
        $insert_sql = "INSERT INTO admin (user_id) VALUES ($user_id)";
        
        if ($conn->query($insert_sql) === TRUE) {
            header("Location: admin_users.php?success=grant");
        } else {
            header("Location: admin_users.php?error=grant_failed&msg=" . urlencode($conn->error));
        }
    } else {
        header("Location: admin_users.php?error=already_admin");
    }

} elseif ($action == 'revoke') {
    $delete_sql = "DELETE FROM admin WHERE user_id = $user_id";
    
    if ($conn->query($delete_sql) === TRUE) {
        header("Location: admin_users.php?success=revoke");
    } else {
        header("Location: admin_users.php?error=revoke_failed&msg=" . urlencode($conn->error));
    }
} else {
    header("Location: admin_users.php?error=invalid_action");
}

$conn->close();
exit();
?>