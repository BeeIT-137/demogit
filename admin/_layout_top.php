<?php
require_once __DIR__ . '/../core/functions.php';
require_staff_or_admin();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Mobile Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar__logo">
            <a href="<?php echo BASE_URL; ?>/admin/index.php">Admin Mobile</a>
        </div>
        <nav class="admin-sidebar__nav">
            <?php if (is_admin()): ?>
                <a href="<?php echo BASE_URL; ?>/admin/categories.php">Danh mục</a>
                <a href="<?php echo BASE_URL; ?>/admin/products.php">Sản phẩm</a>
                <a href="<?php echo BASE_URL; ?>/admin/users.php">Người dùng</a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>/admin/orders.php">Đơn hàng</a>
            <a href="<?php echo BASE_URL; ?>/index.php" target="_blank">Xem website</a>
            <span class="admin-sidebar__user">
                <?php echo e($user['fullname']); ?> (<?php echo $user['role']==1?'Admin':'Staff'; ?>)
            </span>
        </nav>
    </aside>
    <main class="admin-main">
