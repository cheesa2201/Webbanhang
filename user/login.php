<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Nếu đã đăng nhập thì về trang chủ
if (isLoggedIn()) {
    redirect('user/shop.php');
}

if (is_post()) {
    $auth = new AuthController(db_connect());
    $auth->login();
}
?>

<?php 
$page_title = 'Đăng nhập - TechShop';
$page_desc = 'Đăng nhập để tiếp tục mua sắm';
require_once __DIR__ . '/../includes/auth_header.php';
?>

<div class="card card-custom bg-white">
    <h2 class="h5 fw-bold text-center text-dark mb-4">Đăng nhập</h2>

    <div class="demo-box p-3 mb-4 fs-sm">
        <p class="fw-semibold mb-1">Tài khoản demo:</p>
        <div class="d-flex align-items-center gap-1 mb-1">
            <span><i class="bi bi-person-fill"></i> Khách hàng: an.le@gmail.com / 123456</span>
        </div>
        <div class="d-flex align-items-center gap-1">
            <span><i class="bi bi-key-fill text-warning"></i> Admin: admin@webbanhang.com / 123456</span>
        </div>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger fs-sm py-2 px-3 border-0 bg-danger bg-opacity-10 text-danger fw-medium" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success fs-sm py-2 px-3 border-0 bg-success bg-opacity-10 text-success fw-medium" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-envelope"></i></span>
                <input
                    type="email"
                    name="email"
                    required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    class="form-control border-start-0 py-2 fs-sm"
                    placeholder="email@gmail.com"
                >
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Mật khẩu</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-lock"></i></span>
                <input
                    type="password"
                    name="mat_khau"
                    id="mat_khau_login"
                    required
                    class="form-control border-start-0 border-end-0 py-2 fs-sm"
                    placeholder="Mật khẩu"
                >
                <span class="input-group-text toggle-password border-start-0" data-target="#mat_khau_login">
                    <i class="bi bi-eye-slash text-muted"></i>
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check m-0">
                <input class="form-check-input" type="checkbox" name="ghi_nho" id="remember_me" style="border-color: #d1d5db;">
                <label class="form-check-label ms-1 fw-medium" for="remember_me" style="font-size: 0.875rem; color: #4b5563;">
                    Ghi nhớ đăng nhập
                </label>
            </div>
            <a href="#" class="text-custom text-decoration-none fw-medium fs-sm">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn btn-custom w-100 py-2 fw-medium fs-sm">
            Đăng nhập
        </button>
    </form>

    <div class="mt-4 text-center fs-sm text-muted">
        Chưa có tài khoản? <a href="register.php" class="text-custom text-decoration-none fw-semibold">Đăng ký ngay</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>