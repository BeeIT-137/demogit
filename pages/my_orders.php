<?php
require_once __DIR__ . '/../core/functions.php';
require_login();
$user = current_user();

$orders = db_fetch_all("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC", [$user['id']]);

require_once __DIR__ . '/../partials/header.php';
?>
<section class="section">
    <h1>Đơn hàng của tôi</h1>
    <?php if (isset($_GET['success'])): ?>
        <p class="form-msg">Đặt hàng thành công!</p>
    <?php endif; ?>
    <?php if (empty($orders)): ?>
        <p>Bạn chưa có đơn hàng nào.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
            $details = db_fetch_all("
                SELECT od.*, v.color, v.storage, p.name, p.thumbnail
                FROM order_details od
                JOIN product_variants v ON v.id = od.product_variant_id
                JOIN products p ON p.id = v.product_id
                WHERE od.order_id = ?
            ", [$order['id']]);
            ?>
            <div class="order-card">
                <div class="order-card__header">
                    <div>
                        <p>Mã đơn: #<?php echo $order['id']; ?></p>
                        <p>Ngày tạo: <?php echo $order['created_at']; ?></p>
                    </div>
                    <div class="order-card__status order-status--<?php echo e($order['status']); ?>">
                        <?php echo strtoupper($order['status']); ?>
                    </div>
                </div>
                <div class="order-card__body">
                    <?php foreach ($details as $d): ?>
                        <div class="order-item">
                            <img src="<?php echo BASE_URL . '/' . e($d['thumbnail']); ?>" alt="">
                            <div>
                                <p><?php echo e($d['name']); ?></p>
                                <p><?php echo e($d['color']); ?> - <?php echo e($d['storage']); ?></p>
                                <p>SL: <?php echo (int)$d['quantity']; ?></p>
                            </div>
                            <div class="order-item__price">
                                <?php echo format_price($d['price_at_purchase'] * $d['quantity']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="order-card__footer">
                    <strong>Tổng tiền: <?php echo format_price($order['total_money']); ?></strong>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<?php
require_once __DIR__ . '/../partials/footer.php';
