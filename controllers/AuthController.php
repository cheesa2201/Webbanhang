<?php
class AuthController 
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    // ======================
    // REGISTER
    // ======================
    public function register() {

        if ($_SERVER["REQUEST_METHOD"] !== "POST")
        {
            echo "Invalid request";
            return;
        }

        $data = [
            "ho_ten" => trim($_POST["ho_ten"] ?? ""),
            "email" => trim($_POST["email"] ?? ""),
            "so_dien_thoai" => trim($_POST["so_dien_thoai"] ?? ""),
            "mat_khau" => $_POST["mat_khau"] ?? "",
            "dia_chi" => trim($_POST["dia_chi"] ?? ""),
            "id_vai_tro" => 2
        ];

        if (!$data["email"] || !$data["mat_khau"])
        {
            echo "Thiếu email hoặc mật khẩu";
            return;
        }

        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
        {
            echo "Email không hợp lệ";
            return;
        }

        if ($this->userModel->existsByEmail($data["email"]))
        {
            echo "Email đã tồn tại";
            return;
        }

        // hash password
        $data["mat_khau"] = password_hash($data["mat_khau"], PASSWORD_DEFAULT);

        $result = $this->userModel->create($data);

        echo $result["message"];
    }


    // ======================
    // LOGIN
    // ======================
    public function login() {

        session_start();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") 
        {
            echo "Invalid request";
            return;
        }

        $email = trim($_POST["email"] ?? "");
        $password = $_POST["mat_khau"] ?? "";

        if (!$email || !$password) 
        {
            echo "Thiếu email hoặc mật khẩu";
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            echo "Sai tài khoản hoặc mật khẩu!";
            return;
        }

        if (!password_verify($password, $user["mat_khau"])) 
        {
            echo "Sai tài khoản hoặc mật khẩu!";
            return;
        }

        $_SESSION["user"] = [
            "id" => $user["id_nguoi_dung"],
            "ho_ten" => $user["ho_ten"],
            "email" => $user["email"],
            "role" => $user["id_vai_tro"]
        ];

        echo "Login thành công!";
    }


    // ======================
    // LOGOUT
    // ======================
    public function logout() 
    {
        session_start();
        session_unset();
        session_destroy();

        echo "Đã logout";
    }
}
