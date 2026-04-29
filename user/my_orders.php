<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/auth.php';

// Bắt buộc đăng nhập
requireLogin('login.php');

$user = getCurrentUser();
$conn = db_connect();

// Fetch orders
$stmt = $conn->prepare("SELECT * FROM don_hang WHERE id_nguoi_dung = ? ORDER BY ngay_dat DESC");
$stmt->execute([$user['id_nguoi_dung']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalOrders = count($orders);

$page_title = 'Đơn hàng của tôi - TechShop';
include '../includes/header.php';

function getStatusInfo($status) {
    $map = [
        'cho_xac_nhan' => ['name' => 'Chờ xác nhận', 'class' => 'status-pending'],
        'da_xac_nhan'  => ['name' => 'Đã xác nhận', 'class' => 'status-confirmed', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
        'dang_giao'    => ['name' => 'Đang giao hàng', 'class' => 'status-shipping', 'color' => '#8b5cf6', 'bg' => '#f5f3ff'],
        'da_giao'      => ['name' => 'Đã giao hàng', 'class' => 'status-delivered', 'color' => '#10b981', 'bg' => '#d1fae5'],
        'da_huy'       => ['name' => 'Đã hủy', 'class' => 'status-cancelled', 'color' => '#ef4444', 'bg' => '#fee2e2'],
    ];
    return $map[$status] ?? $map['cho_xac_nhan'];
}
?>

<style>
.nav-pills-custom .nav-link {
    color: #64748b;
    border: 1px solid #e2e8f0;
    border-radius: 50px;
    padding: 8px 20px;
    font-size: 0.9rem;
    font-weight: 500;
    margin-right: 10px;
    margin-bottom: 10px;
    background-color: white;
    transition: all 0.2s;
}
.nav-pills-custom .nav-link:hover {
    border-color: #cbd5e1;
    background-color: #f8fafc;
}
.nav-pills-custom .nav-link.active {
    background-color: #2563eb;
    color: white;
    border-color: #2563eb;
}
.order-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background-color: white;
    margin-bottom: 20px;
    overflow: hidden;
}
.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #e2e8f0;
}
.order-body {
    padding: 20px;
}
.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background-color: #fcfcfc;
    border-top: 1px solid #e2e8f0;
}
.status-badge {
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
}
.status-pending {
    background-color: #fef3c7;
    color: #d97706;
}
</style>

<div class="bg-light py-5" style="min-height: 80vh; background-color: #f8fafc !important;">
    <div class="container">
        
        <h4 class="fw-bold mb-4">Đơn hàng của tôi</h4>

        <!-- Tabs -->
        <ul class="nav nav-pills nav-pills-custom mb-4" id="orderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" type="button">Tất cả (<?= $totalOrders ?>)</button>
            </li>
            <!-- Các tab khác có thể tính số lượng tĩnh hoặc động tùy ý, tạm ẩn số lượng hoặc để (0) -->
            <li class="nav-item" role="presentation"><button class="nav-link" type="button">Chờ xác nhận</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" type="button">Đã xác nhận</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" type="button">Đang giao hàng</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" type="button">Đã giao hàng</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" type="button">Đã hủy</button></li>
        </ul>

        <!-- Order List -->
        <?php if(empty($orders)): ?>
            <div class="text-center py-5">
                <img src="https://cdn0.fahasa.com/skin/frontend/ma_vanilla/fahasa/images/ico_emptycart.svg" alt="Empty" style="width: 120px; opacity: 0.6; margin-bottom: 20px;">
                <h5 class="text-muted">Bạn chưa có đơn hàng nào</h5>
                <a href="shop.php" class="btn btn-primary mt-3 rounded-pill px-4">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <?php foreach($orders as $order): 
                $statusInfo = getStatusInfo($order['trang_thai_don_hang']);
                
                // Fetch items for this order
                $stmtItem = $conn->prepare("SELECT ct.*, sp.ten_san_pham, sp.hinh_anh_chinh FROM chi_tiet_don_hang ct JOIN san_pham sp ON ct.id_san_pham = sp.id_san_pham WHERE ct.id_don_hang = ?");
                $stmtItem->execute([$order['id_don_hang']]);
                $items = $stmtItem->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="order-card shadow-sm">
                <!-- Header -->
                <div class="order-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 rounded text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">Đơn hàng #<?= htmlspecialchars($order['id_don_hang']) ?></div>
                            <div class="text-muted" style="font-size: 0.8rem;"><?= date('H:i:s d/m/Y', strtotime($order['ngay_dat'])) ?></div>
                        </div>
                    </div>
                    <div>
                        <span class="status-badge <?= $statusInfo['class'] ?> d-flex align-items-center gap-1" style="<?= isset($statusInfo['bg']) ? "background-color: {$statusInfo['bg']}; color: {$statusInfo['color']};" : '' ?>">
                            <?php if($order['trang_thai_don_hang'] == 'cho_xac_nhan'): ?>
                                <span class="spinner-grow spinner-grow-sm" style="width: 0.5rem; height: 0.5rem;" role="status"></span>
                            <?php endif; ?>
                            <?= $statusInfo['name'] ?>
                        </span>
                    </div>
                </div>

                <!-- Body -->
                <div class="order-body">
                    <?php foreach($items as $item): 
                        $imgSrc = filter_var($item['hinh_anh_chinh'], FILTER_VALIDATE_URL) ? $item['hinh_anh_chinh'] : '../uploads/' . $item['hinh_anh_chinh'];
                        if(empty($item['hinh_anh_chinh'])) $imgSrc = 'https://placehold.co/600x600?text=No+Image';
                    ?>
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <div style="width: 70px; height: 70px; flex-shrink: 0;" class="bg-light rounded-3 overflow-hidden border">
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium text-dark" style="font-size: 0.95rem;"><?= htmlspecialchars($item['ten_san_pham']) ?></div>
                            <div class="text-muted mt-1" style="font-size: 0.85rem;">x<?= $item['so_luong'] ?> · <?= number_format($item['don_gia'], 0, ',', '.') ?> ₫</div>
                        </div>
                        <div class="fw-bold text-dark">
                            <?= number_format($item['thanh_tien'], 0, ',', '.') ?> ₫
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Footer -->
                <div class="order-footer">
                    <div class="text-dark" style="font-size: 0.95rem;">
                        Tổng tiền: <strong class="text-primary fs-5 ms-1"><?= number_format($order['tong_tien'], 0, ',', '.') ?> ₫</strong>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if($order['trang_thai_don_hang'] == 'cho_xac_nhan'): ?>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center gap-1" style="height: 36px;" data-bs-toggle="modal" data-bs-target="#cancelConfirmModal" data-orderid="<?= $order['id_don_hang'] ?>" data-returnto="my_orders.php">
                            <i class="bi bi-x"></i> Hủy đơn
                        </button>
                        <?php endif; ?>
                        <a href="order_detail.php?id=<?= $order['id_don_hang'] ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center gap-1" style="height: 36px; text-decoration: none;">
                            <i class="bi bi-eye"></i> Chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-labelledby="cancelConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-0 pb-0 justify-content-end">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pt-0 pb-4 px-4 px-md-5">
        <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle mb-3 mt-2" style="width: 80px; height: 80px;">
            <i class="bi bi-x-circle text-danger" style="font-size: 2.5rem;"></i>
        </div>
        <h4 class="fw-bold mb-3">Hủy đơn hàng?</h4>
        <p class="text-muted mb-4" style="font-size: 1rem;">Bạn có chắc muốn hủy đơn hàng <br><strong class="text-dark" id="cancelOrderName">#...</strong><br> không?</p>
        
        <form action="cancel_order.php" method="POST" class="m-0 p-0">
            <input type="hidden" name="order_id" id="cancelOrderIdInput" value="">
            <input type="hidden" name="return_to" id="cancelReturnToInput" value="">
            <div class="d-flex gap-3 mt-4 mb-2">
                <button type="button" class="btn btn-light rounded-pill flex-grow-1 fw-medium" data-bs-dismiss="modal" style="height: 48px;">Không hủy</button>
                <button type="submit" class="btn btn-danger rounded-pill flex-grow-1 fw-medium d-inline-flex align-items-center justify-content-center" style="height: 48px;">Đồng ý hủy</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cancelConfirmModal = document.getElementById('cancelConfirmModal');
    if (cancelConfirmModal) {
        cancelConfirmModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-orderid');
            const returnTo = button.getAttribute('data-returnto');

            document.getElementById('cancelOrderName').textContent = '#' + orderId;
            document.getElementById('cancelOrderIdInput').value = orderId;
            document.getElementById('cancelReturnToInput').value = returnTo;
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
