<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$fav_items = isset($_SESSION['favorites']) ? $_SESSION['favorites'] : [];
$is_empty = empty($fav_items);

$page_title = "Sản phẩm yêu thích";
include '../includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Sản phẩm yêu thích</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5" style="min-height: 70vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0" style="color: #1e293b;">Sản phẩm yêu thích <span class="text-muted fs-5 fw-normal">(<?= count($fav_items) ?>)</span></h2>
    </div>

    <?php if ($is_empty): ?>
        <!-- EMPTY STATE -->
        <div class="text-center py-5 my-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle mb-4" style="width: 120px; height: 120px;">
                <i class="bi bi-heartbreak text-danger" style="font-size: 3.5rem;"></i>
            </div>
            <h4 class="fw-bold mb-3 text-dark">Chưa có sản phẩm yêu thích</h4>
            <p class="text-muted mb-4" style="font-size: 1.05rem;">Bạn chưa lưu sản phẩm nào vào danh sách Yêu thích.<br>Hãy lướt vài vòng để tìm kiếm những siêu phẩm ưng ý nhé!</p>
            <a href="shop.php" class="btn btn-primary rounded-pill px-5 py-3 fw-medium" style="background-color: #2563eb; border:none; font-size: 1.05rem;">
                Khám phá ngay <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    <?php else: ?>
        <!-- GRID -->
        <div class="row g-4 mb-5">
            <?php foreach ($fav_items as $item): 
                $formattedPrice = number_format($item['price'], 0, ',', '.');
                $id = $item['id'];
                $name = $item['name'];
                $brand = $item['brand'];
                $img = $item['image'];
            ?>
            <div class="col-6 col-md-4 col-lg-3 position-relative" id="fav-card-<?= $id ?>">
                <a href="product_detail.php?id=<?= $id ?>" class="product-card">
                    <div class="p-img-box">
                        <div class="badge-discount"><i class="bi bi-lightning-fill"></i> -15%</div>
                        <!-- Favorite Button (Active) -->
                        <div class="btn-favorite ajax-fav-btn" data-id="<?= $id ?>" data-name="<?= htmlspecialchars($name) ?>" data-price="<?= $item['price'] ?>" data-brand="<?= htmlspecialchars($brand) ?>" data-image="<?= htmlspecialchars($img) ?>" onclick="event.preventDefault(); document.getElementById('fav-card-<?= $id ?>').style.opacity = '0.5'; setTimeout(() => location.reload(), 1000);">
                            <i class="bi bi-heart-fill text-danger"></i>
                        </div>
                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($name) ?>">
                    </div>
                    <div class="p-content">
                        <div class="p-brand"><?= htmlspecialchars($brand) ?></div>
                        <div class="p-title"><?= htmlspecialchars($name) ?></div>
                        <div class="p-rating">
                            <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-half text-warning"></i>
                            <span class="text-muted ms-1">(150)</span>
                        </div>
                        <div class="mt-auto d-flex justify-content-between align-items-end">
                            <div class="price-wrap">
                                <div class="p-price"><?= $formattedPrice ?> ₫</div>
                            </div>
                            <!-- Add to cart -->
                            <div class="btn-add-circle ajax-cart-btn" data-id="<?= $id ?>" data-name="<?= htmlspecialchars($name) ?>" data-price="<?= $item['price'] ?>" data-brand="<?= htmlspecialchars($brand) ?>" data-image="<?= htmlspecialchars($img) ?>" onclick="event.preventDefault();"><i class="bi bi-cart3"></i></div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
