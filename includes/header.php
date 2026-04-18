<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php
// Tính tổng số lượng sản phẩm trong giỏ hàng
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $c_item) {
        $cart_count += $c_item['qty'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body>

    <div class="bg-primary text-white py-2 text-center" style="font-size: 13px;">
        <div class="container">
            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> 
            Flash Sale đang diễn ra! Giảm đến 15% cho sản phẩm chọn lọc. 
            <i class="bi bi-lightning-charge-fill text-warning me-1"></i>
        </div>
    </div>

    <header class="bg-white shadow-sm py-3 sticky-top">
        <div class="container d-flex align-items-center justify-content-between">
            
            <div class="d-flex align-items-center gap-4 gap-lg-5">
                <a href="shop.php" class="d-flex align-items-center gap-2 text-dark text-decoration-none fw-bold fs-4">
                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 18px;">T</div>
                    <span class="d-none d-sm-block">TechShop</span>
                </a>
                
                <nav class="d-none d-lg-flex align-items-center gap-4">
                    <a href="shop.php" class="text-dark text-decoration-none nav-link-custom">Trang chủ</a>
                    
    <div class="dropdown">
        <a href="#" class="text-decoration-none nav-link-custom dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Danh mục
        </a>
        <ul class="dropdown-menu border-0 shadow p-2 rounded-4" style="min-width: 260px; font-size: 15px;">
            <?php
            $navCategories = require __DIR__ . '/category_buttons.php';
            foreach ($navCategories as $category) {
                $categoryUrl = 'shop.php?category=' . urlencode($category['id']);
                echo '<li>' .
                     '<a class="dropdown-item py-2 px-3 mb-1 rounded text-dark hover-bg-light d-flex align-items-center fw-medium" href="' . $categoryUrl . '">' .
                     '<span class="me-3 text-center" style="width: 24px; font-size: 1.2rem;">' . htmlspecialchars($category['icon']) . '</span>' .
                     htmlspecialchars($category['name']) .
                     '</a>' .
                     '</li>';
            }
            ?>
        </ul>
    </div>
                    
                    <a href="shop.php" class="text-dark text-decoration-none nav-link-custom">Sản phẩm</a>
                    <a href="#" class="text-danger fw-semibold text-decoration-none nav-link-custom"><i class="bi bi-lightning-charge-fill"></i> Flash Sale</a>
                </nav>
            </div>
            
            <div class="d-flex align-items-center gap-3 gap-md-4">
                
                <form action="search.php" method="GET" class="d-none d-md-block position-relative" style="width: 300px;">
                    <input type="text" name="q" class="form-control rounded-pill bg-light border-0 pe-5" placeholder="Tìm kiếm sản phẩm..." required>
                    <button type="submit" class="btn btn-primary position-absolute end-0 top-0 rounded-circle m-1 p-0 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="bi bi-search" style="font-size: 14px;"></i>
                    </button>
                </form>
                
                <div class="d-flex align-items-center gap-3">
                    <a href="favorites.php" class="text-dark fs-5 text-decoration-none d-none d-sm-block"><i class="bi bi-heart"></i></a>
                    
                    <a href="cart.php" class="text-dark fs-5 position-relative text-decoration-none me-2">
                        <i class="bi bi-cart3"></i>
                        <span id="cart-badge" class="position-absolute translate-middle badge rounded-pill bg-danger" style="top: 5px; right: -20px; font-size: 10px; <?= $cart_count == 0 ? 'display: none;' : '' ?>"><?php echo $cart_count; ?></span>
                    </a>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="index.php" class="btn btn-primary rounded-pill d-none d-sm-block px-3 fw-medium">
                            <i class="bi bi-person-fill me-1"></i> Tài khoản
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary rounded-pill d-none d-sm-block px-3 fw-medium">
                            <i class="bi bi-person me-1"></i> Đăng nhập
                        </a>
                    <?php endif; ?>

                    <button class="btn btn-light d-lg-none border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>


