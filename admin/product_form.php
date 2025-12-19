<?php
require_once __DIR__ . '/../core/functions.php';
require_admin();
require_once __DIR__ . '/_layout_top.php';

$action = $_GET['action'] ?? 'add';
$product = [
    'id' => 0, 'name' => '', 'slug' => '', 'description' => '',
    'thumbnail' => '', 'base_price' => 0, 'discount_percent' => 0,
    'category_id' => ''
];

$categories = db_fetch_all("SELECT * FROM categories ORDER BY name ASC");

if ($action == 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $product = db_fetch("SELECT * FROM products WHERE id=?", [$id]);
}

if ($action == 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    db_execute("DELETE FROM products WHERE id=?", [$id]);
    redirect(BASE_URL . '/admin/products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        $_POST['name'],
        $_POST['slug'],
        $_POST['description'],
        $_POST['thumbnail'],
        $_POST['base_price'],
        $_POST['discount_percent'],
        $_POST['category_id']
    ];

    if ($action == 'add') {
        db_execute("
            INSERT INTO products (name, slug, description, thumbnail, base_price, discount_percent, category_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ", $data);
    } else {
        $data[] = $_POST['id'];
        db_execute("
            UPDATE products SET name=?, slug=?, description=?, thumbnail=?, base_price=?, discount_percent=?, category_id=?
            WHERE id=?
        ", $data);
    }
    redirect(BASE_URL . '/admin/products.php');
}
?>
<section class="admin-section">
    <h1><?php echo $action==='add' ? 'Thêm sản phẩm' : 'Sửa sản phẩm'; ?></h1>

    <form method="post" class="admin-form">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

        <label>Tên sản phẩm</label>
        <input type="text" name="name" id="nameInput" required value="<?php echo e($product['name']); ?>">

        <label>Slug</label>
        <input type="text" name="slug" id="slugInput" required value="<?php echo e($product['slug']); ?>">

        <label>Ảnh thumbnail (URL hoặc path)</label>
        <input type="text" name="thumbnail" placeholder="assets/img/xxx.jpg"
               value="<?php echo e($product['thumbnail']); ?>">

        <label>Danh mục</label>
        <select name="category_id" required>
            <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c['id']; ?>"
                    <?php echo $product['category_id']==$c['id'] ? 'selected' : ''; ?>>
                    <?php echo e($c['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Giá gốc</label>
        <input type="number" name="base_price" value="<?php echo $product['base_price']; ?>">

        <label>Giảm giá (%)</label>
        <input type="number" name="discount_percent" value="<?php echo $product['discount_percent']; ?>">

        <label>Mô tả</label>
        <textarea name="description" rows="5"><?php echo e($product['description']); ?></textarea>

        <button type="submit" class="btn-primary">Lưu</button>
    </form>
</section>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
