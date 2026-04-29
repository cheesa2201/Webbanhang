<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin('login.php');

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$orderId) {
    redirect('user/my_orders.php');
}

$conn = db_connect();
$user = getCurrentUser();

// Fetch order with payment method
$stmt = $conn->prepare("
    SELECT d.*, p.ten_phuong_thuc 
    FROM don_hang d 
    LEFT JOIN phuong_thuc_thanh_toan p ON d.id_phuong_thuc = p.id_phuong_thuc 
    WHERE d.id_don_hang = ? AND d.id_nguoi_dung = ?
");
$stmt->execute([$orderId, $user['id_nguoi_dung']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    redirect('user/my_orders.php');
}

// Fetch items
$stmtItem = $conn->prepare("
    SELECT ct.*, sp.ten_san_pham, sp.hinh_anh_chinh 
    FROM chi_tiet_don_hang ct 
    JOIN san_pham sp ON ct.id_san_pham = sp.id_san_pham 
    WHERE ct.id_don_hang = ?
");
$stmtItem->execute([$orderId]);
$items = $stmtItem->fetchAll(PDO::FETCH_ASSOC);

function getStatusInfo($status) {
    $map = [
        'cho_xac_nhan' => ['name' => 'Chờ xác nhận', 'class' => 'status-pending', 'icon' => 'bi-hourglass-split'],
        'da_xac_nhan'  => ['name' => 'Đã xác nhận', 'class' => 'status-confirmed', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'icon' => 'bi-check-circle'],
        'dang_giao'    => ['name' => 'Đang giao hàng', 'class' => 'status-shipping', 'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'icon' => 'bi-truck'],
        'da_giao'      => ['name' => 'Đã giao hàng', 'class' => 'status-delivered', 'color' => '#10b981', 'bg' => '#d1fae5', 'icon' => 'bi-box-seam'],
        'da_huy'       => ['name' => 'Đã hủy', 'class' => 'status-cancelled', 'color' => '#ef4444', 'bg' => '#fee2e2', 'icon' => 'bi-x-circle'],
    ];
    return $map[$status] ?? $map['cho_xac_nhan'];
}
$statusInfo = getStatusInfo($order['trang_thai_don_hang']);

$page_title = 'Chi tiết đơn hàng #' . $orderId . ' - TechShop';
include '../includes/header.php';
?>

<style>
.invoice-card {
    border: none;
    border-radius: 12px;
    background-color: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
}
.invoice-header {
    background-color: #f8fafc;
    border-bottom: 2px dashed #e2e8f0;
    padding: 30px;
    border-radius: 12px 12px 0 0;
}
.invoice-body {
    padding: 30px;
}
.invoice-footer {
    background-color: #f8fafc;
    border-top: 2px dashed #e2e8f0;
    padding: 30px;
    border-radius: 0 0 12px 12px;
}
.info-label {
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.info-value {
    color: #0f172a;
    font-size: 1rem;
    font-weight: 500;
}
.table-invoice th {
    background-color: #f1f5f9;
    color: #475569;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    border: none;
    padding: 12px 15px;
}
.table-invoice td {
    vertical-align: middle;
    padding: 15px;
    border-bottom: 1px solid #f1f5f9;
}
</style>

<div class="bg-light py-5" style="min-height: 80vh; background-color: #f1f5f9 !important;">
    <div class="container">
        
        <div class="mb-4">
            <a href="my_orders.php" class="text-decoration-none text-muted fw-medium d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách đơn hàng
            </a>
        </div>

        <div class="invoice-card mx-auto" style="max-width: 900px;">
            <!-- Header -->
            <div class="invoice-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Hoá Đơn Đặt Hàng</h3>
                    <div class="text-muted">Mã đơn hàng: <strong class="text-primary">#<?= htmlspecialchars($order['id_don_hang']) ?></strong></div>
                </div>
                <div class="text-md-end">
                    <div class="mb-2">
                        <span class="badge rounded-pill px-3 py-2 fw-medium" style="font-size: 0.9rem; <?= isset($statusInfo['bg']) ? "background-color: {$statusInfo['bg']}; color: {$statusInfo['color']};" : 'background-color: #fef3c7; color: #d97706;' ?>">
                            <i class="bi <?= $statusInfo['icon'] ?? 'bi-hourglass-split' ?> me-1"></i> <?= $statusInfo['name'] ?>
                        </span>
                    </div>
                    <div class="text-muted" style="font-size: 0.9rem;">
                        Ngày đặt: <strong><?= date('H:i d/m/Y', strtotime($order['ngay_dat'])) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Body (Customer & Shipping Info) -->
            <div class="invoice-body">
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-4 h-100">
                            <h6 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                                <i class="bi bi-person-lines-fill"></i> Thông tin khách hàng
                            </h6>
                            <div class="mb-3">
                                <div class="info-label">Người nhận</div>
                                <div class="info-value"><?= htmlspecialchars($order['ten_nguoi_nhan']) ?></div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Số điện thoại</div>
                                <div class="info-value"><?= htmlspecialchars($order['so_dien_thoai_nhan']) ?></div>
                            </div>
                            <div>
                                <div class="info-label">Phương thức thanh toán</div>
                                <div class="info-value">
                                    <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary-subtle">
                                        <?= htmlspecialchars($order['ten_phuong_thuc'] ?? 'COD') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-4 h-100">
                            <h6 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill"></i> Giao hàng đến
                            </h6>
                            <div class="mb-3">
                                <div class="info-label">Địa chỉ giao hàng</div>
                                <div class="info-value" style="line-height: 1.6;">
                                    <?= nl2br(htmlspecialchars($order['dia_chi_giao_hang'])) ?>
                                </div>
                            </div>
                            <div>
                                <div class="info-label">Ghi chú của khách hàng</div>
                                <div class="info-value fst-italic text-muted">
                                    <?= !empty($order['ghi_chu']) ? nl2br(htmlspecialchars($order['ghi_chu'])) : 'Không có ghi chú' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <h6 class="fw-bold mb-3 text-dark">Sản phẩm đã đặt</h6>
                <div class="table-responsive border rounded-3 overflow-hidden">
                    <table class="table table-invoice mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Sản phẩm</th>
                                <th class="text-center" style="width: 15%;">Đơn giá</th>
                                <th class="text-center" style="width: 15%;">Số lượng</th>
                                <th class="text-end" style="width: 20%;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $item): 
                                $imgSrc = filter_var($item['hinh_anh_chinh'], FILTER_VALIDATE_URL) ? $item['hinh_anh_chinh'] : '../uploads/' . $item['hinh_anh_chinh'];
                                if(empty($item['hinh_anh_chinh'])) $imgSrc = 'https://placehold.co/600x600?text=No+Image';
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 50px; height: 50px;" class="bg-light rounded border overflow-hidden flex-shrink-0">
                                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div class="fw-medium text-dark"><?= htmlspecialchars($item['ten_san_pham']) ?></div>
                                    </div>
                                </td>
                                <td class="text-center text-muted"><?= number_format($item['don_gia'], 0, ',', '.') ?> ₫</td>
                                <td class="text-center fw-medium"><?= $item['so_luong'] ?></td>
                                <td class="text-end fw-bold text-dark"><?= number_format($item['thanh_tien'], 0, ',', '.') ?> ₫</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer (Totals) -->
            <div class="invoice-footer">
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-medium text-dark"><?= number_format($order['tong_tien'], 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-secondary-subtle">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="fw-medium text-success">Miễn phí</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-5">Tổng cộng:</span>
                            <span class="fw-bold text-primary fs-3"><?= number_format($order['tong_tien'], 0, ',', '.') ?> ₫</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <?php if($order['trang_thai_don_hang'] == 'cho_xac_nhan'): ?>
        <div class="text-center mt-4">
            <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#cancelConfirmModal" data-orderid="<?= $order['id_don_hang'] ?>" data-returnto="order_detail.php?id=<?= $order['id_don_hang'] ?>">
                <i class="bi bi-x-circle me-1"></i> Hủy đơn hàng này
            </button>
        </div>
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
