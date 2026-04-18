<?php
include_once '../config/config.php';
include_once '../config/database.php';
$conn = db_connect();

$selectedCategoryId = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : null;
$selectedCategoryName = null;
if ($selectedCategoryId) {
    try {
        $catStmt = $conn->prepare('SELECT ten_danh_muc FROM danh_muc WHERE id_danh_muc = :id_danh_muc LIMIT 1');
        $catStmt->execute([':id_danh_muc' => $selectedCategoryId]);
        $categoryRow = $catStmt->fetch(PDO::FETCH_ASSOC);
        if ($categoryRow) {
            $selectedCategoryName = $categoryRow['ten_danh_muc'];
        } else {
            $selectedCategoryId = null;
        }
    } catch (PDOException $e) {
        $selectedCategoryId = null;
    }
}

include '../includes/header.php';
?>

    <?php
    $heroSlides = [];
    try {
        $heroStmt = $conn->prepare(
            "SELECT sp.*, th.ten_thuong_hieu, km.phan_tram_giam
             FROM san_pham sp
             LEFT JOIN thuong_hieu th ON sp.id_thuong_hieu = th.id_thuong_hieu
             LEFT JOIN san_pham_khuyen_mai spkm ON sp.id_san_pham = spkm.id_san_pham
             LEFT JOIN khuyen_mai km ON spkm.id_khuyen_mai = km.id_khuyen_mai AND km.trang_thai = 'dang_dien_ra'
             WHERE sp.trang_thai = 'dang_ban'
             ORDER BY km.phan_tram_giam DESC, sp.ngay_tao DESC
             LIMIT 3"
        );
        $heroStmt->execute();
        $heroSlides = $heroStmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($heroSlides) === 0) {
            $fallbackStmt = $conn->prepare(
                "SELECT sp.*, th.ten_thuong_hieu, NULL AS phan_tram_giam
                 FROM san_pham sp
                 LEFT JOIN thuong_hieu th ON sp.id_thuong_hieu = th.id_thuong_hieu
                 WHERE sp.trang_thai = 'dang_ban'
                 ORDER BY sp.ngay_tao DESC
                 LIMIT 3"
            );
            $fallbackStmt->execute();
            $heroSlides = $fallbackStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $heroSlides = [];
    }
    ?>

    <div class="container my-4">
    <!-- Hero Slider -->
    <div class="hero-slider rounded-4 mb-5 position-relative">
        <div class="hero-slides-container">
            <?php if (count($heroSlides) > 0): ?>
                <?php foreach ($heroSlides as $index => $slide): ?>
                    <?php
                        $slideTitle = htmlspecialchars($slide['ten_san_pham']);
                        $slideSubtitle = htmlspecialchars($slide['mo_ta_ngan'] ?: 'Sản phẩm nổi bật');
                        $slidePrice = number_format((float) $slide['gia'], 0, ',', '.') . ' ₫';
                        $slideDiscount = !empty($slide['phan_tram_giam']) ? (int) $slide['phan_tram_giam'] : null;
                        $slideOldPrice = null;
                        if ($slideDiscount && $slideDiscount > 0) {
                            $oldPriceValue = $slide['gia'] / (1 - ($slideDiscount / 100));
                            $slideOldPrice = number_format((float) $oldPriceValue, 0, ',', '.') . ' ₫';
                        }
                        $slideImage = 'https://placehold.co/600x600?text=No+Image';
                        if (!empty($slide['hinh_anh_chinh'])) {
                            $slideImage = filter_var($slide['hinh_anh_chinh'], FILTER_VALIDATE_URL)
                                ? $slide['hinh_anh_chinh']
                                : '../uploads/' . htmlspecialchars($slide['hinh_anh_chinh']);
                        }
                        $slideUrl = 'product_detail.php?id=' . urlencode($slide['id_san_pham']);
                    ?>
                    <div class="hero-slide<?= $index === 0 ? ' active' : '' ?>" data-slide="<?= $index ?>">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="subtitle"><?= $slideSubtitle ?></div>
                                <h2><?= $slideTitle ?></h2>
                                <div class="price-box">
                                    <span class="price"><?= $slidePrice ?></span>
                                    <?php if ($slideOldPrice): ?>
                                        <span class="old-price"><?= $slideOldPrice ?></span>
                                    <?php endif; ?>
                                    <?php if ($slideDiscount): ?>
                                        <span class="discount">-<?= $slideDiscount ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= $slideUrl ?>" class="btn-buy d-inline-flex align-items-center gap-2">Mua ngay <i class="bi bi-arrow-right"></i></a>
                                    <a href="<?= $slideUrl ?>" class="btn-more">Xem thêm</a>
                                </div>
                            </div>
                            <div class="col-md-6 position-relative text-center">
                                <img src="<?= $slideImage ?>" alt="<?= $slideTitle ?>" class="hero-image">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="hero-slide active" data-slide="0">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="subtitle">Chưa có nội dung hiển thị</div>
                            <h2>Admin chưa thêm banner</h2>
                            <div class="price-box">
                                <span class="price">0 ₫</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn-buy d-inline-flex align-items-center gap-2">Mua ngay <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-6 position-relative text-center">
                            <img src="https://placehold.co/600x600?text=Banner+Coming+Soon" alt="Banner" class="hero-image">
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Navigation Arrows -->
        <button class="hero-arrow prev" id="heroSliderPrev">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="hero-arrow next" id="heroSliderNext">
            <i class="bi bi-chevron-right"></i>
        </button>
        <!-- Dots Navigation -->
        <div class="hero-dots">
            <?php if (count($heroSlides) > 0): ?>
                <?php foreach ($heroSlides as $index => $slide): ?>
                    <div class="dot<?= $index === 0 ? ' active' : '' ?>" data-index="<?= $index ?>"></div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="dot active" data-index="0"></div>
            <?php endif; ?>
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
                    <div class="feature-desc">123456789 miễn phí</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="section-head">
        <h3 class="section-title"><?php echo $selectedCategoryName ? 'Danh mục: ' . htmlspecialchars($selectedCategoryName) : 'Danh mục sản phẩm'; ?></h3>
        <a href="shop.php" class="link-all">Xem tất cả <i class="bi bi-chevron-right ms-1" style="font-size: 10px;"></i></a>
    </div>

    <div class="category-row mb-5">
        <?php
            $navCategories = require __DIR__ . '/../includes/category_buttons.php';
            foreach ($navCategories as $category) {
                $categoryUrl = 'shop.php?category=' . urlencode($category['id']);
                $activeClass = $selectedCategoryId === (int)$category['id'] ? ' border-primary shadow-sm' : '';
                echo '
                    <div class="category-col">
                        <a href="' . $categoryUrl . '" class="category-item border rounded-4 bg-white text-center d-block text-decoration-none' . $activeClass . '">
                            <span class="category-icon">' . htmlspecialchars($category['icon']) . '</span>
                            <span class="d-block mt-3 text-dark fw-semibold">' . htmlspecialchars($category['name']) . '</span>
                        </a>
                    </div>';
            }
        ?>
    </div>

    <!-- Tất cả sản phẩm -->
    <div class="section-head mt-5">
        <h3 class="section-title">Sản phẩm nổi bật</h3>
        <a href="shop.php" class="link-all">Xem tất cả <i class="bi bi-chevron-right ms-1" style="font-size: 10px;"></i></a>
    </div>

    <div class="row g-4 mb-5">
        <?php
        $hasProducts = false;
        try {
            if ($selectedCategoryId) {
                $query = "SELECT sp.*, th.ten_thuong_hieu 
                          FROM san_pham sp 
                          LEFT JOIN thuong_hieu th ON sp.id_thuong_hieu = th.id_thuong_hieu 
                          WHERE sp.id_danh_muc = :category_id 
                          ORDER BY sp.ngay_tao DESC LIMIT 12";
                $stmt = $conn->prepare($query);
                $stmt->execute([':category_id' => $selectedCategoryId]);
            } else {
                $query = "SELECT sp.*, th.ten_thuong_hieu 
                          FROM san_pham sp 
                          LEFT JOIN thuong_hieu th ON sp.id_thuong_hieu = th.id_thuong_hieu 
                          ORDER BY sp.ngay_tao DESC LIMIT 12";
                $stmt = $conn->prepare($query);
                $stmt->execute();
            }

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

<script>
// Hero Slider Control
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dots .dot');
    const prevBtn = document.getElementById('heroSliderPrev');
    const nextBtn = document.getElementById('heroSliderNext');
    let currentSlide = 0;
    let autoSlideTimeout;

    function showSlide(n) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        slides[n].classList.add('active');
        dots[n].classList.add('active');
        currentSlide = n;
        
        // Reset auto-slide timer
        clearTimeout(autoSlideTimeout);
        autoSlideTimeout = setTimeout(nextSlide, 5000);
    }

    function nextSlide() {
        let n = (currentSlide + 1) % slides.length;
        showSlide(n);
    }

    function prevSlide() {
        let n = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(n);
    }

    // Event listeners for arrows
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);

    // Event listeners for dots
    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            showSlide(index);
        });
    });

    // Start auto-slide
    autoSlideTimeout = setTimeout(nextSlide, 5000);
});
</script>

<?php include '../includes/footer.php'; ?>
