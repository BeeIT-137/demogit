<?php
require_once __DIR__ . '/../partials/header.php';

// Lấy danh sách danh mục để làm filter nhanh
$allCategories = db_fetch_all("SELECT * FROM categories ORDER BY name ASC");

// Lọc / Tìm kiếm
$catSlug = $_GET['cat'] ?? null;
$keyword = trim($_GET['q'] ?? '');

$params = [];
$sql = "
    SELECT p.*, c.name AS cat_name,
           (SELECT MIN(price) FROM product_variants v WHERE v.product_id = p.id) AS min_price
    FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE 1=1
";

if ($catSlug) {
    $sql .= " AND c.slug = ? ";
    $params[] = $catSlug;
}
if ($keyword !== '') {
    $sql .= " AND p.name LIKE ? ";
    $params[] = '%' . $keyword . '%';
}
$sql .= " ORDER BY p.id DESC";

$products = db_fetch_all($sql, $params);
?>

<!-- HERO / BANNER TRANG CHỦ -->
<section class="section home-hero">
    <div class="home-hero__content">
        <p class="home-hero__eyebrow">TopMobile · Flagship Zone</p>
        <h1 class="home-hero__title">
            Nâng cấp trải nghiệm<br>
            với <span>smartphone chính hãng</span>
        </h1>
        <p class="home-hero__subtitle">
            Chỉ tập trung vào điện thoại di động – chọn là có ngay,
            ưu tiên những mẫu mới nhất từ iPhone, Samsung, Xiaomi...
        </p>
        <div class="home-hero__badges">
            <span>✔ Hàng mới, chính hãng</span>
            <span>✔ Nhiều phiên bản màu & dung lượng</span>
            <span>✔ Giá ưu đãi từng biến thể</span>
        </div>
    </div>
    <div class="home-hero__visual">
        <div class="home-hero__card">
            <p>Flash deal hôm nay</p>
            <strong>Giảm đến 15%</strong>
            <span>Cho một số mẫu flagship</span>
        </div>
        <div class="home-hero__glow"></div>
    </div>
</section>

<!-- HÀNG FILTER / TRẠNG THÁI LỌC -->
<section class="section home-filters">
    <div class="home-filters__top">
        <h2 class="home-filters__title">Danh sách điện thoại</h2>

        <div class="home-filters__state">
            <?php if ($keyword !== '' && $catSlug): ?>
                <span>Đang lọc theo:</span>
                <span class="pill pill--active">
                    Từ khóa: “<?php echo e($keyword); ?>”
                </span>
                <?php
                $currentCat = null;
                foreach ($allCategories as $c) {
                    if ($c['slug'] === $catSlug) {
                        $currentCat = $c['name'];
                        break;
                    }
                }
                ?>
                <?php if ($currentCat): ?>
                    <span class="pill pill--active">
                        Danh mục: <?php echo e($currentCat); ?>
                    </span>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/index.php" class="pill pill--clear">Xóa lọc</a>

            <?php elseif ($keyword !== ''): ?>
                <span>Đang tìm kiếm: </span>
                <span class="pill pill--active">“<?php echo e($keyword); ?>”</span>
                <a href="<?php echo BASE_URL; ?>/index.php" class="pill pill--clear">Xóa lọc</a>

            <?php elseif ($catSlug): ?>
                <?php
                $currentCat = null;
                foreach ($allCategories as $c) {
                    if ($c['slug'] === $catSlug) {
                        $currentCat = $c['name'];
                        break;
                    }
                }
                ?>
                <span>Đang xem:</span>
                <span class="pill pill--active">
                    <?php echo e($currentCat ?: 'Tất cả'); ?>
                </span>
                <a href="<?php echo BASE_URL; ?>/index.php" class="pill pill--clear">Xem tất cả</a>

            <?php else: ?>
                <span class="home-filters__hint">
                    Gợi ý: dùng ô tìm kiếm phía trên hoặc chọn nhanh hãng ở dưới.
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- PILL DANH MỤC -->
    <div class="home-filters__categories">
        <a href="<?php echo BASE_URL; ?>/index.php"
           class="pill <?php echo $catSlug ? '' : 'pill--active'; ?>">
            Tất cả
        </a>
        <?php foreach ($allCategories as $cat): ?>
            <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo e($cat['slug']); ?>"
               class="pill <?php echo ($catSlug === $cat['slug']) ? 'pill--active' : ''; ?>">
                <?php echo e($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- LƯỚI SẢN PHẨM -->
<section class="section">
    <div class="section__header">
        <h2>Điện thoại nổi bật</h2>
        <?php if (!empty($products)): ?>
            <p class="section__caption">
                Tìm thấy <strong><?php echo count($products); ?></strong> mẫu điện thoại
                <?php if ($keyword !== ''): ?> phù hợp với từ khóa của bạn<?php endif; ?>.
            </p>
        <?php endif; ?>
    </div>

    <?php if (!empty($products)): ?>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <?php
                $discount   = (int)$p['discount_percent'];
                $minPrice   = (float)$p['min_price'];
                $hasVariant = $minPrice > 0;
                if ($hasVariant) {
                    $finalPrice = $minPrice * (100 - $discount) / 100;
                } else {
                    $finalPrice = (float)$p['base_price'] * (100 - $discount) / 100;
                }
                ?>
                <a class="product-card"
                   href="<?php echo BASE_URL; ?>/pages/product_detail.php?slug=<?php echo e($p['slug']); ?>">
                    <div class="product-card__thumb">
                        <img src="<?php echo BASE_URL . '/' . e($p['thumbnail']); ?>"
                             alt="<?php echo e($p['name']); ?>">
                        <?php if ($discount > 0): ?>
                            <span class="product-card__badge">
                                -<?php echo $discount; ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="product-card__body">
                        <h2><?php echo e($p['name']); ?></h2>
                        <p class="product-card__cat"><?php echo e($p['cat_name']); ?></p>

                        <div class="product-card__price">
                            <span class="price-final">
                                <?php echo format_price($finalPrice); ?>
                            </span>
                            <?php if ($hasVariant): ?>
                                <span class="price-base">
                                    từ <?php echo format_price($minPrice); ?>
                                </span>
                            <?php else: ?>
                                <span class="price-base">
                                    <?php echo format_price($p['base_price']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <p class="product-card__note">
                            Nhiều phiên bản màu & dung lượng
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3>Không tìm thấy sản phẩm phù hợp</h3>
            <p>Thử đổi từ khóa, bỏ bớt bộ lọc hoặc quay lại trang tất cả sản phẩm.</p>
            <a href="<?php echo BASE_URL; ?>/index.php" class="btn-primary">Xem tất cả điện thoại</a>
        </div>
    <?php endif; ?>
</section>

<?php
require_once __DIR__ . '/../partials/footer.php';
