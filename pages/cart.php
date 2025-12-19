<?php
require_once __DIR__ . '/../partials/header.php';

$cart = get_cart();
?>
<section class="section">
    <h1>Giỏ hàng</h1>
    <?php if (empty($cart)): ?>
        <p>Giỏ hàng của bạn đang trống.</p>
        <a href="<?php echo BASE_URL; ?>/index.php" class="btn-primary">Tiếp tục mua sắm</a>
    <?php else: ?>
        <div class="cart-table">
            <?php foreach ($cart as $key => $item): ?>
                <div class="cart-row" data-key="<?php echo e($key); ?>" data-variant-id="<?php echo (int)$item['variant_id']; ?>">
                    <div class="cart-row__thumb">
                        <img src="<?php echo BASE_URL . '/' . e($item['thumbnail']); ?>" alt="">
                    </div>
                    <div class="cart-row__info">
                        <h2><?php echo e($item['name']); ?></h2>
                        <p><?php echo e($item['color']); ?> - <?php echo e($item['storage']); ?></p>
                        <p>Giá: <span class="cart-price-unit"><?php echo format_price($item['price']); ?></span></p>
                    </div>
                    <div class="cart-row__qty">
                        <button class="cart-btn cart-dec">-</button>
                        <span class="cart-qty"><?php echo (int)$item['qty']; ?></span>
                        <button class="cart-btn cart-inc">+</button>
                    </div>
                    <div class="cart-row__total">
                        <span class="cart-line-total">
                            <?php echo format_price($item['price'] * $item['qty']); ?>
                        </span>
                        <button class="cart-btn cart-remove">Xóa</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="cart-summary">
            <p>Tổng tiền: <span id="cartTotal"><?php echo format_price(cart_total_money()); ?></span></p>
            <a href="<?php echo BASE_URL; ?>/pages/checkout.php" class="btn-primary">Tiến hành đặt hàng</a>
        </div>
    <?php endif; ?>
</section>
<?php
require_once __DIR__ . '/../partials/footer.php';
