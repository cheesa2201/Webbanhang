<?php
declare(strict_types=1);

function is_logged_in(): bool
{
    return !empty($_SESSION['id_nguoi_dung']);
}

function current_user(): ?array
{
    return is_logged_in() ? [
        'id_nguoi_dung' => $_SESSION['id_nguoi_dung'],
        'id_vai_tro' => $_SESSION['id_vai_tro'] ?? null,
        'ten_vai_tro' => $_SESSION['ten_vai_tro'] ?? null,
        'ho_ten' => $_SESSION['ho_ten'] ?? null,
        'email' => $_SESSION['email'] ?? null,
    ] : null;
}

function create_login_session(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['id_nguoi_dung'] = (int)$user['id_nguoi_dung'];
    $_SESSION['id_vai_tro'] = (int)$user['id_vai_tro'];
    $_SESSION['ten_vai_tro'] = strtolower((string)$user['ten_vai_tro']);
    $_SESSION['ho_ten'] = (string)$user['ho_ten'];
    $_SESSION['email'] = (string)$user['email'];
    $_SESSION['so_dien_thoai'] = $user['so_dien_thoai'] ?? null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        redirect('login.php');
    }
}

function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['ten_vai_tro'] ?? '') === 'admin';
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die('Bạn không có quyền truy cập khu vực admin.');
    }
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
    }
    session_destroy();
}
