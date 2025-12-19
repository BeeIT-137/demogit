<?php
require_once __DIR__ . '/../core/functions.php';
require_admin();
require_once __DIR__ . '/_layout_top.php';

$product_id = (int)($_GET['product_id'] ?? 0);
$product = db_fetch("SELECT * FROM products WHERE id=?", [$product_id]);

if (!$product) {
    echo "<p class='admin-section'>Sản phẩm không tồn tại.</p>";
    require_once __DIR__ . '/_layout_bottom.php';
    exit;
}

$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if ($action === 'add') {
        db_execute("
            INSERT INTO product_variants (product_id, color, storage, price, stock)
            VALUES (?, ?, ?, ?, ?)
        ", [
            $product_id, $_POST['color'], $_POST['storage'],
            $_POST['price'], $_POST['stock']
        ]);
    } elseif ($action === 'edit') {
        db_execute("
            UPDATE product_variants 
            SET color=?, storage=?, price=?, stock=?
            WHERE id=?
        ", [
            $_POST['color'], $_POST['storage'], $_POST['price'],
            $_POST['stock'], $_POST['id']
        ]);
    }
    redirect(BASE_URL . '/admin/variants.php?product_id=' . $product_id);
}

if ($action === 'delete') {
    $id = (int)$_GET['id'];
    db_execute("DELETE FROM product_variants WHERE id=?", [$id]);
    redirect(BASE_URL . '/admin/variants.php?product_id='. $product_id);
}

if ($action === 'add' || $action === 'edit') {
    $v = ['id'=>0,'color'=>'','storage'=>'','price'=>'','stock'=>''];
    if ($action === 'edit') {
        $v = db_fetch("SELECT * FROM product_variants WHERE id=?", [$_GET['id']]);
    }
?>
<section class="admin-section">
    <h1>Biến thể - <?php echo e($product['name']); ?></h1>
    <form method="post" class="admin-form">
        <input type="hidden" name="id" value="<?php echo $v['id']; ?>">

        <label>Màu sắc</label>
        <input type="text" name="color" required value="<?php echo e($v['color']); ?>">

        <label>Dung lượng</label>
        <input type="text" name="storage" required value="<?php echo e($v['storage']); ?>">

        <label>Giá</label>
        <input type="number" name="price" required value="<?php echo e($v['price']); ?>">

        <label>Tồn kho</label>
        <input type="number" name="stock" required value="<?php echo e($v['stock']); ?>">

        <button class="btn-primary">Lưu</button>
    </form>
</section>
<?php
} else {
    $variants = db_fetch_all("SELECT * FROM product_variants WHERE product_id=?", [$product_id]);
?>
<section class="admin-section">
    <div class="admin-section__header">
        <h1>Biến thể: <?php echo e($product['name']); ?></h1>
        <a href="?product_id=<?php echo $product_id; ?>&action=add" class="btn-primary">Thêm biến thể</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th><th>Màu</th><th>ROM</th><th>Giá</th><th>Tồn</th><th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($variants as $v): ?>
            <tr>
                <td><?php echo $v['id']; ?></td>
                <td><?php echo e($v['color']); ?></td>
                <td><?php echo e($v['storage']); ?></td>
                <td><?php echo format_price($v['price']); ?></td>
                <td><?php echo $v['stock']; ?></td>
                <td>
                    <a href="?product_id=<?php echo $product_id; ?>&action=edit&id=<?php echo $v['id']; ?>">Sửa</a> |
                    <a href="?product_id=<?php echo $product_id; ?>&action=delete&id=<?php echo $v['id']; ?>"
                       onclick="return confirm('Xóa biến thể?')">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php
}
require_once __DIR__ . '/_layout_bottom.php';
