<?php
require_once __DIR__ . '/../core/functions.php';
require_staff_or_admin();
require_once __DIR__ . '/_layout_top.php';

$id = (int)($_GET['id'] ?? 0);
$order = db_fetch("
    SELECT o.*, u.fullname, u.username
    FROM orders o
    JOIN users u ON u.id = o.user_id
    WHERE o.id = ?
", [$id]);

if (!$order) {
    echo "<p class='admin-section'>Không có đơn này.</p>";
    require_once __DIR__ . '/_layout_bottom.php';
    exit;
}

$details = db_fetch_all("
    SELECT od.*, v.color, v.storage, p.name, p.thumbnail
    FROM order_details od
    JOIN product_variants v ON v.id = od.product_variant_id
    JOIN products p ON p.id = v.product_id
    WHERE od.order_id = ?
", [$id]);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    db_execute("UPDATE orders SET status=? WHERE id=?", [
        $_POST['status'], $id
    ]);
    redirect(BASE_URL . "/admin/order_view.php?id=$id");
}
?>
<section class="admin-section">
    <h1>Chi tiết đơn #<?php echo $order['id']; ?></h1>

    <div class="order-info">
        <p><strong>Khách hàng:</strong> <?php echo e($order['fullname']); ?> (<?php echo e($order['username']); ?>)</p>
        <p><strong>Ngày tạo:</strong> <?php echo $order['created_at']; ?></p>
        <p><strong>Trạng thái:</strong> <?php echo strtoupper($order['status']); ?></p>
    </div>

    <h2>Sản phẩm</h2>
    <table class="admin-table">
        <thead>
        <tr>
            <th>Ảnh</th><th>Tên</th><th>Màu</th><th>ROM</th>
            <th>SL</th><th>Giá mua</th><th>Thành tiền</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($details as $d): ?>
        <tr>
            <td><img src="<?php echo BASE_URL . '/' . $d['thumbnail']; ?>" class="admin-thumb-small"></td>
            <td><?php echo e($d['name']); ?></td>
            <td><?php echo e($d['color']); ?></td>
            <td><?php echo e($d['storage']); ?></td>
            <td><?php echo $d['quantity']; ?></td>
            <td><?php echo format_price($d['price_at_purchase']); ?></td>
            <td><?php echo format_price($d['price_at_purchase'] * $d['quantity']); ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Tổng tiền: <?php echo format_price($order['total_money']); ?></h2>

    <form method="post" class="admin-form">
        <label>Đổi trạng thái</label>
        <select name="status">
            <option value="pending">Pending</option>
            <option value="shipping">Shipping</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <button class="btn-primary">Cập nhật</button>
    </form>
</section>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
