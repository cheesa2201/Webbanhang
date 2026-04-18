<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Khởi tạo một dữ liệu mồi trong Session ĐỂ DEMO một lần duy nhất.
// Sau này khi có chức năng Thêm giỏ hàng từ product_detail, bạn có thể xoá khối này.
if (!isset($_SESSION['cart_initialized_demo'])) {
    $_SESSION['cart'] = [
        [
            'id' => 1,
            'name' => 'Samsung Galaxy Buds3 Pro',
            'brand' => 'Samsung',
            'price' => 4990000,
            'qty' => 1,
            'image' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/a/samsung-galaxy-buds-3-pro_2_.jpg'
        ]
    ];
    $_SESSION['cart_initialized_demo'] = true;
}

// Xử lý Xoá sản phẩm
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $remove_id = $_GET['id'];
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $remove_id) {
                unset($_SESSION['cart'][$key]);
            }
        }
        // Đánh lại số thứ tự mảng
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header("Location: cart.php");
    exit;
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$is_empty = empty($cart_items);

$cart_count = 0;
$subtotal = 0;
foreach($cart_items as $item) {
    $cart_count += $item['qty'];
    $subtotal += $item['price'] * $item['qty'];
}
$shipping = 0;
$total = $subtotal + $shipping;

include '../includes/header.php';
?>

<div class="bg-light py-5" style="min-height: 80vh; background-color: #f8fafc !important;">
    <div class="container">
    
        <?php if ($is_empty): ?>
            <!-- EMPTY CART STATE -->
            <div class="text-center py-5" style="max-width: 600px; margin: 0 auto; margin-top: 2rem;">
                <div class="mb-4">
                    <img src="https://cdn0.fahasa.com/skin/frontend/ma_vanilla/fahasa/images/ico_emptycart.svg" alt="Empty Cart" style="width: 160px; opacity: 0.8;">
                </div>
                <h4 class="fw-bold mb-3 text-dark" style="font-size: 1.2rem;">Giỏ hàng trống</h4>
                <p class="text-muted mb-4" style="font-size: 0.95rem;">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                <a href="shop.php" class="btn btn-primary px-4 py-2 fw-medium d-inline-flex align-items-center gap-2" style="background-color: #2563eb; border:none; border-radius: 8px; font-size: 0.95rem;">
                    <i class="bi bi-bag"></i> Tiếp tục mua sắm
                </a>
            </div>
            
        <?php else: ?>
            <!-- POPULATED CART STATE -->
            <div class="d-flex justify-content-between align-items-end mb-4">
                <h3 class="fw-bold m-0" style="font-size: 1.4rem;">Giỏ hàng (<?= $cart_count ?> sản phẩm)</h3>
            </div>
            
            <div class="row g-4 mb-5">
                <!-- Left: Cart Items -->
                <div class="col-lg-8">
                    <div class="bg-white rounded-4 p-0 shadow-sm mb-4" style="border: 1px solid var(--border-color); overflow: hidden;">
                        <?php foreach($cart_items as $index => $item): ?>
                            <div class="p-4 d-flex gap-4 <?= $index > 0 ? 'border-top' : '' ?> position-relative">
                                
                                <!-- Delete Btn -->
                                <button type="button" class="btn btn-link text-muted position-absolute top-0 end-0 m-3 p-1 h-auto" style="border:none;" onmouseover="this.className='btn btn-link text-danger position-absolute top-0 end-0 m-3 p-1 h-auto'" onmouseout="this.className='btn btn-link text-muted position-absolute top-0 end-0 m-3 p-1 h-auto'" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-delete-id="<?= $item['id'] ?>" data-delete-name="<?= htmlspecialchars($item['name']) ?>">
                                    <i class="bi bi-trash3 fs-5"></i>
                                </button>
                                
                                <div style="width: 120px; height: 120px; flex-shrink: 0;" class="bg-light rounded-4 overflow-hidden border">
                                    <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-100 h-100 object-fit-cover">
                                </div>
                                
                                <div class="d-flex flex-column flex-grow-1">
                                    <div class="pe-4">
                                        <div class="text-primary fw-medium mb-1" style="font-size: 0.85rem;"><?= htmlspecialchars($item['brand']) ?></div>
                                        <h5 class="fw-bold mb-3" style="font-size: 1.1rem; color: #1e293b;"><?= htmlspecialchars($item['name']) ?></h5>
                                    </div>
                                    
                                    <div class="mt-auto d-flex align-items-center justify-content-between w-100">
                                        <div class="qty-input-group bg-white" style="height: 40px; border-radius: 50px;">
                                            <button class="qty-btn" onclick="this.parentNode.querySelector('input').stepDown()" style="width: 35px; height: 100%;">-</button>
                                            <input type="number" class="qty-input bg-transparent" value="<?= $item['qty'] ?>" min="1" max="99" style="width: 40px; height: 100%;">
                                            <button class="qty-btn" onclick="this.parentNode.querySelector('input').stepUp()" style="width: 35px; height: 100%;">+</button>
                                        </div>
                                        
                                        <div class="fw-bold text-primary" style="font-size: 1.15rem; color: #2563eb !important;">
                                            <?= number_format($item['price'], 0, ',', '.') ?> ₫
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <a href="shop.php" class="text-primary text-decoration-none fw-medium d-inline-flex align-items-center" style="color: #2563eb !important;">
                        <i class="bi bi-arrow-left me-2"></i> Tiếp tục mua sắm
                    </a>
                </div>
                
                <!-- Right: Summary -->
                <div class="col-lg-4">
                    <!-- Coupon Card -->
                    <div class="bg-white rounded-4 p-3 shadow-sm mb-4" style="border: 1px solid var(--border-color);">
                        <form class="d-flex gap-2">
                            <div class="position-relative flex-grow-1">
                                <i class="bi bi-tag position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" class="form-control rounded-pill ps-5 bg-light border-0" placeholder="Nhập mã giảm giá..." style="height: 48px; border: 1px solid #e2e8f0 !important;">
                            </div>
                            <button type="button" class="btn btn-dark rounded-pill px-4 fw-medium flex-shrink-0" style="height: 48px; background-color: #1e293b; border:none; color: white;">Áp dụng</button>
                        </form>
                    </div>
                    
                    <!-- Summary Card -->
                    <div class="bg-white rounded-4 p-4 p-lg-5 shadow-sm" style="border: 1px solid var(--border-color);">
                        <h5 class="fw-bold mb-4" style="font-size: 1.1rem; color: #1e293b;">Tóm tắt đơn hàng</h5>
                        
                        <div class="d-flex justify-content-between mb-3 text-muted" style="font-size: 0.95rem;">
                            <span>Tạm tính (<?= $cart_count ?> sản phẩm)</span>
                            <span class="text-dark fw-medium"><?= number_format($subtotal, 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 text-muted" style="font-size: 0.95rem;">
                            <span>Phí vận chuyển</span>
                            <span class="text-success fw-medium">Miễn phí</span>
                        </div>
                        
                        <hr class="border-secondary opacity-10 mb-4">
                        
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark" style="font-size: 1.05rem;">Tổng cộng</span>
                            <span class="fw-bold text-primary" style="font-size: 1.4rem; letter-spacing: -0.5px; color: #2563eb !important;"><?= number_format($total, 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="text-muted mb-4" style="font-size: 0.75rem;">
                            Đã bao gồm VAT
                        </div>
                        
                        <button class="btn btn-primary w-100 rounded-pill fw-medium d-flex justify-content-center align-items-center gap-2 mb-4" style="height: 52px; font-size: 1.05rem; background-color: #2563eb; border:none;">
                            Tiến hành thanh toán <i class="bi bi-arrow-right"></i>
                        </button>
                        
                        <div class="d-flex justify-content-between px-2">
                            <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-lock-fill text-warning"></i> Thanh toán bảo mật
                            </div>
                            <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-shield-check text-danger"></i> Bảo hành chính hãng
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-0 pb-0 justify-content-end">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pt-0 pb-4 px-4 px-md-5">
        <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle mb-3 mt-2" style="width: 80px; height: 80px;">
            <i class="bi bi-trash3 text-danger" style="font-size: 2.5rem;"></i>
        </div>
        <h4 class="fw-bold mb-3">Xoá sản phẩm?</h4>
        <p class="text-muted mb-4" style="font-size: 1rem;">Bạn có chắc muốn xoá <br><strong class="text-dark" id="deleteProductName">sản phẩm</strong><br> khỏi giỏ hàng?</p>
        
        <div class="d-flex gap-3 mt-4 mb-2">
            <button type="button" class="btn btn-light rounded-pill flex-grow-1 fw-medium" data-bs-dismiss="modal" style="height: 48px;">Huỷ bỏ</button>
            <a href="#" id="confirmDeleteBtn" class="btn btn-danger rounded-pill flex-grow-1 fw-medium d-inline-flex align-items-center justify-content-center" style="height: 48px;">Đồng ý xoá</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var deleteModal = document.getElementById('deleteConfirmModal')
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                // Nút vừa được click
                var button = event.relatedTarget;
                var productId = button.getAttribute('data-delete-id');
                var productName = button.getAttribute('data-delete-name');
                
                // Cập nhật DOM của Modal
                var confirmBtn = deleteModal.querySelector('#confirmDeleteBtn');
                var nameSpan = deleteModal.querySelector('#deleteProductName');
                
                confirmBtn.href = 'cart.php?action=remove&id=' + productId;
                nameSpan.textContent = productName;
            });
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
