<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/models/Order.php';
require_login();

$orders = (new Order(db_connect()))->getOrdersByUser((int)$_SESSION['id_nguoi_dung']);
$page_title = 'Đơn hàng của tôi — TechShop';
require __DIR__ . '/includes/header.php';

function order_status_label(string $status): array
{
    return match ($status) {
        'cho_xac_nhan' => ['Chờ xác nhận', 'warning'],
        'da_xac_nhan' => ['Đã xác nhận', 'info'],
        'dang_giao' => ['Đang giao', 'primary'],
        'da_giao' => ['Đã giao', 'success'],
        'da_huy' => ['Đã hủy', 'danger'],
        default => [$status, 'secondary'],
    };
}
?>
<main class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:13px">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/account.php">Tài khoản</a></li>
            <li class="breadcrumb-item active">Đơn hàng</li>
        </ol>
    </nav>

    <div class="section-heading mb-4">
        <i class="bi bi-box-seam me-2"></i>Đơn hàng của tôi
        <span class="text-muted fw-normal ms-2" style="font-size:14px">(<?= count($orders) ?> đơn)</span>
    </div>

    <?php if (!$orders): ?>
        <div class="empty-state bg-white rounded-4 shadow-sm">
            <i class="bi bi-box"></i>
            <h5>Bạn chưa có đơn hàng nào</h5>
            <p class="text-muted">Hãy mua sắm để tạo đơn hàng đầu tiên.</p>
            <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary mt-2">
                <i class="bi bi-bag me-2"></i>Mua sắm ngay
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-4 shadow-sm p-4 table-responsive">
            <table class="table align-middle order-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Tổng tiền</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o):
                        [$label, $color] = order_status_label($o['trang_thai_don_hang']);
                    ?>
                    <tr>
                        <td class="fw-800">#<?= (int)$o['id_don_hang'] ?></td>
                        <td><?= h($o['ngay_dat']) ?></td>
                        <td><?= h($o['ten_phuong_thuc'] ?? '—') ?></td>
                        <td><span class="badge rounded-pill bg-<?= $color ?>"><?= h($label) ?></span></td>
                        <td class="text-end fw-800 text-primary"><?= format_price($o['tong_tien']) ?></td>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>/order_complete.php?id=<?= (int)$o['id_don_hang'] ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Xem
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
