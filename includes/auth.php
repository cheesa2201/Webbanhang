<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Kiểm tra đã đăng nhập
function isLoggedIn(): bool
{
    return isset($_SESSION['id_nguoi_dung']) 
        && !empty($_SESSION['id_nguoi_dung']);
}

//Bắt buộc đăng nhập
function requireLogin(string $login_url = 'user/login.php'): void
{
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        header('Location: ' . $login_url);
        exit();
    }
}

//Lấy user hiện tại
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id_nguoi_dung' => $_SESSION['id_nguoi_dung'] ?? null,
        'id_vai_tro' => $_SESSION['id_vai_tro'] ?? null,
        'ten_vai_tro' => $_SESSION['ten_vai_tro'] ?? null,
        'ho_ten' => $_SESSION['ho_ten'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'so_dien_thoai' => $_SESSION['so_dien_thoai'] ?? null,
        'trang_thai' => $_SESSION['trang_thai'] ?? null,
    ];
}

// Tạo session sau login
function createLoginSession(array $nguoi_dung): void
{
    session_regenerate_id(true);
    $_SESSION['id_nguoi_dung'] = $nguoi_dung['id_nguoi_dung'];
    $_SESSION['id_vai_tro'] = $nguoi_dung['id_vai_tro'];
    $_SESSION['ten_vai_tro'] = strtolower($nguoi_dung['ten_vai_tro'] ?? '');
    $_SESSION['ho_ten'] = $nguoi_dung['ho_ten'];
    $_SESSION['email'] = $nguoi_dung['email'];
    $_SESSION['so_dien_thoai'] = $nguoi_dung['so_dien_thoai'] ?? null;
   $_SESSION['trang_thai'] = $nguoi_dung['trang_thai'] ?? 'hoat_dong';
}

//Logout
function logout(string $redirect = 'user/login.php'): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
    header('Location: ' . $redirect);
    exit();
}