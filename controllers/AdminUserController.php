<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';

class AdminUserController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function handlePost(): void
    {
        require_admin();

        if (!is_post()) {
            return;
        }

        $id = (int)($_POST['id_nguoi_dung'] ?? 0);

        if ($id === (int)$_SESSION['id_nguoi_dung']) {
            $_SESSION['error'] = 'Bạn không thể tự sửa quyền/trạng thái của chính mình.';
            redirect('admin/users.php');
        }

        (new User($this->pdo))->updateRoleAndStatus(
            $id,
            (int)($_POST['id_vai_tro'] ?? 2),
            (string)($_POST['trang_thai'] ?? 'hoat_dong')
        );

        $_SESSION['success'] = 'Đã cập nhật người dùng.';
        redirect('admin/users.php');
    }

    public function indexData(): array
    {
        require_admin();

        $keyword = trim((string)($_GET['q'] ?? ''));
        $users = (new User($this->pdo))->allUsers();

        if ($keyword !== '') {
            $kw = mb_strtolower($keyword);
            $users = array_values(array_filter($users, function ($u) use ($kw) {
                return str_contains(mb_strtolower((string)($u['ho_ten'] ?? '')), $kw)
                    || str_contains(mb_strtolower((string)($u['email'] ?? '')), $kw)
                    || str_contains(mb_strtolower((string)($u['ten_vai_tro'] ?? '')), $kw);
            }));
        }

        return [
            'users' => $users,
            'keyword' => $keyword,
            'success' => flash('success'),
            'error' => flash('error'),
        ];
    }
}
