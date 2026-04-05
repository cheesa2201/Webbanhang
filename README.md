# Web Bán Máy Tính — PHP MVC

Website thương mại điện tử bán máy tính, xây dựng bằng PHP thuần theo mô hình MVC.

------------------------------------------

## Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| Backend | PHP >= 8.1 |
| Database | MySQL |
| Frontend | HTML, CSS, JavaScript, Bootstrap 5 |
| Thư viện | Composer |
| Môi trường | XAMPP |

------------------------------------------

## Cấu trúc thư mục

------------------------------------------
web_ban_hang_may_tinh/
├── admin/          # Giao diện & logic trang quản trị
├── user/           # Giao diện khách hàng
├── controllers/    # Điều phối logic giữa Model và View
├── models/         # Truy vấn cơ sở dữ liệu
├── includes/       # Header, Footer, Navbar dùng chung
├── config/
│   └── database.php
├── assets/         # CSS, JS, Hình ảnh
├── vendor/         # Thư viện Composer (không commit)
└── database_new.sql
```

------------------------------------------

## Cài đặt

**Yêu cầu:** Đã cài XAMPP và Composer.

**Bước 1 — Clone repo** vào thư mục `htdocs`:

```bash
git clone https://github.com/cheesa2201/webbanhang.git
cd web_ban_hang_may_tinh
```

**Bước 2 — Cài thư viện:**

```bash
composer install
```

**Bước 3 — Tạo database:**

- Truy cập `http://localhost/phpmyadmin/`
- Tạo database tên `web_may_tinh`
- Import file `database_new.sql`

**Bước 4 — Cấu hình kết nối** trong `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'web_may_tinh');
define('DB_USER', 'root');
define('DB_PASS', '');  
```

**Bước 5 — Truy cập:**

| | URL |
|---|---|
| Trang khách hàng | `http://localhost/web_ban_hang_may_tinh/user/index.php` |
| Trang quản trị | `http://localhost/web_ban_hang_may_tinh/admin/index.php` |

------------------------------------------

## Quy trình làm việc nhóm

**1. Cập nhật code mới nhất:**

```bash
git checkout develop
git pull origin develop
```

**2. Tạo nhánh riêng để làm việc:**

```bash
git checkout -b feature/ten-tinh-nang

```

**3. Commit và push:**

```bash
git add .
git commit -m "mô tả công việc đã làm"
git push origin feature/ten-tinh-nang
```

**4.** Vào GitHub tạo **Pull Request** để Leader review và merge vào nhánh `develop`.



