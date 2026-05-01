<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/models/Product.php';

$p = (new Product(db_connect()))->getById((int)($_GET['id'] ?? 0));
if (!$p) {
    http_response_code(404);
    require __DIR__ . '/includes/header.php';
    echo '<main class="container py-5 text-center"><i class="bi bi-exclamation-circle display-1 text-muted"></i><h3 class="mt-3">Không tìm thấy sản phẩm</h3><a href="' . BASE_URL . '/shop.php" class="btn btn-primary mt-3">Quay lại cửa hàng</a></main>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$price       = (!empty($p['gia_giam']) && $p['gia_giam'] < $p['gia_ban']) ? $p['gia_giam'] : $p['gia_ban'];
$hasDiscount = !empty($p['gia_giam']) && $p['gia_giam'] < $p['gia_ban'];
$discountPct = $hasDiscount ? round((1 - $p['gia_giam'] / $p['gia_ban']) * 100) : 0;
$inStock     = (int)$p['so_luong_ton'] > 0;
$stockClass  = (int)$p['so_luong_ton'] <= 5 ? 'low' : '';

$page_title = h($p['ten_san_pham']) . ' — TechShop';
require __DIR__ . '/includes/header.php';
?>
<main class="container py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:13px">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/shop.php">Cửa hàng</a></li>
            <?php if (!empty($p['ten_danh_muc'])): ?>
            <li class="breadcrumb-item">
                <a href="<?= BASE_URL ?>/shop.php?category=<?= (int)$p['id_danh_muc'] ?>">
                    <?= h($p['ten_danh_muc']) ?>
                </a>
            </li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?= h($p['ten_san_pham']) ?></li>
        </ol>
    </nav>

    <div class="bg-white rounded-4 shadow-sm p-4">
        <div class="row g-4 align-items-start">

            <!-- Image -->
            <div class="col-lg-5">
                <div class="product-detail-img">
                    <img src="<?= product_img_url($p['hinh_anh']) ?>"
                         alt="<?= h($p['ten_san_pham']) ?>">
                </div>
            </div>

            <!-- Info -->
            <div class="col-lg-7">
                <div class="p-brand mb-1"><?= h($p['ten_thuong_hieu'] ?? '') ?></div>
                <h1 class="fw-800" style="font-size:clamp(20px,3vw,28px);line-height:1.3"><?= h($p['ten_san_pham']) ?></h1>

                <?php if (!empty($p['mo_ta_ngan'])): ?>
                <p class="text-muted mt-2" style="font-size:15px"><?= h($p['mo_ta_ngan']) ?></p>
                <?php endif; ?>

                <hr class="my-3">

                <!-- Price -->
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="p-price" style="font-size:32px"><?= format_price($price) ?></span>
                    <?php if ($hasDiscount): ?>
                        <span class="p-old-price" style="font-size:18px"><?= format_price($p['gia_ban']) ?></span>
                        <span class="badge bg-danger" style="font-size:14px">-<?= $discountPct ?>%</span>
                    <?php endif; ?>
                </div>

                <!-- Stock -->
                <div class="mt-3">
                    <?php if ($inStock): ?>
                        <span class="stock-indicator <?= $stockClass ?>">
                            <span class="dot"></span>
                            <?= (int)$p['so_luong_ton'] <= 5
                                ? 'Chỉ còn ' . (int)$p['so_luong_ton'] . ' sản phẩm'
                                : 'Còn hàng (' . (int)$p['so_luong_ton'] . ' sản phẩm)' ?>
                        </span>
                    <?php else: ?>
                        <span class="stock-indicator out"><span class="dot"></span>Hết hàng</span>
                    <?php endif; ?>
                </div>

                <hr class="my-3">

                <!-- Qty + Actions -->
                <?php if ($inStock): ?>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="qty-selector">
                        <button type="button" class="qty-btn" data-dir="down">−</button>
                        <input type="number" id="qty-input" value="1" min="1"
                               max="<?= (int)$p['so_luong_ton'] ?>">
                        <button type="button" class="qty-btn" data-dir="up">+</button>
                    </div>
                    <button class="btn btn-primary btn-lg ajax-cart-btn flex-fill"
                            data-id="<?= (int)$p['id_san_pham'] ?>" style="max-width:220px">
                        <i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng
                    </button>
                    <button class="btn btn-outline-danger btn-lg ajax-favorite-btn"
                            data-id="<?= (int)$p['id_san_pham'] ?>" title="Thêm yêu thích">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>
                <?php else: ?>
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="bi bi-x-circle me-2"></i>Hết hàng
                </button>
                <?php endif; ?>

                <!-- Badges -->
                <div class="d-flex gap-2 flex-wrap mt-4">
                    <span class="badge bg-light text-dark border py-2 px-3">
                        <i class="bi bi-shield-check text-success me-1"></i>Bảo hành chính hãng
                    </span>
                    <span class="badge bg-light text-dark border py-2 px-3">
                        <i class="bi bi-truck text-primary me-1"></i>Giao hàng toàn quốc
                    </span>
                    <span class="badge bg-light text-dark border py-2 px-3">
                        <i class="bi bi-arrow-repeat text-warning me-1"></i>Đổi trả 7 ngày
                    </span>
                </div>
            </div>
        </div>

        <?php if (!empty($p['mo_ta_chi_tiet'])): ?>
        <hr class="mt-4">
        <div class="mt-3">
            <h5 class="fw-800 mb-3">Mô tả sản phẩm</h5>
            <div style="font-size:15px;line-height:1.8;color:#334155">
                <?= nl2br(h($p['mo_ta_chi_tiet'])) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
