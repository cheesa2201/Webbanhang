<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/auth.php';

// Bắt buộc đăng nhập
requireLogin('login.php');

$user = getCurrentUser();

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart_items)) {
    redirect('user/cart.php');
}

$cart_count = 0;
$subtotal = 0;
foreach($cart_items as $item) {
    $cart_count += $item['qty'];
    $subtotal += $item['price'] * $item['qty'];
}
$shipping = 0;
$total = $subtotal + $shipping;

// Xử lý khi Submit Đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {
    $shipName = $_POST['ship_name'] ?? '';
    $shipPhone = $_POST['ship_phone'] ?? '';
    $shipAddress = $_POST['ship_address'] ?? '';
    $shipNote = $_POST['ship_note'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? 'cod';
    
    // Map payment method to id_phuong_thuc
    $paymentId = 1; // COD
    if($paymentMethod == 'bank') $paymentId = 2;
    if($paymentMethod == 'momo') $paymentId = 3;
    if($paymentMethod == 'card') $paymentId = 2;

    $conn = db_connect();
    try {
        $conn->beginTransaction();
        
        $stmt = $conn->prepare("INSERT INTO don_hang (id_nguoi_dung, id_phuong_thuc, tong_tien, ten_nguoi_nhan, so_dien_thoai_nhan, dia_chi_giao_hang, ghi_chu, trang_thai_don_hang) VALUES (?, ?, ?, ?, ?, ?, ?, 'cho_xac_nhan')");
        $stmt->execute([
            $user['id_nguoi_dung'],
            $paymentId,
            $total,
            $shipName,
            $shipPhone,
            $shipAddress,
            $shipNote
        ]);
        $orderId = $conn->lastInsertId();
        
        $stmtDetail = $conn->prepare("INSERT INTO chi_tiet_don_hang (id_don_hang, id_san_pham, so_luong, don_gia, thanh_tien) VALUES (?, ?, ?, ?, ?)");
        foreach($cart_items as $item) {
            $thanhTien = $item['price'] * $item['qty'];
            $stmtDetail->execute([
                $orderId,
                $item['id'],
                $item['qty'],
                $item['price'],
                $thanhTien
            ]);
        }
        
        $conn->commit();
        
        unset($_SESSION['cart']);
        unset($_SESSION['cart_initialized_demo']);
        $_SESSION['last_order_id'] = $orderId;
        
        redirect('user/order_complete.php');
    } catch(Exception $e) {
        $conn->rollBack();
        die("Lỗi khi đặt hàng: " . $e->getMessage());
    }
}

$page_title = 'Thanh toán - TechShop';
include '../includes/header.php';
?>

<style>
.checkout-progress {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    max-width: 600px;
    margin: 0 auto;
    margin-bottom: 40px;
}
.checkout-progress::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #e2e8f0;
    z-index: 0;
    transform: translateY(-50%);
}
.progress-line {
    position: absolute;
    top: 50%;
    left: 0;
    height: 2px;
    background-color: #10b981;
    z-index: 1;
    transform: translateY(-50%);
    transition: width 0.4s ease;
    width: 0%;
}
.step-item {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 10px;
    background-color: #f8fafc;
    padding: 0 10px;
}
.step-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background-color: #e2e8f0;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 13px;
    transition: all 0.3s ease;
}
.step-label {
    font-size: 14px;
    color: #64748b;
    font-weight: 500;
}
.step-item.active .step-circle {
    background-color: #2563eb;
    color: white;
}
.step-item.active .step-label {
    color: #2563eb;
}
.step-item.completed .step-circle {
    background-color: #d1fae5;
    color: #10b981;
}
.step-item.completed .step-label {
    color: #10b981;
}

.step-content {
    display: none;
}
.step-content.active {
    display: block;
    animation: fadeIn 0.4s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.payment-option {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 15px;
}
.payment-option:hover {
    border-color: #cbd5e1;
}
.payment-option.selected {
    border-color: #2563eb;
    background-color: #eff6ff;
}
.form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.1);
}
</style>

<div class="bg-light py-5" style="min-height: 80vh; background-color: #f8fafc !important;">
    <div class="container">
        
        <!-- Progress Bar -->
        <div class="checkout-progress">
            <div class="progress-line" id="progressLine"></div>
            <div class="step-item active" id="step1">
                <div class="step-circle">1</div>
                <div class="step-label">Thông tin</div>
            </div>
            <div class="step-item" id="step2">
                <div class="step-circle">2</div>
                <div class="step-label">Thanh toán</div>
            </div>
            <div class="step-item" id="step3">
                <div class="step-circle">3</div>
                <div class="step-label">Xác nhận</div>
            </div>
        </div>

        <form id="checkoutForm" action="checkout.php" method="POST">
            <input type="hidden" name="action" value="place_order">
            <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="cod">

            <div class="row g-4 mb-5">
                <!-- Left Column: Steps -->
                <div class="col-lg-8">
                    
                    <!-- STEP 1: THÔNG TIN GIAO HÀNG -->
                    <div class="step-content active" id="content1">
                        <div class="bg-white rounded-4 p-4 p-lg-5 shadow-sm" style="border: 1px solid var(--border-color);">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt text-primary"></i> Thông tin giao hàng
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium text-dark" style="font-size: 0.95rem;">Họ và tên người nhận <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" id="shipName" name="ship_name" class="form-control" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required placeholder="Nhập họ và tên">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium text-dark" style="font-size: 0.95rem;">Số điện thoại <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" id="shipPhone" name="ship_phone" class="form-control" value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>" required placeholder="Nhập số điện thoại">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium text-dark" style="font-size: 0.95rem;">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent text-muted"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" id="shipAddress" name="ship_address" class="form-control" required placeholder="Ví dụ: 123 Nguyễn Huệ, Quận 1, TP.HCM">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium text-dark" style="font-size: 0.95rem;">Ghi chú (không bắt buộc)</label>
                                <textarea id="shipNote" name="ship_note" class="form-control" rows="3" placeholder="Ghi chú cho đơn hàng..."></textarea>
                            </div>

                            <button type="button" class="btn btn-primary w-100 rounded-3 fw-medium py-3" style="background-color: #2563eb; border:none;" onclick="goToStep(1)">
                                Tiếp tục <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: PHƯƠNG THỨC THANH TOÁN -->
                    <div class="step-content" id="content2">
                        <div class="bg-white rounded-4 p-4 p-lg-5 shadow-sm" style="border: 1px solid var(--border-color);">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-credit-card text-primary"></i> Phương thức thanh toán
                            </h5>
                            
                            <div class="payment-option selected" onclick="selectPayment('cod', this)">
                                <input class="form-check-input mt-0" type="radio" name="payment_radio" checked>
                                <div>
                                    <div class="fw-bold text-dark">Thanh toán khi nhận hàng (COD)</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                </div>
                            </div>
                            
                            <div class="payment-option" onclick="selectPayment('bank', this)">
                                <input class="form-check-input mt-0" type="radio" name="payment_radio">
                                <div>
                                    <div class="fw-bold text-dark">Chuyển khoản ngân hàng</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">Chuyển khoản qua tài khoản ngân hàng</div>
                                </div>
                            </div>

                            <div class="payment-option" onclick="selectPayment('momo', this)">
                                <input class="form-check-input mt-0" type="radio" name="payment_radio">
                                <div>
                                    <div class="fw-bold text-dark">Ví điện tử MoMo</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">Thanh toán qua ví MoMo</div>
                                </div>
                            </div>

                            <div class="payment-option" onclick="selectPayment('card', this)">
                                <input class="form-check-input mt-0" type="radio" name="payment_radio">
                                <div>
                                    <div class="fw-bold text-dark">Thẻ tín dụng / Ghi nợ</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">Thanh toán qua thẻ Visa, Mastercard, JCB</div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <button type="button" class="btn btn-outline-secondary rounded-3 fw-medium py-3 px-4" onclick="goToStep(0)">
                                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                                </button>
                                <button type="button" class="btn btn-primary rounded-3 fw-medium py-3 flex-grow-1" style="background-color: #2563eb; border:none;" onclick="goToStep(2)">
                                    Tiếp tục <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: XÁC NHẬN ĐƠN HÀNG -->
                    <div class="step-content" id="content3">
                        <div class="bg-white rounded-4 p-4 p-lg-5 shadow-sm" style="border: 1px solid var(--border-color);">
                            <h5 class="fw-bold mb-4">Xác nhận đơn hàng</h5>
                            
                            <div class="bg-light rounded-3 p-3 mb-4">
                                <div class="d-flex align-items-center gap-2 mb-2 fw-bold text-primary">
                                    <i class="bi bi-truck"></i> Thông tin giao hàng
                                </div>
                                <div class="text-dark fs-sm mb-1"><span class="text-muted me-2">Người nhận:</span> <span id="confirmName" class="fw-medium"></span></div>
                                <div class="text-dark fs-sm mb-1"><span class="text-muted me-2">Điện thoại:</span> <span id="confirmPhone" class="fw-medium"></span></div>
                                <div class="text-dark fs-sm mb-3"><span class="text-muted me-2">Địa chỉ:</span> <span id="confirmAddress" class="fw-medium"></span></div>

                                <div class="d-flex align-items-center gap-2 mb-2 fw-bold text-primary">
                                    <i class="bi bi-credit-card"></i> Phương thức thanh toán
                                </div>
                                <div class="text-dark fs-sm mb-1" id="confirmPayment">Thanh toán khi nhận hàng (COD)</div>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Sản phẩm (<?= $cart_count ?>)</h6>
                                <?php foreach($cart_items as $item): ?>
                                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                    <div style="width: 60px; height: 60px; flex-shrink: 0;" class="bg-light rounded-3 overflow-hidden border">
                                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium text-dark fs-sm"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;">x<?= $item['qty'] ?></div>
                                    </div>
                                    <div class="fw-bold text-dark fs-sm">
                                        <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?> ₫
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex gap-3">
                                <button type="button" class="btn btn-outline-secondary rounded-3 fw-medium py-3 px-4" onclick="goToStep(1)">
                                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                                </button>
                                <button type="submit" class="btn btn-success rounded-3 fw-medium py-3 flex-grow-1 d-flex justify-content-center align-items-center gap-2">
                                    <i class="bi bi-check-circle"></i> Đặt hàng ngay
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- Right Column: Order Summary -->
                <div class="col-lg-4">
                    <div class="bg-white rounded-4 p-4 shadow-sm" style="border: 1px solid var(--border-color); position: sticky; top: 20px;">
                        <h5 class="fw-bold mb-4" style="font-size: 1.1rem; color: #1e293b;">Đơn hàng</h5>
                        
                        <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden;" class="mb-4 pe-2">
                            <?php foreach($cart_items as $item): ?>
                            <div class="d-flex gap-3 mb-3">
                                <div style="width: 50px; height: 50px; flex-shrink: 0;" class="bg-light rounded-3 overflow-hidden border position-relative">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="" class="w-100 h-100 object-fit-cover">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 0.6rem;">
                                        <?= $item['qty'] ?>
                                    </span>
                                </div>
                                <div class="flex-grow-1" style="font-size: 0.85rem;">
                                    <div class="fw-medium text-dark text-truncate" style="max-width: 150px;"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="fw-bold mt-1"><?= number_format($item['price'], 0, ',', '.') ?> ₫</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="border-secondary opacity-10 mb-4">
                        
                        <div class="d-flex justify-content-between mb-3 text-muted" style="font-size: 0.95rem;">
                            <span>Tạm tính</span>
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
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let currentStepIdx = 0;

function selectPayment(method, element) {
    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    document.querySelectorAll('.payment-option input').forEach(el => el.checked = false);
    
    element.classList.add('selected');
    element.querySelector('input').checked = true;
    
    document.getElementById('selectedPaymentMethod').value = method;
}

function getPaymentMethodName(method) {
    const methods = {
        'cod': 'Thanh toán khi nhận hàng (COD)',
        'bank': 'Chuyển khoản ngân hàng',
        'momo': 'Ví điện tử MoMo',
        'card': 'Thẻ tín dụng / Ghi nợ'
    };
    return methods[method] || methods['cod'];
}

function validateStep1() {
    const name = document.getElementById('shipName').value.trim();
    const phone = document.getElementById('shipPhone').value.trim();
    const address = document.getElementById('shipAddress').value.trim();
    
    if(!name || !phone || !address) {
        alert("Vui lòng điền đầy đủ thông tin giao hàng (Họ tên, SĐT, Địa chỉ)");
        return false;
    }
    return true;
}

function goToStep(idx) {
    if(idx < 0 || idx > 2) return;
    
    // Validate
    if (idx > currentStepIdx) {
        if (currentStepIdx === 0 && !validateStep1()) return;
    }

    currentStepIdx = idx;
    
    // Update contents
    document.querySelectorAll('.step-content').forEach((el, i) => {
        el.classList.toggle('active', i === currentStepIdx);
    });
    
    // Update progress line
    const progressLine = document.getElementById('progressLine');
    if(currentStepIdx === 0) progressLine.style.width = '0%';
    else if(currentStepIdx === 1) progressLine.style.width = '50%';
    else progressLine.style.width = '100%';
    
    // Update step circles
    for (let i = 0; i < 3; i++) {
        const el = document.getElementById('step' + (i+1));
        const circle = el.querySelector('.step-circle');
        
        if (i < currentStepIdx) {
            el.className = 'step-item completed';
            circle.innerHTML = '<i class="bi bi-check" style="font-size:1.2rem;"></i>';
        } else if (i === currentStepIdx) {
            el.className = 'step-item active';
            circle.innerHTML = i + 1;
        } else {
            el.className = 'step-item';
            circle.innerHTML = i + 1;
        }
    }

    // Populate confirm data
    if(currentStepIdx === 2) {
        document.getElementById('confirmName').innerText = document.getElementById('shipName').value;
        document.getElementById('confirmPhone').innerText = document.getElementById('shipPhone').value;
        document.getElementById('confirmAddress').innerText = document.getElementById('shipAddress').value;
        document.getElementById('confirmPayment').innerText = getPaymentMethodName(document.getElementById('selectedPaymentMethod').value);
    }
    
    window.scrollTo(0, 0);
}
</script>

<?php include '../includes/footer.php'; ?>
