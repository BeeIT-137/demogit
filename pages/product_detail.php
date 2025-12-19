<?php
require_once __DIR__ . '/../partials/header.php';

$slug = $_GET['slug'] ?? '';
$product = db_fetch("SELECT p.*, c.name AS cat_name 
                     FROM products p 
                     JOIN categories c ON c.id = p.category_id
                     WHERE p.slug = ?", [$slug]);

if (!$product) {
    echo "<p class='section'>Sản phẩm không tồn tại.</p>";
    require_once __DIR__ . '/../partials/footer.php';
    exit;
}

$variants = db_fetch_all("SELECT * FROM product_variants WHERE product_id = ? ORDER BY price ASC", [$product['id']]);

$colors = [];
$storages = [];
foreach ($variants as $v) {
    if (!in_array($v['color'], $colors)) $colors[] = $v['color'];
    if (!in_array($v['storage'], $storages)) $storages[] = $v['storage'];
}

$discount = (int)$product['discount_percent'];
?>
<section class="section product-detail">
    <div class="product-detail__left">
        <img src="<?php echo BASE_URL . '/' . e($product['thumbnail']); ?>" alt="<?php echo e($product['name']); ?>">
    </div>
    <div class="product-detail__right">
        <h1><?php echo e($product['name']); ?></h1>
        <p class="product-detail__cat"><?php echo e($product['cat_name']); ?></p>
        <div class="product-detail__variant">
            <div class="variant-group">
                <span>Màu sắc:</span>
                <div class="variant-options" id="colorOptions">
                    <?php foreach ($colors as $idx => $color): ?>
                        <button type="button"
                                class="variant-chip <?php echo $idx === 0 ? 'active' : ''; ?>"
                                data-color="<?php echo e($color); ?>">
                            <?php echo e($color); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="variant-group">
                <span>Dung lượng:</span>
                <div class="variant-options" id="storageOptions">
                    <?php foreach ($storages as $idx => $st): ?>
                        <button type="button"
                                class="variant-chip <?php echo $idx === 0 ? 'active' : ''; ?>"
                                data-storage="<?php echo e($st); ?>">
                            <?php echo e($st); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="product-detail__price">
            <span>Giá:</span>
            <div>
                <span id="variantPrice" class="price-final">Đang tải...</span>
                <span id="variantBasePrice" class="price-base"></span>
                <?php if ($discount > 0): ?>
                    <span class="product-card__badge">-<?php echo $discount; ?>%</span>
                <?php endif; ?>
            </div>
        </div>
        <p class="product-detail__stock">Tồn kho: <span id="variantStock">-</span></p>

        <form id="addToCartForm" class="product-detail__cart">
            <input type="hidden" name="variant_id" id="variantIdInput">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <label>Số lượng:</label>
            <input type="number" name="qty" id="qtyInput" value="1" min="1">
            <button type="submit" class="btn-primary">Thêm vào giỏ</button>
            <span id="addCartMsg" class="form-msg"></span>
        </form>

        <article class="product-detail__desc">
            <h3>Chi tiết sản phẩm</h3>
            <p><?php echo nl2br(e($product['description'])); ?></p>
        </article>
    </div>
</section>

<script>
const productId = <?php echo (int)$product['id']; ?>;
const discountPercent = <?php echo (int)$product['discount_percent']; ?>;
</script>
<?php
require_once __DIR__ . '/../partials/footer.php';
