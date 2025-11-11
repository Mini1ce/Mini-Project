<?php
$page_title = "จัดการผู้ใช้งาน";
require_once 'admin_header.php';

$sql_users = "SELECT u.user_id, u.username, u.fullname, u.email, 
              (a.admin_id IS NOT NULL) as is_admin
              FROM users u 
              LEFT JOIN admin a ON u.user_id = a.user_id 
              ORDER BY u.created_at DESC";
$all_users = $conn->query($sql_users);
?>

            <header class="main-header">
                <h1>จัดการผู้ใช้งาน</h1>
            </header>

            <div class="table-container full-width">
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>ชื่อ-สกุล</th>
                            <th>Email</th>
                            <th>สถานะแอดมิน</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $all_users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <span style="color: green; font-weight: bold;">ใช่</span>
                                <?php else: ?>
                                    <span>ไม่</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <a href="handle_admin.php?user_id=<?php echo $user['user_id']; ?>&action=revoke" 
                                       class="btn-delete" 
                                       onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบสิทธิ์แอดมินของ <?php echo htmlspecialchars($user['username']); ?>?');">
                                        ลบสิทธิ์
                                    </a>
                                <?php else: ?>
                                    <a href="handle_admin.php?user_id=<?php echo $user['user_id']; ?>&action=grant" 
                                       class="btn-submit"
                                       onclick="return confirm('คุณแน่ใจหรือไม่ที่จะแต่งตั้ง <?php echo htmlspecialchars($user['username']); ?> เป็นแอดมิน?');">
                                        แต่งตั้ง
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </main> </div> </body>
</html>
<?php $conn->close(); ?>