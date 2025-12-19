<?php
require_once __DIR__ . '/../core/functions.php';
require_admin();
require_once __DIR__ . '/_layout_top.php';

$products = db_fetch_all("
    SELECT p.*, c.name AS cat_name
    FROM products p 
    JOIN categories c ON c.id = p.category_id
    ORDER BY p.id DESC
");
?>
<section class="admin-section">
    <div class="admin-section__header">
        <h1>Sản phẩm</h1>
        <a href="<?php echo BASE_URL; ?>/admin/product_form.php?action=add" class="btn-primary">Thêm sản phẩm</a>
    </div>

    <table class="admin-table">
        <thead>
        <tr>
            <th>ID</th><th>Ảnh</th><th>Tên</th><th>Danh mục</th><th>Giá gốc</th>
            <th>Giảm (%)</th><th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><img src="<?php echo BASE_URL . '/' . e($p['thumbnail']); ?>" class="admin-thumb-small"></td>
                <td><?php echo e($p['name']); ?></td>
                <td><?php echo e($p['cat_name']); ?></td>
                <td><?php echo format_price($p['base_price']); ?></td>
                <td><?php echo $p['discount_percent']; ?>%</td>
                <td>
                    <a href="product_form.php?action=edit&id=<?php echo $p['id']; ?>">Sửa</a> |
                    <a href="variants.php?product_id=<?php echo $p['id']; ?>">Biến thể</a> |
                    <a href="product_form.php?action=delete&id=<?php echo $p['id']; ?>"
                       onclick="return confirm('Xóa sản phẩm?');">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
