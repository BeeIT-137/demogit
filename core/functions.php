<?php
// core/functions.php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function db_fetch_all($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function db_fetch($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

function db_execute($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    return $stmt->execute($params);
}

function e($str)
{
    if ($str === null) {
        return '';
    }
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}


function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function format_price($number)
{
    return number_format((float)$number, 0, ',', '.') . '₫';
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in()
{
    return isset($_SESSION['user']);
}

function is_admin()
{
    return is_logged_in() && $_SESSION['user']['role'] == 1;
}

function is_staff()
{
    return is_logged_in() && $_SESSION['user']['role'] == 2;
}

function is_customer()
{
    return is_logged_in() && $_SESSION['user']['role'] == 0;
}

function require_login()
{
    if (!is_logged_in()) {
        redirect(BASE_URL . '/pages/login.php');
    }
}

function require_admin()
{
    require_login();
    if (!is_admin()) {
        redirect(BASE_URL . '/index.php');
    }
}

function require_staff_or_admin()
{
    require_login();
    if (!is_admin() && !is_staff()) {
        redirect(BASE_URL . '/index.php');
    }
}

/**
 * CART HELPERS (session)
 */
function get_cart()
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

function save_cart($cart)
{
    $_SESSION['cart'] = $cart;
}

function cart_total_qty()
{
    $cart = get_cart();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['qty'];
    }
    return $total;
}

function cart_total_money()
{
    $cart = get_cart();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['qty'];
    }
    return $total;
}
