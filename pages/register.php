<?php
require_once __DIR__ . '/../core/auth.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/index.php');
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $password && login($username, $password)) {
        redirect(BASE_URL . '/index.php');
    } else {
        $msg = 'Sai tài khoản hoặc mật khẩu';
    }
}

require_once __DIR__ . '/../partials/header.php';
?>
<section class="section auth">
    <h1>Đăng nhập</h1>
    <?php if ($msg): ?><p class="form-msg form-msg--error"><?php echo e($msg); ?></p><?php endif; ?>
    <form method="post" class="auth-form">
        <label>Tên đăng nhập</label>
        <input type="text" name="username" required>
        <label>Mật khẩu</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn-primary">Đăng nhập</button>
    </form>
</section>
<?php
require_once __DIR__ . '/../partials/footer.php';
