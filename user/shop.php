<?php
include_once '../config/config.php';
include_once '../config/database.php';
$conn = db_connect();

include '../includes/header.php';
?>

<div class="container my-4">
    <!-- Hero Slider -->
    <div class="hero-slider rounded-4 mb-5">
        <div class="row align-items-center px-4 px-md-5">
            <div class="col-md-6 z-2">
                <div class="subtitle">Siêu mỏng • Pin 18h • Không quạt</div>
                <h2>MacBook Air M3</h2>
                <div class="price-box">
                    <span class="price">24.641.500 ₫</span>
                    <span class="old-price">28.990.000 ₫</span>
                    <span class="discount">-15%</span>
                </div>
                <div class="d-flex gap-3">
                    <a href="#" class="btn-buy d-inline-flex align-items-center gap-2">Mua ngay <i class="bi bi-arrow-right"></i></a>
                    <a href="#" class="btn-more">Xem thêm</a>
                </div>
            </div>
            <div class="col-md-6 position-relative mt-4 mt-md-0 z-1 text-center">
                <img src="https://cdn.tgdd.vn/Products/Images/44/322613/macbook-air-13-inch-m3-16gb-256gb-silver-thumb-600x600.jpg" alt="MacBook Air M3" class="hero-image">
            </div>
        </div>
        <div class="hero-dots">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>

    <!-- Feature Icons -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-lg-3">
            <div class="feature-box">
                <div class="feature-icon text-primary"><i class="bi bi-truck"></i></div>
                <div>
                    <div class="feature-title">Miễn phí Vận chuyển</div>
                    <div class="feature-desc">Đơn hàng từ 500K</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="feature-box">
                <div class="feature-icon text-success"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div class="feature-title">Bảo hành chính hãng</div>
                    <div class="feature-desc">12 - 24 tháng</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="feature-box">
                <div class="feature-icon text-warning"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="feature-title">Giao hàng nhanh</div>
                    <div class="feature-desc">Trong 2 - 4 giờ</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="feature-box">
                <div class="feature-icon text-info"><i class="bi bi-headset"></i></div>
                <div>
                    <div class="feature-title">Hỗ trợ 24/7</div>
                    <div class="feature-desc">1900 1234 miễn phí</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="section-head">
        <h3 class="section-title">Danh mục sản phẩm</h3>
        <a href="#" class="link-all">Xem tất cả <i class="bi bi-chevron-right ms-1" style="font-size: 10px;"></i></a>
    </div>
    
    <div class="row g-3 mb-5 justify-content-center">
        <?php
        try {
            $queryCat = "SELECT * FROM danh_muc LIMIT 7";
            $stmtCat = $conn->prepare($queryCat);
            $stmtCat->execute();
            
            if ($stmtCat->rowCount() > 0) {
                $icons = [
                    'Điện thoại' => 'bi-phone',
                    'Laptop' => 'bi-laptop',
                    'Máy tính bảng' => 'bi-tablet',
                    'Tai nghe' => 'bi-headphones',
                    'Màn hình' => 'bi-display',
                    'Đồng hồ thông minh' => 'bi-smartwatch',
                    'Phụ kiện' => 'bi-usb-drive',
                    'Mặc định' => 'bi-grid'
                ];
                while ($row = $stmtCat->fetch(PDO::FETCH_ASSOC)) {
                    $iconClass = isset($icons[$row['ten_danh_muc']]) ? $icons[$row['ten_danh_muc']] : $icons['Mặc định'];
                    echo '
                    <div class="col-4 col-md-2" style="width: 14.28%">
                        <a href="#" class="category-item border rounded-4 bg-white text-center d-block py-3 text-decoration-none">
                            <i class="bi ' . $iconClass . ' fs-1 text-primary"></i>
                            <span class="d-block mt-2 text-dark">' . htmlspecialchars($row['ten_danh_muc']) . '</span>
                        </a>
                    </div>';
                }
            } else {
                echo '<div class="col-12 text-center text-muted">Chưa có danh mục nào.</div>';
            }
        } catch(PDOException $e) {
            echo '<div class="col-12 text-center text-muted">Chưa cấu hình dữ liệu danh mục.</div>';
        }
        ?>
    </div>

    <!-- Tất cả sản phẩm -->
    <div class="section-head mt-5">
        <h3 class="section-title">Sản phẩm nổi bật</h3>
        <a href="#" class="link-all">Xem tất cả <i class="bi bi-chevron-right ms-1" style="font-size: 10px;"></i></a>
    </div>

    <div class="row g-4 mb-5">
        <?php
        $hasProducts = false;
        try {
            $query = "SELECT sp.*, th.ten_thuong_hieu 
                      FROM san_pham sp 
                      LEFT JOIN thuong_hieu th ON sp.id_thuong_hieu = th.id_thuong_hieu 
                      ORDER BY sp.ngay_tao DESC LIMIT 12";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $hasProducts = true;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $imgSrc = !empty($row['hinh_anh_chinh']) ? '../uploads/' . htmlspecialchars($row['hinh_anh_chinh']) : 'https://placehold.co/600x600?text=No+Image';
                    if (filter_var($row['hinh_anh_chinh'], FILTER_VALIDATE_URL)) {
                        $imgSrc = $row['hinh_anh_chinh'];
                    }
                    
                    $price = number_format((float)$row['gia'], 0, ',', '.');
                    $brand = $row['ten_thuong_hieu'] ? htmlspecialchars($row['ten_thuong_hieu']) : 'Unknown';
                    $name = htmlspecialchars($row['ten_san_pham']);
                    $id = $row['id_san_pham'];
                    
                    echo '
                    <div class="col-6 col-lg-3">
                        <div class="product-card">
                            <div class="p-img-box">
                                <a href="product_detail.php?id='.$id.'"><img src="'.$imgSrc.'" alt="'.$name.'"></a>
                            </div>
                            <div class="p-brand">'.$brand.'</div>
                            <a href="product_detail.php?id='.$id.'"><div class="p-title text-dark">'.$name.'</div></a>
                            <div class="p-rating">
                                <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-half text-warning"></i>
                                <span class="text-muted">(Chưa có)</span>
                            </div>
                            <div class="mt-auto">
                                <span class="p-price text-danger fw-bold">'.$price.' ₫</span>
                            </div>
                            <button class="btn-add-circle"><i class="bi bi-cart-plus"></i></button>
                        </div>
                    </div>';
                }
            }
        } catch(PDOException $e) {
            // Ignore missing table
        }

        if (!$hasProducts) {
            echo '
            <div class="col-12 py-5 text-center">
                <div class="mb-3"><i class="bi bi-box-seam text-secondary" style="font-size: 3rem;"></i></div>
                <h5 class="text-secondary fw-normal">Hiện tại cửa hàng chưa cập nhật sản phẩm nào.</h5>
                <p class="text-muted">Vui lòng chờ Admin thêm sản phẩm mới nhé bạn!</p>
            </div>';
        }
        ?>
    </div>

    <?php if ($stmt->rowCount() > 0): ?>
    <!-- Side by Side Promos (Only show if products exist) -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="promo-banner dark-blue">
                <div class="promo-badge">Mới ra mắt</div>
                <div class="promo-title">iPad Pro M4</div>
                <div class="promo-desc">Mỏng nhất. Mạnh nhất. Màn hình OLED tuyệt đỉnh.</div>
                <button class="promo-btn">Khám phá <i class="bi bi-arrow-right ms-1"></i></button>
                <img src="https://cdn.tgdd.vn/Products/Images/522/324838/ipad-pro-m4-11-inch-wifi-xam-thumb-1-600x600.jpg" alt="iPad Pro" class="promo-img" style="filter: drop-shadow(-10px 10px 15px rgba(0,0,0,0.3));">
            </div>
        </div>
        <div class="col-md-6">
            <div class="promo-banner dark">
                <div class="promo-badge bg-danger text-white">Gaming</div>
                <div class="promo-title">Asus ROG Monitor</div>
                <div class="promo-desc">360Hz, QHD, G-Sync. Đỉnh cao trải nghiệm gaming.</div>
                <button class="promo-btn bg-success text-white border-0">Xem ngay <i class="bi bi-arrow-right ms-1"></i></button>
                <img src="https://cdn.tgdd.vn/Products/Images/5697/305260/asus-rog-swift-oled-pg27aqdm-265-inch-2k-thumb-600x600.jpg" alt="Monitor" class="promo-img">
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>
