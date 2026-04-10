# Web Bán Máy Tính

Website bán hàng máy tính trực tuyến sử dụng PHP thuần + MySQL theo mô hình MVC.

## Công nghệ

- PHP >= 8.1
- MySQL
- HTML, CSS, JavaScript, Bootstrap 5
- Composer
- Git + GitHub
- XAMPP

## Cách chạy project

1. Clone repo vào thư mục `htdocs`: `git clone https://github.com/cheesa2201/webbanhang.git`
2. Di chuyển vào thư mục dự án: `cd web_ban_hang_may_tinh`
3. Cài thư viện: `composer install`
4. Mở XAMPP, chạy Apache và MySQL
5. Tạo database tên `web_may_tinh`, import file `database_new.sql`
6. Kiểm tra cấu hình trong `config/database.php`
7. Truy cập `http://localhost/web_ban_hang_may_tinh/user/index.php`

## Cấu trúc chính

- `admin/` : Giao diện và xử lý trang quản trị
- `user/` : Giao diện khách hàng
- `controllers/` : Xử lý logic
- `models/` : Truy vấn database
- `config/` : Cấu hình hệ thống
- `assets/` : CSS, JS, hình ảnh
- `vendor/` : Thư viện Composer (không commit)

## Quy trình làm việc nhóm

1. Cập nhật code mới nhất: `git checkout develop` → `git pull origin develop`
2. Tạo nhánh riêng: `git checkout -b feature/ten-tinh-nang`
3. Sau khi xong: `git add .` → `git commit -m "mô tả"` → `git push origin feature/ten-tinh-nang`
4. Tạo Pull Request trên GitHub để Leader review và merge vào `develop`

## Thành viên

| Họ tên | Vai trò |
|---|---|
| | |