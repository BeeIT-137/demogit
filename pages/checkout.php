<?php
require_once __DIR__ . '/../core/functions.php';
require_login();
$cart = get_cart();
if (empty($cart)) {
    redirect(BASE_URL . '/pages/cart.php');
}
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $total = cart_total_money();
        db_execute("INSERT INTO orders (user_id, total_money, status) VALUES (?, ?, 'pending')", [
            $user['id'], $total
        ]);
        $orderId = $pdo->lastInsertId();

        foreach ($cart as $item) {
            db_execute("INSERT INTO order_details (order_id, product_variant_id, quantity, price_at_purchase)
                        VALUES (?, ?, ?, ?)", [
                $orderId,
                $item['variant_id'],
                $item['qty'],
                $item['price']
            ]);
            db_execute("UPDATE product_variants SET stock = stock - ? WHERE id = ? AND stock >= ?", [
                $item['qty'], $item['variant_id'], $item['qty']
            ]);
        }

        $_SESSION['cart'] = [];
        $pdo->commit();
        redirect(BASE_URL . '/pages/my_orders.php?success=1');
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Đặt hàng thất bại, vui lòng thử lại!";
    }
}

require_once __DIR__ . '/../partials/header.php';
?>
<section class="section">
    <h1>Thanh toán</h1>
    <?php if (!empty($error)): ?>
        <p class="form-msg form-msg--error"><?php echo e($error); ?></p>
    <?php endif; ?>
    <div class="checkout-info">
        <div>
            <h2>Thông tin khách hàng</h2>
            <p>Họ tên: <?php echo e($user['fullname']); ?></p>
            <p>Tài khoản: <?php echo e($user['username']); ?></p>
        </div>
        <div>
            <h2>Tóm tắt đơn hàng</h2>
            <ul>
                <?php foreach ($cart as $item): ?>
                    <li>
                        <?php echo e($item['name']); ?> (<?php echo e($item['color']); ?> - <?php echo e($item['storage']); ?>)
                        x <?php echo (int)$item['qty']; ?>:
                        <?php echo format_price($item['price'] * $item['qty']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Tổng tiền: <?php echo format_price(cart_total_money()); ?></strong></p>
        </div>
    </div>
    <form method="post">
        <button type="submit" class="btn-primary">Xác nhận đặt hàng</button>
    </form>
</section>
<?php
require_once __DIR__ . '/../partials/footer.php';
