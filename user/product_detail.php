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

        <!-- Main Detail Container -->
        <div class="bg-white rounded-4 p-4 p-lg-5 mb-5 shadow-sm" style="border: 1px solid var(--border-color);">
            <div class="row g-5 mb-5">
                <!-- Left: Image Gallery -->
                <div class="col-md-5">
                    <div class="position-relative overflow-hidden rounded-4 mb-3">
                        <img src="<?= $mainImg ?>" alt="<?= htmlspecialchars($product['ten_san_pham']) ?>" id="mainImage" class="w-100 object-fit-cover" style="max-height: 450px;">
                    </div>
                    
                    <div class="d-flex gap-2 justify-content-center">
                        <?php if(empty($images)): ?>
                            <div class="p-1 rounded-3 active border" style="width: 80px; height: 80px; cursor: pointer; border-color: var(--primary) !important;" onclick="document.getElementById('mainImage').src=this.querySelector('img').src">
                                <img src="<?= $mainImg ?>" alt="Thumb" class="w-100 h-100 object-fit-cover rounded-2">
                            </div>
                        <?php else: ?>
                            <?php foreach($images as $index => $img): 
                                $thumbSrc = filter_var($img['duong_dan_anh'], FILTER_VALIDATE_URL) ? $img['duong_dan_anh'] : '../uploads/' . $img['duong_dan_anh'];
                            ?>
                            <div class="p-1 rounded-3 border <?= $index === 0 ? 'active' : '' ?>" style="width: 80px; height: 80px; cursor: pointer; border-color: <?= $index === 0 ? 'var(--primary)' : 'var(--border-color)' ?> !important;" onclick="document.getElementById('mainImage').src=this.querySelector('img').src">
                                <img src="<?= $thumbSrc ?>" alt="Thumb" class="w-100 h-100 object-fit-cover rounded-2">
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Right: Info -->
                <div class="col-md-7">
                    <div class="d-flex gap-2 mb-3">
                        <span class="pd-badge category"><?= htmlspecialchars($product['ten_danh_muc'] ?: 'Sản phẩm') ?></span>
                        <span class="pd-badge brand"><?= htmlspecialchars($product['ten_thuong_hieu'] ?: 'Không xác định') ?></span>
                        <span class="pd-badge" style="color: var(--text-muted);">Mã SP: <?= htmlspecialchars($product['ma_san_pham'] ?: 'N/A') ?></span>
                    </div>
                    
                    <h1 class="pd-title"><?= htmlspecialchars($product['ten_san_pham']) ?></h1>
                    
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="text-warning fs-6">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                        </div>
                        <span class="fw-bold fs-6">0.0</span>
                        <span class="text-muted fs-6">(0 đánh giá)</span>
                    </div>
                    
                    <div class="pd-price-box d-flex align-items-baseline mb-4">
                        <div class="pd-price"><?= $price ?> ₫</div>
                    </div>
                    
                    <div class="text-muted mb-4" style="font-size: 15px; line-height: 1.6;">
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
                        <span class="fw-medium text-dark">Số lượng:</span>
                        <div class="qty-input-group">
                            <button class="qty-btn" onclick="this.parentNode.querySelector('input').stepDown()">-</button>
                            <input type="number" class="qty-input" value="1" min="1" max="<?= max(1, $product['so_luong_ton']) ?>">
                            <button class="qty-btn" onclick="this.parentNode.querySelector('input').stepUp()">+</button>
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="d-flex gap-3 mb-4">
                        <button class="pd-btn-buy flex-grow-1" <?= $product['so_luong_ton'] <= 0 ? 'disabled' : '' ?> onclick="window.location.href='cart.php'">Mua ngay</button>
                        <button class="pd-btn-cart flex-grow-1 ajax-cart-btn" data-id="<?= $id ?>" data-name="<?= htmlspecialchars($product['ten_san_pham']) ?>" data-price="<?= $row['gia'] ?>" data-brand="<?= htmlspecialchars($product['ten_thuong_hieu']) ?>" data-image="<?= htmlspecialchars($mainImg) ?>" <?= $product['so_luong_ton'] <= 0 ? 'disabled' : '' ?>>
                            <i class="bi bi-cart3 me-1"></i> Thêm vào giỏ
                        </button>
                        
                        <?php
                            $is_fav = false;
                            if (isset($_SESSION['favorites'])) {
                                foreach ($_SESSION['favorites'] as $f) { if ($f['id'] == $id) { $is_fav = true; break; } }
                            }
                        ?>
                        <button class="pd-btn-heart flex-shrink-0 ajax-fav-btn" data-id="<?= $id ?>" data-name="<?= htmlspecialchars($product['ten_san_pham']) ?>" data-price="<?= $row['gia'] ?>" data-brand="<?= htmlspecialchars($product['ten_thuong_hieu']) ?>" data-image="<?= htmlspecialchars($mainImg) ?>">
                            <i class="bi <?= $is_fav ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
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

            <!-- Tabs Area -->
            <div class="border-top pt-4 mt-2">
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
        <!-- DEMO STATE: Hiển thị giao diện Demo khi DB trống (theo screenshot) -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none text-muted">Sản phẩm</a></li>
                <li class="breadcrumb-item"><a href="shop.php?category=1" class="text-decoration-none text-muted">Điện thoại</a></li>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">iPhone 15 Pro Max</li>
            </ol>
        </nav>

        <div class="bg-white rounded-4 p-4 p-lg-5 mb-5 shadow-sm" style="border: 1px solid var(--border-color);">
            <div class="row g-5 mb-5">
                <!-- Left: Image Gallery -->
                <div class="col-md-5">
                    <div class="position-relative overflow-hidden rounded-4 mb-3">
                        <div class="position-absolute top-0 start-0 m-3 bg-danger text-white px-2 py-1 rounded-pill" style="font-size: 0.85rem; font-weight: 600; z-index: 2;">
                            <i class="bi bi-lightning-fill"></i> -15%
                        </div>
                        <img src="https://cdn.tgdd.vn/Products/Images/42/305658/iphone-15-pro-max-blue-thumbnew-600x600.jpg" alt="iPhone 15 Pro Max" id="mainImageDemo" class="w-100 object-fit-cover">
                    </div>
                </div>
                
                <!-- Right: Info -->
                <div class="col-md-7">
                    <div class="d-flex gap-2 mb-3">
                        <span class="pd-badge category">Điện thoại</span>
                        <span class="pd-badge brand">Apple</span>
                        <span class="pd-badge" style="color: var(--text-muted); font-weight: 500;">#APPL-IP15PM-256</span>
                    </div>
                    
                    <h1 class="pd-title">iPhone 15 Pro Max</h1>
                    
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="text-warning fs-6">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                        </div>
                        <span class="fw-bold fs-6">4.8</span>
                        <span class="text-muted fs-6">(234 đánh giá)</span>
                    </div>
                    
                    <div class="pd-price-box d-flex align-items-baseline mb-1">
                        <div class="pd-price">25.491.500 ₫</div>
                        <div class="pd-old-price ms-3">29.990.000 ₫</div>
                    </div>
                    <div class="pd-save-text mb-4">Tiết kiệm 4.498.500 ₫</div>
                    
                    <div class="text-muted mb-4" style="font-size: 15px; line-height: 1.6;">
                        iPhone 15 Pro Max là đỉnh cao công nghệ của Apple với màn hình Super Retina XDR 6.7 inch ProMotion 120Hz, chip A17 Pro mạnh mẽ, camera chính 48MP với khả năng zoom quang học 5x, pin lên đến 29 giờ sử dụng liên tục và khung viền titanium cao cấp.
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 mb-4 text-success fw-medium" style="font-size: 14px;">
                        <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Còn 45 sản phẩm trong kho
                    </div>
                    
                    <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom">
                        <span class="fw-medium text-dark">Số lượng:</span>
                        <div class="qty-input-group">
                            <button class="qty-btn" onclick="this.parentNode.querySelector('input').stepDown()">-</button>
                            <input type="number" class="qty-input" value="1" min="1" max="45">
                            <button class="qty-btn" onclick="this.parentNode.querySelector('input').stepUp()">+</button>
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="d-flex gap-3 mb-4">
                        <button class="pd-btn-buy flex-grow-1" onclick="window.location.href='cart.php'">Mua ngay</button>
                        <?php
                            $fake_id = 999;
                            $demo_name = 'iPhone 15 Pro Max';
                            $demo_price = 25491500;
                            $demo_brand = 'Apple';
                            $demo_img = 'https://cdn.tgdd.vn/Products/Images/42/305658/iphone-15-pro-max-blue-thumbnew-600x600.jpg';
                            
                            $is_fav_demo = false;
                            if (isset($_SESSION['favorites'])) {
                                foreach ($_SESSION['favorites'] as $f) { if ($f['id'] == $fake_id) { $is_fav_demo = true; break; } }
                            }
                        ?>
                        <button class="pd-btn-cart flex-grow-1 ajax-cart-btn" data-id="<?= $fake_id ?>" data-name="<?= $demo_name ?>" data-price="<?= $demo_price ?>" data-brand="<?= $demo_brand ?>" data-image="<?= $demo_img ?>">
                            <i class="bi bi-cart3 me-1"></i> Thêm vào giỏ
                        </button>
                        <button class="pd-btn-heart flex-shrink-0 ajax-fav-btn" data-id="<?= $fake_id ?>" data-name="<?= $demo_name ?>" data-price="<?= $demo_price ?>" data-brand="<?= $demo_brand ?>" data-image="<?= $demo_img ?>">
                            <i class="bi <?= $is_fav_demo ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
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

            <!-- Tabs Area -->
            <div class="border-top pt-4 mt-2">
            <div class="custom-tabs">
                <button class="custom-tab active" data-bs-toggle="tab" data-bs-target="#desc-demo">Mô tả sản phẩm</button>
                <button class="custom-tab" data-bs-toggle="tab" data-bs-target="#specs-demo">Thông số kỹ thuật (9)</button>
                <button class="custom-tab" data-bs-toggle="tab" data-bs-target="#reviews-demo">Đánh giá (3)</button>
            </div>
            
            <div class="tab-content mt-4">
                <div class="tab-pane fade show active" id="desc-demo">
                    <div class="text-muted" style="font-size: 15px; max-width: 800px; line-height: 1.8;">
                        <p>iPhone 15 Pro Max là đỉnh cao công nghệ của Apple với màn hình Super Retina XDR 6.7 inch ProMotion 120Hz, chip A17 Pro được sản xuất trên tiến trình 3nm, camera chính 48MP với khả năng zoom quang học 5x, pin lên đến 29 giờ sử dụng liên tục và khung viền làm từ Titanium cao cấp lần đầu tiên xuất hiện trên iPhone.</p>
                        <p>Sản phẩm này mang lại trải nghiệm cầm nắm tuyệt vời, sắc sảo từ những chi tiết nhỏ nhất. Phù hợp cho những ai yêu thích sự hoàn hảo và mạnh mẽ.</p>
                    </div>
                </div>
                <div class="tab-pane fade" id="specs-demo">
                    <p class="text-muted">Đang cập nhật...</p>
                </div>
                <div class="tab-pane fade" id="reviews-demo">
                    <p class="text-muted">Chưa có đánh giá nào.</p>
                </div>
            </div>
        </div>
        
        <!-- Sản phẩm liên quan Section -->
        <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
            <h4 class="fw-bold m-0 text-dark">Sản phẩm liên quan</h4>
            <a href="shop.php" class="text-primary text-decoration-none fw-medium">Xem thêm</a>
        </div>
        <div class="row g-4 mb-5">
            <!-- Đổi sang product-card từ style.css -->
            <div class="col-6 col-lg-3">
                <a href="product_detail.php?id=2" class="product-card">
                    <div class="p-img-box">
                        <img src="https://cdn.tgdd.vn/Products/Images/42/307174/samsung-galaxy-s24-ultra-grey-thumb-600x600.jpg" alt="Samsung Galaxy S24 Ultra">
                    </div>
                    <div class="p-content">
                        <div class="p-brand">Samsung</div>
                        <div class="p-title">Samsung Galaxy S24 Ultra</div>
                        <div class="p-rating">
                            <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i>
                            <span class="text-muted ms-1">(198)</span>
                        </div>
                        <div class="mt-auto d-flex justify-content-between align-items-end">
                            <div class="price-wrap">
                                <div class="p-price">26.990.000 ₫</div>
                            </div>
                            <div class="btn-add-circle" onclick="event.preventDefault();"><i class="bi bi-cart3"></i></div>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-6 col-lg-3">
                <a href="product_detail.php?id=3" class="product-card">
                    <div class="p-img-box">
                        <img src="https://dienmaythienhoa.vn/wp-content/uploads/2024/02/xiaomi-14-ultra-11.jpg" alt="Xiaomi 14 Ultra">
                    </div>
                    <div class="p-content">
                        <div class="p-brand">Xiaomi</div>
                        <div class="p-title">Xiaomi 14 Ultra</div>
                        <div class="p-rating">
                            <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-half text-warning"></i>
                            <span class="text-muted ms-1">(112)</span>
                        </div>
                        <div class="mt-auto d-flex justify-content-between align-items-end">
                            <div class="price-wrap">
                                <div class="p-price">23.990.000 ₫</div>
                            </div>
                            <div class="btn-add-circle" onclick="event.preventDefault();"><i class="bi bi-cart3"></i></div>
                        </div>
                    </div>
                </a>
            </div>
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

  </div>
</div>

<?php include '../includes/footer.php'; ?>
