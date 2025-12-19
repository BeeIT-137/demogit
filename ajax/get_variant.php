<?php
// ajax/get_variant.php
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json; charset=utf-8');

$product_id = (int)($_POST['product_id'] ?? 0);
$color      = trim($_POST['color'] ?? '');
$storage    = trim($_POST['storage'] ?? '');

if (!$product_id || !$color || !$storage) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu.']);
    exit;
}

$variant = db_fetch("
    SELECT v.*, p.discount_percent, p.base_price
    FROM product_variants v
    JOIN products p ON p.id = v.product_id
    WHERE v.product_id = ? AND v.color = ? AND v.storage = ?
", [$product_id, $color, $storage]);

if (!$variant) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy biến thể.']);
    exit;
}

$price = (float)$variant['price'];
$discount = (int)$variant['discount_percent'];
$finalPrice = $price * (100 - $discount) / 100;

echo json_encode([
    'success' => true,
    'data' => [
        'id'          => (int)$variant['id'],
        'price'       => $price,
        'final_price' => $finalPrice,
        'stock'       => (int)$variant['stock'],
        'discount'    => $discount
    ]
]);
