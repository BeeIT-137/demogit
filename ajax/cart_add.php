<?php
// ajax/cart_add.php
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json; charset=utf-8');

$variant_id = (int)($_POST['variant_id'] ?? 0);
$product_id = (int)($_POST['product_id'] ?? 0);
$qty        = (int)($_POST['qty'] ?? 1);

if ($variant_id <= 0 || $product_id <= 0 || $qty <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$variant = db_fetch("
    SELECT v.*, p.name, p.thumbnail, p.discount_percent
    FROM product_variants v
    JOIN products p ON p.id = v.product_id
    WHERE v.id = ? AND v.product_id = ?
", [$variant_id, $product_id]);

if (!$variant) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy biến thể sản phẩm.']);
    exit;
}

if ($qty > (int)$variant['stock']) {
    echo json_encode(['success' => false, 'message' => 'Số lượng vượt tồn kho.']);
    exit;
}

// Tính giá sau giảm
$price = (float)$variant['price'];
$discount = (int)$variant['discount_percent'];
$finalPrice = $price * (100 - $discount) / 100;

$cart = get_cart();
$key = (string)$variant_id;

if (!isset($cart[$key])) {
    $cart[$key] = [
        'variant_id' => $variant_id,
        'product_id' => $product_id,
        'name'       => $variant['name'],
        'thumbnail'  => $variant['thumbnail'],
        'color'      => $variant['color'],
        'storage'    => $variant['storage'],
        'price'      => $finalPrice,
        'qty'        => 0,
        'max_stock'  => (int)$variant['stock']
    ];
}

$newQty = $cart[$key]['qty'] + $qty;
if ($newQty > $cart[$key]['max_stock']) {
    echo json_encode(['success' => false, 'message' => 'Không đủ tồn kho.']);
    exit;
}

$cart[$key]['qty'] = $newQty;
save_cart($cart);

echo json_encode([
    'success' => true,
    'message' => 'Đã thêm vào giỏ hàng.',
    'cart_count' => cart_total_qty()
]);
