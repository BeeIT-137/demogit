<?php
require_once __DIR__ . '/../core/functions.php';
require_admin();
require_once __DIR__ . '/_layout_top.php';

$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if ($action === 'edit') {
        db_execute("
            UPDATE users SET fullname=?, role=? WHERE id=?
        ", [
            $_POST['fullname'], $_POST['role'], $_POST['id']
        ]);
    }
    if ($action === 'password') {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        db_execute("UPDATE users SET password=? WHERE id=?", [$hash, $_POST['id']]);
    }
    redirect(BASE_URL . '/admin/users.php');
}

if ($action === 'delete') {
    $id = (int)$_GET['id'];
    if ($id != current_user()['id']) {
        db_execute("DELETE FROM users WHERE id=?", [$id]);
    }
    redirect(BASE_URL . '/admin/users.php');
}

if ($action === 'edit') {
    $u = db_fetch("SELECT * FROM users WHERE id=?", [$_GET['id']]);
?>
<section class="admin-section">
    <h1>Sửa user</h1>
    <form method="post" class="admin-form">
        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
        <label>Họ tên</label>
        <input type="text" name="fullname" value="<?php echo e($u['fullname']); ?>">

        <label>Role</label>
        <select name="role">
            <option value="0" <?php echo $u['role']==0?'selected':''; ?>>Khách</option>
            <option value="1" <?php echo $u['role']==1?'selected':''; ?>>Admin</option>
            <option value="2" <?php echo $u['role']==2?'selected':''; ?>>Nhân viên</option>
        </select>

        <button class="btn-primary">Lưu</button>
    </form>
</section>
<?php
} elseif ($action === 'password') {
    $u = db_fetch("SELECT * FROM users WHERE id=?", [$_GET['id']]);
?>
<section class="admin-section">
    <h1>Đặt lại mật khẩu</h1>
    <form method="post" class="admin-form">
        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
        <label>Mật khẩu mới</label>
        <input type="password" name="password" required>
        <button class="btn-primary">Cập nhật</button>
    </form>
</section>
<?php
} else {
    $users = db_fetch_all("SELECT * FROM users ORDER BY id DESC");
?>
<section class="admin-section">
    <h1>Người dùng</h1>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th><th>Username</th><th>Họ tên</th><th>Role</th><th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo e($u['username']); ?></td>
                <td><?php echo e($u['fullname']); ?></td>
                <td><?php echo $u['role']; ?></td>
                <td>
                    <a href="?action=edit&id=<?php echo $u['id']; ?>">Sửa</a> |
                    <a href="?action=password&id=<?php echo $u['id']; ?>">Đổi mật khẩu</a> |
                    <?php if ($u['id'] != current_user()['id']): ?>
                        <a href="?action=delete&id=<?php echo $u['id']; ?>" 
                           onclick="return confirm('Xóa user?');">Xóa</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php
}
require_once __DIR__ . '/_layout_bottom.php';
