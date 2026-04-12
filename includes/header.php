<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'TechShop' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FAFAFA; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25); border-color: #2563EB; }
        .input-group-text { background-color: transparent; border-right: none; color: #9ca3af; }
        .form-control.border-start-0 { border-left: none; padding-left: 0; }
        .form-control.border-end-0 { border-right: none; }
        .toggle-password { cursor: pointer; border-left: none; background-color: transparent; }
        .btn-custom { background-color: #2563EB; border-color: #2563EB; color: white; border-radius: 0.5rem; transition: background-color 0.2s; }
        .btn-custom:hover { background-color: #1D4ED8; border-color: #1D4ED8; color: white; }
        .card-custom { border: none; border-radius: 1rem; box-shadow: 0 10px 20px -2px rgba(0,0,0,0.04), 0 2px 15px -3px rgba(0,0,0,0.07); padding: 2rem; }
        .text-custom { color: #2563EB; }
        .logo-box { width: 40px; height: 40px; background-color: #1D4ED8; border-radius: 0.375rem; color: white; font-weight: bold; font-size: 1.25rem; display: inline-flex; align-items: center; justify-content: center; }
        .fs-sm { font-size: 0.875rem; }
        .form-label { font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem; }
        .demo-box { background-color: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; border-radius: 0.75rem; }
    </style>
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center p-3">

    <div class="w-100" style="max-width: 450px;">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <div class="logo-box">T</div>
                <h1 class="h4 fw-bold text-dark mb-0">TechShop</h1>
            </div>
            <p class="text-muted fs-sm"><?= isset($page_desc) ? htmlspecialchars($page_desc) : 'Tạo tài khoản để bắt đầu mua sắm' ?></p>
        </div>