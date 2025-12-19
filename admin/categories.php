<?php
require_once __DIR__ . '/../core/functions.php';
require_admin();
require_once __DIR__ . '/_layout_top.php';

$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        db_execute("INSERT INTO categories (name, slug) VALUES (?, ?)", [
            $_POST['name'], $_POST['slug']
        ]);
        redirect(BASE_URL . '/admin/categories.php');
    } elseif ($action === 'edit') {
        db_execute("UPDATE categories SET name=?, slug=? WHERE id=?", [
            $_POST['name'], $_POST['slug'], $_POST['id']
        ]);
        redirect(BASE_URL . '/admin/categories.php');
    }
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) db_execute("DELETE FROM categories WHERE id=?", [$id]);
    redirect(BASE_URL . '/admin/categories.php');
}

if ($action === 'add' || $action === 'edit') {
    $cat = ['id' => 0, 'name' => '', 'slug' => ''];
    if ($action === 'edit') {
        $id = (int)($_GET['id'] ?? 0);
        $cat = db_fetch("SELECT * FROM categories WHERE id=?", [$id]);
    }
    ?>
    <section class="admin-section">
        <h1><?php echo $action === 'add' ? 'Thêm danh mục' : 'Sửa danh mục'; ?></h1>
        <form method="post" class="admin-form">
            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
            <label>Tên danh mục</label>
            <input type="text" name="name" value="<?php echo e($cat['name']); ?>" id="nameInput" required>
            <label>Slug</label>
            <input type="text" name="slug" value="<?php echo e($cat['slug']); ?>" id="slugInput" required>
            <button type="submit" class="btn-primary">Lưu</button>
        </form>
    </section>
    <?php
} else {
    $cats = db_fetch_all("SELECT * FROM categories ORDER BY id DESC");
    ?>
    <section class="admin-section">
        <div class="admin-section__header">
            <h1>Danh mục</h1>
            <a href="?action=add" class="btn-primary">Thêm</a>
        </div>
        <table class="admin-table">
            <thead>
            <tr>
                <th>ID</th><th>Tên</th><th>Slug</th><th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($cats as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo e($c['name']); ?></td>
                    <td><?php echo e($c['slug']); ?></td>
                    <td>
                        <a href="?action=edit&id=<?php echo $c['id']; ?>">Sửa</a> |
                        <a href="?action=delete&id=<?php echo $c['id']; ?>"
                           onclick="return confirm('Xóa danh mục?');">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <?php
}
require_once __DIR__ . '/_layout_bottom.php';
