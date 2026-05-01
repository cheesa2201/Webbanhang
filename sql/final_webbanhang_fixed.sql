-- final_webbanhang_fixed.sql
-- Import 1 file này trong phpMyAdmin là đủ.

DROP DATABASE IF EXISTS webbanhang;
CREATE DATABASE webbanhang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webbanhang;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS van_chuyen,thanh_toan,chi_tiet_don_hang,don_hang,san_pham_yeu_thich,chi_tiet_gio_hang,gio_hang,san_pham_khuyen_mai,khuyen_mai,san_pham,thuong_hieu,danh_muc,phuong_thuc_thanh_toan,nguoi_dung,vai_tro;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE vai_tro(id_vai_tro INT AUTO_INCREMENT PRIMARY KEY,ten_vai_tro VARCHAR(50) UNIQUE NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE nguoi_dung(id_nguoi_dung INT AUTO_INCREMENT PRIMARY KEY,id_vai_tro INT NOT NULL,ho_ten VARCHAR(150) NOT NULL,email VARCHAR(150) UNIQUE NOT NULL,so_dien_thoai VARCHAR(20),mat_khau VARCHAR(255) NOT NULL,dia_chi TEXT,trang_thai ENUM('hoat_dong','khoa') DEFAULT 'hoat_dong',provider VARCHAR(30) DEFAULT 'local',google_id VARCHAR(255) UNIQUE,avatar_url VARCHAR(500),ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(id_vai_tro) REFERENCES vai_tro(id_vai_tro)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE danh_muc(id_danh_muc INT AUTO_INCREMENT PRIMARY KEY,ten_danh_muc VARCHAR(150) NOT NULL,trang_thai ENUM('hien','an') DEFAULT 'hien') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE thuong_hieu(id_thuong_hieu INT AUTO_INCREMENT PRIMARY KEY,ten_thuong_hieu VARCHAR(150) NOT NULL,trang_thai ENUM('hien','an') DEFAULT 'hien') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE san_pham(id_san_pham INT AUTO_INCREMENT PRIMARY KEY,id_danh_muc INT NOT NULL,id_thuong_hieu INT NOT NULL,ten_san_pham VARCHAR(255) NOT NULL,ma_san_pham VARCHAR(80) UNIQUE NOT NULL,gia DECIMAL(15,2) NOT NULL,hinh_anh_chinh VARCHAR(255),mo_ta_ngan TEXT,mo_ta_chi_tiet LONGTEXT,so_luong_ton INT DEFAULT 0,trang_thai ENUM('dang_ban','het_hang','an') DEFAULT 'dang_ban',ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(id_danh_muc) REFERENCES danh_muc(id_danh_muc),FOREIGN KEY(id_thuong_hieu) REFERENCES thuong_hieu(id_thuong_hieu)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE gio_hang(id_gio_hang INT AUTO_INCREMENT PRIMARY KEY,id_nguoi_dung INT UNIQUE NOT NULL,ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(id_nguoi_dung) REFERENCES nguoi_dung(id_nguoi_dung) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE chi_tiet_gio_hang(id_gio_hang INT NOT NULL,id_san_pham INT NOT NULL,so_luong INT NOT NULL DEFAULT 1,don_gia DECIMAL(15,2) NOT NULL,PRIMARY KEY(id_gio_hang,id_san_pham),FOREIGN KEY(id_gio_hang) REFERENCES gio_hang(id_gio_hang) ON DELETE CASCADE,FOREIGN KEY(id_san_pham) REFERENCES san_pham(id_san_pham)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE san_pham_yeu_thich(id_nguoi_dung INT NOT NULL,id_san_pham INT NOT NULL,ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(id_nguoi_dung,id_san_pham),FOREIGN KEY(id_nguoi_dung) REFERENCES nguoi_dung(id_nguoi_dung) ON DELETE CASCADE,FOREIGN KEY(id_san_pham) REFERENCES san_pham(id_san_pham) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE phuong_thuc_thanh_toan(id_phuong_thuc INT AUTO_INCREMENT PRIMARY KEY,ten_phuong_thuc VARCHAR(150) NOT NULL,mo_ta TEXT,trang_thai ENUM('hien','an') DEFAULT 'hien') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE don_hang(id_don_hang INT AUTO_INCREMENT PRIMARY KEY,id_nguoi_dung INT NOT NULL,id_phuong_thuc INT NOT NULL,tong_tien DECIMAL(15,2) NOT NULL,ten_nguoi_nhan VARCHAR(150) NOT NULL,so_dien_thoai_nhan VARCHAR(20) NOT NULL,dia_chi_giao_hang TEXT NOT NULL,ghi_chu TEXT,trang_thai_don_hang ENUM('cho_xac_nhan','da_xac_nhan','dang_giao','da_giao','da_huy') DEFAULT 'cho_xac_nhan',ngay_dat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(id_nguoi_dung) REFERENCES nguoi_dung(id_nguoi_dung),FOREIGN KEY(id_phuong_thuc) REFERENCES phuong_thuc_thanh_toan(id_phuong_thuc)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE chi_tiet_don_hang(id_chi_tiet INT AUTO_INCREMENT PRIMARY KEY,id_don_hang INT NOT NULL,id_san_pham INT NOT NULL,so_luong INT NOT NULL,don_gia DECIMAL(15,2) NOT NULL,thanh_tien DECIMAL(15,2) NOT NULL,FOREIGN KEY(id_don_hang) REFERENCES don_hang(id_don_hang) ON DELETE CASCADE,FOREIGN KEY(id_san_pham) REFERENCES san_pham(id_san_pham)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE thanh_toan(id_thanh_toan INT AUTO_INCREMENT PRIMARY KEY,id_don_hang INT NOT NULL,id_phuong_thuc INT NOT NULL,so_tien DECIMAL(15,2) NOT NULL,trang_thai_thanh_toan ENUM('chua_thanh_toan','da_thanh_toan','that_bai') DEFAULT 'chua_thanh_toan',ngay_thanh_toan TIMESTAMP NULL,FOREIGN KEY(id_don_hang) REFERENCES don_hang(id_don_hang) ON DELETE CASCADE,FOREIGN KEY(id_phuong_thuc) REFERENCES phuong_thuc_thanh_toan(id_phuong_thuc)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE van_chuyen(id_van_chuyen INT AUTO_INCREMENT PRIMARY KEY,id_don_hang INT NOT NULL,don_vi_van_chuyen VARCHAR(150),phi_van_chuyen DECIMAL(15,2) DEFAULT 0,ma_van_don VARCHAR(150),trang_thai_giao_hang ENUM('chuan_bi_hang','dang_giao','da_giao','that_bai') DEFAULT 'chuan_bi_hang',FOREIGN KEY(id_don_hang) REFERENCES don_hang(id_don_hang) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE khuyen_mai(id_khuyen_mai INT AUTO_INCREMENT PRIMARY KEY,ten_khuyen_mai VARCHAR(150) NOT NULL,phan_tram_giam DECIMAL(5,2) NOT NULL,ngay_bat_dau DATETIME NOT NULL,ngay_ket_thuc DATETIME NOT NULL,trang_thai ENUM('dang_dien_ra','ket_thuc','an') DEFAULT 'dang_dien_ra') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE san_pham_khuyen_mai(id_san_pham INT NOT NULL,id_khuyen_mai INT NOT NULL,PRIMARY KEY(id_san_pham,id_khuyen_mai),FOREIGN KEY(id_san_pham) REFERENCES san_pham(id_san_pham) ON DELETE CASCADE,FOREIGN KEY(id_khuyen_mai) REFERENCES khuyen_mai(id_khuyen_mai) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO vai_tro VALUES(1,'admin'),(2,'customer');
INSERT INTO nguoi_dung(id_nguoi_dung,id_vai_tro,ho_ten,email,so_dien_thoai,mat_khau,dia_chi) VALUES
(1,1,'Admin TechShop','admin@techshop.vn','0900000001','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC.uEfjR7eu1WQTTQ0ae','TP.HCM'),
(2,2,'Nguyễn Văn A','customer@techshop.vn','0900000002','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC.uEfjR7eu1WQTTQ0ae','Quận 1, TP.HCM');
INSERT INTO danh_muc(id_danh_muc,ten_danh_muc) VALUES(1,'Laptop'),(2,'Màn hình'),(3,'Bàn phím'),(4,'Chuột'),(5,'SSD'),(6,'Card đồ họa');
INSERT INTO thuong_hieu(id_thuong_hieu,ten_thuong_hieu) VALUES(1,'Dell'),(2,'Asus'),(3,'Logitech'),(4,'Samsung'),(5,'MSI'),(6,'Kingston');
INSERT INTO san_pham(id_san_pham,id_danh_muc,id_thuong_hieu,ten_san_pham,ma_san_pham,gia,hinh_anh_chinh,mo_ta_ngan,mo_ta_chi_tiet,so_luong_ton) VALUES
(1,1,1,'Dell Inspiron 15','LAP-DELL-001',15990000,'assets/images/no-image.jpg','Laptop văn phòng ổn định','Phù hợp học tập, văn phòng',20),
(2,1,2,'Asus Vivobook 14','LAP-ASUS-001',13990000,'assets/images/no-image.jpg','Laptop mỏng nhẹ','Phù hợp sinh viên',18),
(3,2,4,'Samsung Monitor 24 inch','MON-SAM-001',3290000,'assets/images/no-image.jpg','Màn hình Full HD','Làm việc và giải trí',30),
(4,3,3,'Logitech K380','KEY-LOG-001',790000,'assets/images/no-image.jpg','Bàn phím bluetooth','Gọn nhẹ',40),
(5,4,3,'Logitech G102','MOU-LOG-001',390000,'assets/images/no-image.jpg','Chuột gaming','Phổ thông',55),
(6,5,6,'Kingston NV2 500GB','SSD-KIN-001',990000,'assets/images/no-image.jpg','SSD NVMe','Tốc độ cao',28),
(7,6,5,'MSI RTX 4060 Ventus','GPU-MSI-001',8290000,'assets/images/no-image.jpg','Card đồ họa RTX','Gaming 1080p',10);
INSERT INTO khuyen_mai VALUES(1,'Mega Sale',10,DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 30 DAY),'dang_dien_ra');
INSERT INTO san_pham_khuyen_mai VALUES(1,1),(3,1),(7,1);
INSERT INTO phuong_thuc_thanh_toan VALUES(1,'Thanh toán khi nhận hàng','Khách thanh toán trực tiếp khi nhận hàng','hien'),(2,'Chuyển khoản ngân hàng','Chuyển khoản trước khi giao hàng','hien');


-- ===== FIX ẢNH SẢN PHẨM ĐÚNG THEO 7 SẢN PHẨM TRONG FULL DATABASE =====
SET SQL_SAFE_UPDATES = 0;
UPDATE san_pham SET hinh_anh_chinh = 'https://res.cloudinary.com/daro9erbh/image/upload/c_pad,w_400,h_400,b_white/v1776581907/dell-inspiron-15-3530-i7-n5i7301w1-thumb-638754980409982405-600x600_oebsy3.jpg' WHERE id_san_pham = 1;
UPDATE san_pham SET hinh_anh_chinh = 'https://res.cloudinary.com/daro9erbh/image/upload/c_pad,w_400,h_400,b_white/v1776581997/maxresdefault_czcab1.jpg' WHERE id_san_pham = 2;
UPDATE san_pham SET hinh_anh_chinh = 'https://res.cloudinary.com/daro9erbh/image/upload/c_pad,w_400,h_400,b_white/v1776582168/samsung-s3-s31c-ls27c310eaexxv-27-inch-fhd-thumb-1-600x600_xavytv.jpg' WHERE id_san_pham = 3;
UPDATE san_pham SET hinh_anh_chinh = 'https://res.cloudinary.com/daro9erbh/image/upload/c_pad,w_400,h_400,b_white/v1776582187/logitech-g-pro-x-mechanical-gaming-keyboard_2-600x400_0472fe2fcf3640dd8836c1fed7377043_m5i5fq.jpg' WHERE id_san_pham = 4;
UPDATE san_pham SET hinh_anh_chinh = 'https://res.cloudinary.com/daro9erbh/image/upload/c_pad,w_400,h_400,b_white/v1776582301/chuot-gaming-logitech-g102-gen2-lightsync-den-1-750x500_dtp0kf.jpg' WHERE id_san_pham = 5;
UPDATE san_pham SET hinh_anh_chinh = 'https://res.cloudinary.com/daro9erbh/image/upload/c_pad,w_400,h_400,b_white/v1776582684/ktc-product-ssd-snv2s-250g-3-lg_134f63eaef554cb0984a5d322dc25cb5_8ef94ccd223940788600ff0acd438e2b_gu0fhk.png' WHERE id_san_pham = 6;
UPDATE san_pham SET hinh_anh_chinh = 'https://res.cloudinary.com/daro9erbh/image/upload/c_pad,w_400,h_400,b_white/v1776582501/vga-asus-dual-geforce-rtx-4060-oc-edition-8gb-gddr6-dual-rtx4060-o8g_cmhlft.jpg' WHERE id_san_pham = 7;
SET SQL_SAFE_UPDATES = 1;


-- ===== ADD PASSWORD RESET =====

CREATE TABLE IF NOT EXISTS dat_lai_mat_khau (
    id_token INT AUTO_INCREMENT PRIMARY KEY,
    id_nguoi_dung INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    het_han DATETIME NOT NULL,
    da_su_dung TINYINT(1) NOT NULL DEFAULT 0,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token_hash (token_hash),
    INDEX idx_user_expire (id_nguoi_dung, het_han),
    CONSTRAINT fk_reset_user
        FOREIGN KEY (id_nguoi_dung)
        REFERENCES nguoi_dung(id_nguoi_dung)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== KIỂM TRA SAU KHI IMPORT =====
SELECT id_san_pham, ten_san_pham, id_danh_muc, hinh_anh_chinh FROM san_pham ORDER BY id_san_pham;
