<?php
require_once __DIR__ . '/../core/functions.php';
require_staff_or_admin();
require_once __DIR__ . '/_layout_top.php';

$status = $_GET['status'] ?? 'all';
$where = "";
$params = [];

if ($status !== 'all') {
    $where = "WHERE o.status = ?";
    $params[] = $status;
}

$orders = db_fetch_all("
   SELECT o.*, u.fullname
   FROM orders o
   JOIN users u ON u.id = o.user_id
   $where
   ORDER BY o.id DESC
", $params);
?>
<section class="admin-section">
    <div class="admin-section__header">
        <h1>Đơn hàng</h1>
    </div>

    <div class="filter">
        <a href="?status=all">Tất cả</a>
        <a href="?status=pending">Pending</a>
        <a href="?status=shipping">Shipping</a>
        <a href="?status=completed">Completed</a>
        <a href="?status=cancelled">Cancelled</a>
    </div>

    <table class="admin-table">
        <thead>
        <tr>
            <th>ID</th><th>Khách hàng</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ngày tạo</th><th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?php echo $o['id']; ?></td>
                <td><?php echo e($o['fullname']); ?></td>
                <td><?php echo format_price($o['total_money']); ?></td>
                <td><?php echo e($o['status']); ?></td>
                <td><?php echo $o['created_at']; ?></td>
                <td>
                    <a href="order_view.php?id=<?php echo $o['id']; ?>">Xem</a>
                    |
                    <form action="" method="post" style="display:inline-block;">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <select name="status">
                            <option value="pending">Pending</option>
                            <option value="shipping">Shipping</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button>OK</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php
// đổi trạng thái qua POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    db_execute("UPDATE orders SET status=? WHERE id=?", [
        $_POST['status'], $_POST['order_id']
    ]);
    redirect(BASE_URL . "/admin/orders.php?status=$status");
}

require_once __DIR__ . '/_layout_bottom.php';
