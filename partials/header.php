<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/functions.php';

$categories = db_fetch_all("SELECT * FROM categories ORDER BY name ASC");
$user = current_user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mobile Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body class="body-dark">
<header class="header">
    <div class="header__inner">
        <div class="header__left">
            <button class="header__hamburger" id="hamburgerBtn">
                <span></span><span></span><span></span>
            </button>
            <a href="<?php echo BASE_URL; ?>/index.php" class="header__logo">TopMobile</a>
        </div>

        <form action="<?php echo BASE_URL; ?>/index.php" method="get" class="header__search">
            <input type="hidden" name="page" value="home">
            <input type="text"
                   name="q"
                   placeholder="Tìm điện thoại..."
                   value="<?php echo e($_GET['q'] ?? ''); ?>">
        </form>

        <div class="header__right">
            <?php if ($user): ?>
                <span class="header__user">
                    Xin chào, <?php echo e($user['fullname'] ?? ''); ?>
                </span>

                <?php if ( ($user['role'] ?? 0) == 1 || ($user['role'] ?? 0) == 2 ): ?>
                    <a href="<?php echo BASE_URL; ?>/admin/index.php" class="header__link">Admin</a>
                <?php endif; ?>

                <a href="<?php echo BASE_URL; ?>/pages/my_orders.php" class="header__link">Đơn hàng</a>
                <a href="<?php echo BASE_URL; ?>/pages/logout.php" class="header__link">Đăng xuất</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/pages/login.php" class="header__link">Đăng nhập</a>
                <a href="<?php echo BASE_URL; ?>/pages/register.php" class="header__link">Đăng ký</a>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>/pages/cart.php" class="header__cart">
                <span>Giỏ</span>
                <span class="header__cart-badge" id="cartCount">
                    <?php echo cart_total_qty(); ?>
                </span>
            </a>
        </div>
    </div>

    <!-- NAV DESKTOP -->
    <nav class="nav-desktop">
        <a href="<?php echo BASE_URL; ?>/index.php" class="nav-link">Trang chủ</a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo e($cat['slug']); ?>" class="nav-link">
                <?php echo e($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- NAV MOBILE -->
    <div class="nav-mobile-overlay" id="navOverlay"></div>
    <nav class="nav-mobile" id="navMobile">
        <div class="nav-mobile__header">
            <span>Menu</span>
            <button id="navCloseBtn">&times;</button>
        </div>

        <a href="<?php echo BASE_URL; ?>/index.php" class="nav-mobile__link">Trang chủ</a>

        <?php foreach ($categories as $cat): ?>
            <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo e($cat['slug']); ?>" class="nav-mobile__link">
                <?php echo e($cat['name']); ?>
            </a>
        <?php endforeach; ?>

        <a href="<?php echo BASE_URL; ?>/pages/cart.php" class="nav-mobile__link">Giỏ hàng</a>

        <?php if ($user): ?>
            <a href="<?php echo BASE_URL; ?>/pages/my_orders.php" class="nav-mobile__link">Đơn hàng của tôi</a>

            <?php if ( ($user['role'] ?? 0) == 1 || ($user['role'] ?? 0) == 2 ): ?>
                <a href="<?php echo BASE_URL; ?>/admin/index.php" class="nav-mobile__link">Trang quản trị</a>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>/pages/logout.php" class="nav-mobile__link">Đăng xuất</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/pages/login.php" class="nav-mobile__link">Đăng nhập</a>
            <a href="<?php echo BASE_URL; ?>/pages/register.php" class="nav-mobile__link">Đăng ký</a>
        <?php endif; ?>
    </nav>
</header>
<main class="main">
