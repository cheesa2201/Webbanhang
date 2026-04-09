<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ho_ten = $_POST['ho_ten'] ?? '';
    $email = $_POST['email'] ?? '';
    $so_dien_thoai = $_POST['so_dien_thoai'] ?? '';
    $mat_khau = $_POST['mat_khau'] ?? '';
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';
    $dieu_khoan = isset($_POST['dieu_khoan']);

    if (!$dieu_khoan) {
        $error_message = "Vui lòng đồng ý với Điều khoản sử dụng.";
    } elseif ($mat_khau !== $xac_nhan_mat_khau) {
        $error_message = "Mật khẩu xác nhận không khớp.";
    } elseif (strlen($mat_khau) < 6) {
        $error_message = "Mật khẩu phải có ít nhất 6 ký tự.";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            $userModel = new User($db);
            
            // Vai trò 2 thường là Customer (Khách hàng)
            $data = [
                'ho_ten' => $ho_ten,
                'email' => $email,
                'so_dien_thoai' => $so_dien_thoai,
                'mat_khau' => $mat_khau,
                'id_vai_tro' => 2,
                'dia_chi' => ''
            ];

            $result = $userModel->create($data);

            if ($result['success']) {
                $user_id = $result['id'];
                
                // --- TẠO Session ---
                if ($result['success']) {
                    $user_id = $result['id'];

                    // Lưu session (auto login)
                    $_SESSION['user'] = [
                        'id' => $user_id,
                        'email' => $email,
                        'ho_ten' => $ho_ten,
                        'vai_tro' => 2
                    ];

                    header("Location: index.php");
                    exit();
                }

                $success_message = "Đăng ký thành công! Đang chuyển hướng...";
                // Redirect sau 2s
                echo "<meta http-equiv='refresh' content='2;url=index.php'>";
            } else {
                $error_message = $result['message'];
            }
        } else {
            $error_message = "Không thể kết nối đến cơ sở dữ liệu.";
        }
    }
}
?>
<?php 
$page_title = 'Đăng ký - TechShop';
$page_desc = 'Tạo tài khoản để bắt đầu mua sắm';
require_once __DIR__ . '/../includes/auth_header.php'; 
?>

<!-- Form Container -->
<div class="card card-custom bg-white">
    <h2 class="h5 fw-bold text-center text-dark mb-4">Đăng ký tài khoản</h2>

    <?php if(!empty($error_message)): ?>
        <div class="alert alert-danger fs-sm py-2 px-3 border-0 bg-danger bg-opacity-10 text-danger fw-medium" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if(!empty($success_message)): ?>
        <div class="alert alert-success fs-sm py-2 px-3 border-0 bg-success bg-opacity-10 text-success fw-medium" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        
        <!-- Họ và tên -->
        <div class="mb-3">
            <label class="form-label">Họ và tên *</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-person"></i></span>
                <input type="text" name="ho_ten" required value="<?php echo htmlspecialchars($_POST['ho_ten'] ?? ''); ?>"
                       class="form-control border-start-0 py-2 fs-sm" 
                       placeholder="Nhập Họ và Tên">
            </div>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email *</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       class="form-control border-start-0 py-2 fs-sm" 
                       placeholder="email@gmail.com">
            </div>
        </div>

        <!-- Số điện thoại -->
        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-telephone"></i></span>
                <input type="tel" name="so_dien_thoai" value="<?php echo htmlspecialchars($_POST['so_dien_thoai'] ?? ''); ?>"
                       class="form-control border-start-0 py-2 fs-sm" 
                       placeholder="Nhập Số Điện Thoại">
            </div>
        </div>

        <!-- Mật khẩu -->
        <div class="mb-3">
            <label class="form-label">Mật khẩu *</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-lock"></i></span>
                <input type="password" name="mat_khau" id="mat_khau_reg" required 
                       class="form-control border-start-0 border-end-0 py-2 fs-sm" 
                       placeholder="Nhập Mật Khẩu">
                <span class="input-group-text toggle-password border-start-0" data-target="#mat_khau_reg">
                    <i class="bi bi-eye-slash text-muted"></i>
                </span>
            </div>
        </div>

        <!-- Xác nhận mật khẩu -->
        <div class="mb-3">
            <label class="form-label">Xác nhận mật khẩu *</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-shield-lock"></i></span>
                <input type="password" name="xac_nhan_mat_khau" id="xac_nhan_mat_khau_reg" required 
                       class="form-control border-start-0 border-end-0 py-2 fs-sm" 
                       placeholder="Nhập lại mật khẩu">
                <span class="input-group-text toggle-password border-start-0" data-target="#xac_nhan_mat_khau_reg">
                    <i class="bi bi-eye-slash text-muted"></i>
                </span>
            </div>
        </div>

        <!-- Điều khoản -->
        <div class="mb-4 d-flex align-items-start">
            <div class="form-check">
                <input class="form-check-input mt-1" type="checkbox" name="dieu_khoan" id="terms" required style="border-color: #d1d5db;">
                <label class="form-check-label ms-1" for="terms" style="font-size: 12px; color: #4b5563;">
                    Tôi đồng ý với <a href="#" class="text-custom text-decoration-none fw-medium">Điều khoản sử dụng</a> và <a href="#" class="text-custom text-decoration-none fw-medium">Chính sách bảo mật</a>
                </label>
            </div>
        </div>

        <!-- Nút Submit -->
        <button type="submit" class="btn btn-custom w-100 py-2 fw-medium fs-sm">
            Đăng ký
        </button>
    </form>
    
    <div class="mt-4 text-center fs-sm text-muted">
        Đã có tài khoản? <a href="login.php" class="text-custom text-decoration-none fw-semibold">Đăng nhập ngay</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>
