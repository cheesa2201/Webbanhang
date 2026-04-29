<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin('login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $conn = db_connect();
    $user = getCurrentUser();
    
    // Kiểm tra đơn hàng có phải của user và đang chờ xác nhận không
    $stmt = $conn->prepare("SELECT trang_thai_don_hang FROM don_hang WHERE id_don_hang = ? AND id_nguoi_dung = ?");
    $stmt->execute([$orderId, $user['id_nguoi_dung']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order && $order['trang_thai_don_hang'] === 'cho_xac_nhan') {
        // Cập nhật thành đã hủy
        $update = $conn->prepare("UPDATE don_hang SET trang_thai_don_hang = 'da_huy' WHERE id_don_hang = ?");
        $update->execute([$orderId]);
    }
}

$returnTo = $_POST['return_to'] ?? 'my_orders.php';
redirect('user/' . $returnTo);
