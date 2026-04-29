<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/auth.php';

// Bắt buộc đăng nhập
requireLogin('login.php');

$orderId = $_SESSION['last_order_id'] ?? null;
if (!$orderId) {
    redirect('user/shop.php');
}

$conn = db_connect();
$stmt = $conn->prepare("SELECT * FROM don_hang WHERE id_don_hang = ? AND id_nguoi_dung = ?");
$stmt->execute([$orderId, getCurrentUser()['id_nguoi_dung']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    redirect('user/shop.php');
}

$page_title = 'Đặt hàng thành công - TechShop';
include '../includes/header.php';
?>

<div class="bg-light py-5" style="min-height: 80vh; background-color: #f8fafc !important;">
    <div class="container py-4">
        <div class="bg-white rounded-4 shadow-sm mx-auto" style="max-width: 600px; padding: 40px; border: 1px solid var(--border-color);">
            
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background-color: #d1fae5;">
                    <i class="bi bi-check-circle" style="font-size: 2.5rem; color: #10b981;"></i>
                </div>
                <h3 class="fw-bold text-dark mb-2">Đặt hàng thành công!</h3>
                <p class="text-muted mb-1" style="font-size: 1rem;">Cảm ơn bạn đã mua hàng tại TechShop</p>
                <p class="text-muted" style="font-size: 0.95rem;">Mã đơn hàng: <strong class="text-dark">#<?= htmlspecialchars($order['id_don_hang']) ?></strong></p>
            </div>

            <div class="bg-light rounded-3 p-4 mb-4" style="background-color: #f1f5f9 !important;">
                <h6 class="fw-bold text-primary mb-2" style="font-size: 0.95rem;">Thông tin giao hàng</h6>
                <div class="text-dark" style="font-size: 0.95rem;">
                    <?= htmlspecialchars($order['ten_nguoi_nhan']) ?> - <?= htmlspecialchars($order['so_dien_thoai_nhan']) ?><br>
                    <?= htmlspecialchars($order['dia_chi_giao_hang']) ?>
                </div>
            </div>

            <div class="d-flex gap-3">
                <a href="my_orders.php" class="btn btn-outline-primary rounded-3 flex-grow-1 py-2 fw-medium" style="border-color: #2563eb; color: #2563eb;">Xem đơn hàng</a>
                <a href="shop.php" class="btn btn-primary rounded-3 flex-grow-1 py-2 fw-medium" style="background-color: #2563eb; border:none;">Về trang chủ</a>
            </div>
            
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
