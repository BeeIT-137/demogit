<?php
require_once __DIR__ . '/_layout_top.php';
?>
<section class="admin-section">
    <h1>Dashboard</h1>
    <p>Chào mừng đến khu vực quản trị.</p>
    <div class="admin-stat-grid">
        <div class="admin-card">
            <h3>Tổng sản phẩm</h3>
            <p><?php echo db_fetch("SELECT COUNT(*) AS c FROM products")['c']; ?></p>
        </div>
        <div class="admin-card">
            <h3>Tổng đơn hàng</h3>
            <p><?php echo db_fetch("SELECT COUNT(*) AS c FROM orders")['c']; ?></p>
        </div>
        <div class="admin-card">
            <h3>Tổng người dùng</h3>
            <p><?php echo db_fetch("SELECT COUNT(*) AS c FROM users")['c']; ?></p>
        </div>
    </div>
</section>
<?php
require_once __DIR__ . '/_layout_bottom.php';
