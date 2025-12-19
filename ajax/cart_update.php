<?php
// ajax/cart_update.php
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json; charset=utf-8');

$variant_id = (int)($_POST['variant_id'] ?? 0);
$action     = $_POST['action'] ?? '';

$cart = get_cart();
$key = (string)$variant_id;

if (!isset($cart[$key])) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ.']);
    exit;
}

// Lấy lại tồn kho từ DB để đảm bảo
$variant = db_fetch("SELECT stock FROM product_variants WHERE id = ?", [$variant_id]);
$stock = $variant ? (int)$variant['stock'] : $cart[$key]['max_stock'];

if ($action === 'inc') {
    if ($cart[$key]['qty'] >= $stock) {
        echo json_encode(['success' => false, 'message' => 'Đã đạt số lượng tối đa.']);
        exit;
    }
    $cart[$key]['qty']++;
} elseif ($action === 'dec') {
    $cart[$key]['qty']--;
    if ($cart[$key]['qty'] <= 0) {
        unset($cart[$key]);
        save_cart($cart);
        echo json_encode([
            'success' => true,
            'removed' => true,
            'cart_total' => cart_total_money(),
            'cart_count' => cart_total_qty()
        ]);
        exit;
    }
} elseif ($action === 'remove') {
    unset($cart[$key]);
    save_cart($cart);
    echo json_encode([
        'success' => true,
        'removed' => true,
        'cart_total' => cart_total_money(),
        'cart_count' => cart_total_qty()
    ]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);
    exit;
}

save_cart($cart);
$item = $cart[$key];

echo json_encode([
    'success'    => true,
    'removed'    => false,
    'qty'        => $item['qty'],
    'line_total' => $item['price'] * $item['qty'],
    'cart_total' => cart_total_money(),
    'cart_count' => cart_total_qty()
]);
