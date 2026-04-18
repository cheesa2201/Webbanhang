<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? 'Sản phẩm';
$price = $_POST['price'] ?? 0;
$brand = $_POST['brand'] ?? 'Unknown';
$image = $_POST['image'] ?? '';

if (!$action || !$id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

// 1. Thêm vào giỏ hàng
if ($action === 'add_cart') {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
    if ($qty < 1) $qty = 1;
    
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id) {
            $item['qty'] += $qty; // Tăng số lượng theo biến truyền vào
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'brand' => $brand,
            'image' => $image,
            'qty' => $qty
        ];
    }
    
    // Tính lại tổng số lượng
    $total_items = 0;
    foreach ($_SESSION['cart'] as $v) { $total_items += $v['qty']; }
    
    echo json_encode(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng', 'cart_count' => $total_items]);
    exit;
}

// 2. Click Yêu thích
if ($action === 'toggle_favorite') {
    if (!isset($_SESSION['favorites'])) {
        $_SESSION['favorites'] = [];
    }
    
    foreach ($_SESSION['favorites'] as $key => $item) {
        if ($item['id'] == $id) {
            // Đã tồn tại -> BỎ YÊN THÍCH
            unset($_SESSION['favorites'][$key]);
            // Đánh lại key
            $_SESSION['favorites'] = array_values($_SESSION['favorites']);
            echo json_encode(['status' => 'success', 'favorited' => false, 'message' => 'Đã bỏ yêu thích']);
            exit;
        }
    }
    
    // Chưa tồn tại -> THÊM YÊU THÍCH
    $_SESSION['favorites'][] = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'brand' => $brand,
        'image' => $image
    ];
    
    echo json_encode(['status' => 'success', 'favorited' => true, 'message' => 'Đã thêm vào danh sách yêu thích']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
