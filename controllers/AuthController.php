<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/User.php';

class AuthController 
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function login() 
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            redirect('user/login.php');
        }

        $email = trim($_POST["email"] ?? "");
        $password = $_POST["mat_khau"] ?? "";

        if (!$email || !$password) {
            $_SESSION['error'] = "Thiếu email hoặc mật khẩu";
            redirect('user/login.php');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user["mat_khau"])) {
            $_SESSION['error'] = "Sai tài khoản hoặc mật khẩu!";
            redirect('user/login.php');
        }

        // ✅ TẠO SESSION
        createLoginSession([
            'id_nguoi_dung' => $user['id_nguoi_dung'],
            'id_vai_tro'    => $user['id_vai_tro'],
            'ten_vai_tro'   => $user['ten_vai_tro'] ?? '',
            'ho_ten'        => $user['ho_ten'],
            'email'         => $user['email'],
            'so_dien_thoai' => $user['so_dien_thoai'] ?? null,
            'trang_thai'    => $user['trang_thai'] ?? 'hoat_dong',
        ]);

        // ✅ QUAN TRỌNG: redirect
        redirect('index.php');
    }

    public function logout() 
    {
        logout('user/login.php');
    }
}