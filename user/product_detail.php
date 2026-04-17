<?php
include_once '../config/config.php';
include_once '../config/database.php';
$conn = db_connect();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
$images = [];

if ($productId > 0) {
    // Lấy thông tin sản phẩm
    $query = "SELECT sp.*, th.ten_thuong_hieu, dm.ten_danh_muc 
              FROM san_pham sp 
              LEFT JOIN thuong_hieu th ON sp.id_thuong_hieu = th.id_thuong_hieu 
              LEFT JOIN danh_muc dm ON sp.id_danh_muc = dm.id_danh_muc 
              WHERE sp.id_san_pham = :id AND sp.trang_thai != 'ngung_ban'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Lấy danh sách hình ảnh (nếu có bảng hinh_anh_san_pham thiết lập đúng, ở đây ta query giả dụ thế)
        $queryImg = "SELECT * FROM hinh_anh_san_pham WHERE id_san_pham = :id ORDER BY la_anh_chinh DESC";
        $stmtImg = $conn->prepare($queryImg);
        $stmtImg->bindParam(':id', $productId);
        $stmtImg->execute();
        $images = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
    }
}

include '../includes/header.php';
?>

<div class="container my-4">
    <?php if ($product): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="shop.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#">Sản phẩm</a></li>
                <li class="breadcrumb-item"><a href="#"><?= htmlspecialchars($product['ten_danh_muc'] ?: 'Khác') ?></a></li>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page"><?= htmlspecialchars($product['ten_san_pham']) ?></li>
            </ol>
        </nav>

        <?php
            // Xử lý ảnh chính và mảng ảnh con
            $mainImg = 'https://placehold.co/600x600?text=No+Image';
            if (!empty($product['hinh_anh_chinh'])) {
                $mainImg = filter_var($product['hinh_anh_chinh'], FILTER_VALIDATE_URL) ? $product['hinh_anh_chinh'] : '../uploads/' . $product['hinh_anh_chinh'];
            }
            
            // Xử lý giá
            $price = number_format($product['gia'], 0, ',', '.');
        ?>

        <!-- Main Detail -->
        <div class="row g-5 mb-5">
            <!-- Left: Image Gallery -->
            <div class="col-md-5">
                <div class="pd-image-box mb-3">
                    <img src="<?= $mainImg ?>" alt="<?= htmlspecialchars($product['ten_san_pham']) ?>" id="mainImage">
                </div>
                
                <div class="d-flex gap-2 justify-content-center">
                    <?php if(empty($images)): ?>
                        <div class="pd-image-box p-2 active" style="width: 80px; height: 80px; cursor: pointer; border-color: var(--primary);" onclick="document.getElementById('mainImage').src=this.querySelector('img').src">
                            <img src="<?= $mainImg ?>" alt="Thumb" style="max-height: 100%;">
                        </div>
                    <?php else: ?>
                        <?php foreach($images as $index => $img): 
                            $thumbSrc = filter_var($img['duong_dan_anh'], FILTER_VALIDATE_URL) ? $img['duong_dan_anh'] : '../uploads/' . $img['duong_dan_anh'];
                        ?>
                        <div class="pd-image-box p-2 <?= $index === 0 ? 'active' : '' ?>" style="width: 80px; height: 80px; cursor: pointer; <?= $index === 0 ? 'border-color: var(--primary);' : '' ?>" onclick="document.getElementById('mainImage').src=this.querySelector('img').src">
                            <img src="<?= $thumbSrc ?>" alt="Thumb" style="max-height: 100%;">
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right: Info -->
            <div class="col-md-7">
                <div class="bg-white rounded-4 p-4 p-lg-5" style="border: 1px solid var(--border-color);">
                    <div class="d-flex gap-2 mb-3">
                        <span class="pd-badge category"><?= htmlspecialchars($product['ten_danh_muc'] ?: 'Sản phẩm') ?></span>
                        <span class="pd-badge brand"><?= htmlspecialchars($product['ten_thuong_hieu'] ?: 'Không xác định') ?></span>
                        <span class="pd-badge brand" style="background: transparent; color: var(--text-muted); border: 1px solid var(--border-color);">Mã SP: <?= htmlspecialchars($product['ma_san_pham'] ?: 'N/A') ?></span>
                    </div>
                    
                    <h1 class="pd-title"><?= htmlspecialchars($product['ten_san_pham']) ?></h1>
                    
                    <div class="d-flex align-items-center gap-2 mb-4 pb-4 border-bottom">
                        <div class="text-warning fs-6">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                        </div>
                        <span class="fw-bold fs-6">0.0</span>
                        <span class="text-muted fs-6">(0 đánh giá)</span>
                    </div>
                    
                    <div class="pd-price"><?= $price ?> ₫</div>
                    
                    <div class="text-muted mb-4" style="font-size: 14px;">
                        <?= htmlspecialchars($product['mo_ta_ngan']) ?>
                    </div>
                    
                    <?php if($product['so_luong_ton'] > 0): ?>
                        <div class="d-flex align-items-center gap-2 mb-4 text-success fw-medium" style="font-size: 14px;">
                            <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Còn <?= $product['so_luong_ton'] ?> sản phẩm trong kho
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center gap-2 mb-4 text-danger fw-medium" style="font-size: 14px;">
                            <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Hết hàng
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom">
                        <span class="fw-medium">Số lượng:</span>
                        <div class="qty-input-group">
                            <button class="qty-btn" onclick="this.parentNode.querySelector('input').stepDown()">-</button>
                            <input type="number" class="qty-input" value="1" min="1" max="<?= max(1, $product['so_luong_ton']) ?>">
                            <button class="qty-btn" onclick="this.parentNode.querySelector('input').stepUp()">+</button>
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="d-flex gap-3 mb-4">
                        <button class="pd-btn-buy flex-grow-1" <?= $product['so_luong_ton'] <= 0 ? 'disabled' : '' ?>>Mua ngay</button>
                        <button class="pd-btn-cart flex-grow-1" <?= $product['so_luong_ton'] <= 0 ? 'disabled' : '' ?>>
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                        <button class="pd-btn-heart flex-shrink-0">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                    
                    <!-- Features List -->
                    <div class="pd-features-list">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="item">
                                    <i class="bi bi-shield-check"></i> Bảo hành chính hãng 12 tháng
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="item">
                                    <i class="bi bi-truck"></i> Giao hàng miễn phí toàn quốc
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="item">
                                    <i class="bi bi-arrow-repeat"></i> Đổi trả trong 30 ngày
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="item">
                                    <i class="bi bi-patch-check"></i> Hàng chính hãng 100%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Area -->
        <div class="bg-white rounded-4 p-4 p-lg-5 mb-5" style="border: 1px solid var(--border-color);">
            <div class="custom-tabs">
                <button class="custom-tab active" data-bs-toggle="tab" data-bs-target="#desc">Mô tả sản phẩm</button>
                <button class="custom-tab" data-bs-toggle="tab" data-bs-target="#specs">Thông số kỹ thuật</button>
                <button class="custom-tab" data-bs-toggle="tab" data-bs-target="#reviews">Đánh giá</button>
            </div>
            
            <div class="tab-content mt-4">
                <div class="tab-pane fade show active" id="desc">
                    <div class="text-muted" style="font-size: 15px; max-width: 800px; line-height: 1.8;">
                        <?php 
                            if (!empty($product['mo_ta_chi_tiet'])) {
                                echo nl2br(htmlspecialchars($product['mo_ta_chi_tiet']));
                            } else {
                                echo '<p>Đang cập nhật mô tả chi tiết...</p>';
                            }
                        ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="specs">
                    <p class="text-muted">Đang cập nhật...</p>
                </div>
                <div class="tab-pane fade" id="reviews">
                    <p class="text-muted">Chưa có đánh giá nào.</p>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- EMPTY STATE -->
        <div class="py-5 text-center bg-white rounded-4" style="border: 1px solid var(--border-color);">
            <div class="mb-4">
                <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
            </div>
            <h4 class="fw-bold mb-3">Sản phẩm không tồn tại hoặc chưa có thông tin</h4>
            <p class="text-muted mb-4">Sản phẩm bạn đang tìm kiếm hiện chưa được Admin thêm vào hệ thống.<br>Vui lòng quay lại sau nhé!</p>
            <a href="shop.php" class="btn btn-primary rounded-pill px-4 py-2 fw-medium"><i class="bi bi-arrow-left me-2"></i>Quay lại trang chủ</a>
        </div>
    <?php endif; ?>
</div>

<script>
    document.querySelectorAll('.custom-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.custom-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Xử lý đổi nội dung tab cơ bản
            document.querySelectorAll('.tab-pane').forEach(c => {
                c.classList.remove('show', 'active');
            });
            const target = this.getAttribute('data-bs-target');
            if(target) {
                document.querySelector(target).classList.add('show', 'active');
            }
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
