<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../controllers/AdminUserController.php';

$controller = new AdminUserController(db_connect());
$controller->handlePost();

$data = $controller->indexData();
$users = $data['users'];
$keyword = $data['keyword'];
$success = $data['success'];
$error = $data['error'];

$page_title = 'Người dùng';
require __DIR__ . '/_layout_start.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="mb-1">Quản lý người dùng</h1>
        <p class="text-muted mb-0">Quản lý vai trò và trạng thái tài khoản.</p>
    </div>
</div>

<?php if ($success): ?><div class="alert alert-success rounded-4"><?= h($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger rounded-4"><?= h($error) ?></div><?php endif; ?>

<div class="bg-white p-4 rounded-4 shadow-sm mb-4">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-9">
            <label class="form-label">Tìm kiếm</label>
            <input name="q" value="<?= h($keyword) ?>" class="form-control" placeholder="Tên, email, role...">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">
                <i class="bi bi-search me-2"></i>Lọc
            </button>
        </div>
    </form>
</div>

<div class="bg-white p-4 rounded-4 shadow-sm table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Người dùng</th>
                <th>Role</th>
                <th>Trạng thái</th>
                <th class="text-end">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <div class="fw-800"><?= h($u['ho_ten'] ?? '') ?></div>
                        <div class="text-muted small"><?= h($u['email']) ?></div>
                    </td>
                    <td><span class="badge rounded-pill bg-primary"><?= h($u['ten_vai_tro']) ?></span></td>
                    <td>
                        <span class="badge rounded-pill bg-<?= $u['trang_thai'] === 'hoat_dong' ? 'success' : 'danger' ?>">
                            <?= h($u['trang_thai']) ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <?php if ((int)$u['id_nguoi_dung'] !== (int)$_SESSION['id_nguoi_dung']): ?>
                            <form method="post" class="d-inline-flex gap-2 align-items-center">
                                <input type="hidden" name="id_nguoi_dung" value="<?= (int)$u['id_nguoi_dung'] ?>">
                                <select name="id_vai_tro" class="form-select form-select-sm" style="width:120px">
                                    <option value="1" <?= (int)$u['id_vai_tro'] === 1 ? 'selected' : '' ?>>Admin</option>
                                    <option value="2" <?= (int)$u['id_vai_tro'] === 2 ? 'selected' : '' ?>>Customer</option>
                                </select>
                                <select name="trang_thai" class="form-select form-select-sm" style="width:130px">
                                    <option value="hoat_dong" <?= $u['trang_thai'] === 'hoat_dong' ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="khoa" <?= $u['trang_thai'] === 'khoa' ? 'selected' : '' ?>>Khóa</option>
                                </select>
                                <button class="btn btn-sm btn-primary">Lưu</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted small">Tài khoản hiện tại</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted py-5">Không có người dùng phù hợp.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/_layout_end.php'; ?>
