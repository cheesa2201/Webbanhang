USE web_ban_hang;
SET NAMES utf8mb4;

-- =========================================
-- DATA DEMO CHO WEBSITE BÁN THIẾT BỊ MÁY TÍNH
-- Chạy sau khi đã import schema.sql / database_new.sql
-- File này có thể chạy lại nhiều lần
-- =========================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_SAFE_UPDATES = 0;
DELETE FROM danh_gia;
DELETE FROM van_chuyen;
DELETE FROM thanh_toan;
DELETE FROM chi_tiet_don_hang;
DELETE FROM don_hang;
DELETE FROM san_pham_khuyen_mai;
DELETE FROM khuyen_mai;
DELETE FROM dia_chi_giao_hang;
DELETE FROM chi_tiet_gio_hang;
DELETE FROM gio_hang;
DELETE FROM thong_so_ky_thuat;
DELETE FROM hinh_anh_san_pham;
DELETE FROM san_pham;
DELETE FROM phuong_thuc_thanh_toan;
DELETE FROM nguoi_dung;
DELETE FROM thuong_hieu;
DELETE FROM danh_muc;
DELETE FROM vai_tro;

ALTER TABLE vai_tro AUTO_INCREMENT = 1;
ALTER TABLE nguoi_dung AUTO_INCREMENT = 1;
ALTER TABLE danh_muc AUTO_INCREMENT = 1;
ALTER TABLE thuong_hieu AUTO_INCREMENT = 1;
ALTER TABLE san_pham AUTO_INCREMENT = 1;
ALTER TABLE hinh_anh_san_pham AUTO_INCREMENT = 1;
ALTER TABLE thong_so_ky_thuat AUTO_INCREMENT = 1;
ALTER TABLE gio_hang AUTO_INCREMENT = 1;
ALTER TABLE phuong_thuc_thanh_toan AUTO_INCREMENT = 1;
ALTER TABLE don_hang AUTO_INCREMENT = 1;
ALTER TABLE thanh_toan AUTO_INCREMENT = 1;
ALTER TABLE danh_gia AUTO_INCREMENT = 1;
ALTER TABLE khuyen_mai AUTO_INCREMENT = 1;
ALTER TABLE van_chuyen AUTO_INCREMENT = 1;
ALTER TABLE dia_chi_giao_hang AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;
SET SQL_SAFE_UPDATES = 1;
-- =========================================
-- 1. VAI TRÒ
-- Chỉ còn 2 vai trò: admin và khach_hang
-- =========================================
INSERT INTO vai_tro (id_vai_tro, ten_vai_tro, mo_ta) VALUES
(1, 'admin', 'Quản trị toàn bộ hệ thống'),
(2, 'khach_hang', 'Người dùng mua sắm trên website');

-- =========================================
-- 2. NGƯỜI DÙNG
-- Lưu ý: mat_khau đang là dữ liệu demo
-- Chỉ còn admin và khach_hang
-- =========================================
INSERT INTO nguoi_dung (id_nguoi_dung, id_vai_tro, ho_ten, email, so_dien_thoai, mat_khau, dia_chi, trang_thai) VALUES
(1, 1, 'Nguyen Quoc Admin', 'admin@webbanhang.com', '0901000001', '123456', '1 Nguyen Hue, Quan 1, TP.HCM', 'hoat_dong'),
(2, 2, 'Tran Minh Khang', 'khang.tran@gmail.com', '0901000002', '123456', '12 Vo Van Tan, Quan 3, TP.HCM', 'hoat_dong'),
(3, 2, 'Le Hoang An', 'an.le@gmail.com', '0901000003', '123456', '45 Le Loi, Da Nang', 'hoat_dong'),
(4, 2, 'Pham Bao Chau', 'chau.pham@gmail.com', '0901000004', '123456', '102 Nguyen Trai, Ha Noi', 'hoat_dong'),
(5, 2, 'Vo Minh Dat', 'dat.vo@gmail.com', '0901000005', '123456', '20 Tran Phu, Hai Phong', 'hoat_dong'),
(6, 2, 'Do Thu Ha', 'ha.do@gmail.com', '0901000006', '123456', '11 Cach Mang Thang 8, TP.HCM', 'hoat_dong'),
(7, 2, 'Bui Gia Huy', 'huy.bui@gmail.com', '0901000007', '123456', '19 Phan Chu Trinh, Hue', 'hoat_dong'),
(8, 2, 'Nguyen Ngoc Linh', 'linh.nguyen@gmail.com', '0901000008', '123456', '88 Dien Bien Phu, Can Tho', 'hoat_dong'),
(9, 2, 'Tran Quynh Nhu', 'nhu.tran@gmail.com', '0901000009', '123456', '14 Hoang Van Thu, Da Lat', 'hoat_dong'),
(10, 2, 'Pham Tuan Kiet', 'kiet.pham@gmail.com', '0901000010', '123456', '77 Nguyen Van Cu, Nha Trang', 'hoat_dong');

-- =========================================
-- 3. DANH MỤC
-- =========================================
INSERT INTO danh_muc (id_danh_muc, ten_danh_muc, mo_ta, trang_thai) VALUES
(1, 'Laptop', 'Laptop học tập, văn phòng và gaming', 'hien'),
(2, 'Màn hình', 'Màn hình cho học tập, làm việc và gaming', 'hien'),
(3, 'Bàn phím', 'Bàn phím cơ, bàn phím văn phòng', 'hien'),
(4, 'Chuột', 'Chuột văn phòng và chuột gaming', 'hien'),
(5, 'CPU', 'Bộ vi xử lý cho PC', 'hien'),
(6, 'Card đồ họa', 'GPU cho nhu cầu gaming và đồ họa', 'hien'),
(7, 'Mainboard', 'Bo mạch chủ cho máy tính để bàn', 'hien'),
(8, 'SSD', 'Ổ cứng SSD NVMe và SATA', 'hien');

-- =========================================
-- 4. THƯƠNG HIỆU
-- =========================================
INSERT INTO thuong_hieu (id_thuong_hieu, ten_thuong_hieu, quoc_gia, mo_ta) VALUES
(1, 'Dell', 'USA', 'Thương hiệu laptop và màn hình phổ biến'),
(2, 'ASUS', 'Taiwan', 'Thương hiệu linh kiện và laptop đa dạng'),
(3, 'Lenovo', 'China', 'Laptop cho học tập, văn phòng và gaming'),
(4, 'HP', 'USA', 'Laptop và thiết bị văn phòng'),
(5, 'LG', 'South Korea', 'Màn hình và điện tử tiêu dùng'),
(6, 'Samsung', 'South Korea', 'Thiết bị điện tử và màn hình'),
(7, 'Logitech', 'Switzerland', 'Phụ kiện máy tính, chuột, bàn phím'),
(8, 'DareU', 'China', 'Phụ kiện gaming giá tốt'),
(9, 'Intel', 'USA', 'CPU và nền tảng máy tính'),
(10, 'AMD', 'USA', 'CPU hiệu năng cao cho desktop'),
(11, 'MSI', 'Taiwan', 'Laptop gaming và card đồ họa'),
(12, 'Kingston', 'USA', 'Bộ nhớ và SSD');

-- =========================================
-- 5. PHƯƠNG THỨC THANH TOÁN
-- =========================================
INSERT INTO phuong_thuc_thanh_toan (id_phuong_thuc, ten_phuong_thuc, mo_ta) VALUES
(1, 'COD', 'Thanh toán khi nhận hàng'),
(2, 'Chuyen khoan ngan hang', 'Thanh toán qua tài khoản ngân hàng'),
(3, 'Vi dien tu', 'Thanh toán qua ví điện tử');

-- =========================================
-- 6. SẢN PHẨM
-- =========================================
INSERT INTO san_pham (
    id_san_pham, id_danh_muc, id_thuong_hieu, ten_san_pham, ma_san_pham,
    gia, so_luong_ton, mo_ta_ngan, mo_ta_chi_tiet, hinh_anh_chinh, trang_thai
) VALUES
(1, 1, 1, 'Dell Inspiron 15 3530', 'LAP-DELL-001', 15990000, 15, 'Laptop 15.6 inch phù hợp học tập và văn phòng.', 'Dell Inspiron 15 3530 trang bị cấu hình ổn định, phù hợp cho nhu cầu học tập, làm việc văn phòng và giải trí cơ bản.', 'hinhanh/Dell Inspiron 15 3530.jpg', 'dang_ban'),
(2, 1, 2, 'ASUS Vivobook 15 X1504', 'LAP-ASUS-001', 16990000, 12, 'Laptop mỏng nhẹ với thiết kế hiện đại.', 'ASUS Vivobook 15 X1504 phù hợp cho sinh viên và nhân viên văn phòng nhờ màn hình lớn, hiệu năng ổn định và thiết kế gọn gàng.', 'hinhanh/ASUS Vivobook 15 X1504.jpg', 'dang_ban'),
(3, 1, 3, 'Lenovo LOQ 15IRH8', 'LAP-LENOVO-001', 24990000, 8, 'Laptop gaming tầm trung cho học tập và giải trí.', 'Lenovo LOQ 15IRH8 phù hợp cho người cần laptop vừa học tập vừa chơi game với hiệu năng tốt trong tầm giá.', 'hinhanh/Lenovo LOQ 15IRH8.jpg', 'dang_ban'),
(4, 1, 4, 'HP Pavilion 14', 'LAP-HP-001', 18490000, 10, 'Laptop 14 inch dành cho học tập và công việc linh hoạt.', 'HP Pavilion 14 có thiết kế nhỏ gọn, thời lượng pin tốt và hiệu năng đủ dùng cho môi trường học tập, văn phòng.', 'hinhanh/HP Pavilion 14.jpg', 'dang_ban'),
(5, 2, 5, 'LG UltraGear 24GN60R-B', 'MON-LG-001', 4190000, 18, 'Màn hình gaming 24 inch tần số quét cao.', 'Màn hình LG UltraGear 24GN60R-B hướng đến game thủ phổ thông với tần số quét 144Hz và thời gian phản hồi nhanh.', 'hinhanh/LG UltraGear 24GN60R-B.jpg', 'dang_ban'),
(6, 2, 6, 'Samsung Essential S3 27 inch', 'MON-SAM-001', 3590000, 20, 'Màn hình 27 inch phù hợp văn phòng và giải trí.', 'Samsung Essential S3 27 inch cho không gian hiển thị rộng, màu sắc dễ chịu và phù hợp làm việc hàng ngày.', 'hinhanh/Samsung Essential S3 27 inch.png', 'dang_ban'),
(7, 3, 7, 'Logitech G Pro Mechanical Keyboard', 'KEY-LOGI-001', 2490000, 9, 'Bàn phím cơ gọn gàng cho gaming.', 'Logitech G Pro Mechanical Keyboard có thiết kế tenkeyless, phù hợp cho game thủ cần không gian rê chuột lớn.', 'hinhanh/Logitech G Pro Mechanical Keyboard.jpg', 'dang_ban'),
(8, 3, 8, 'DareU EK75 Wireless', 'KEY-DAREU-001', 1290000, 14, 'Bàn phím cơ 75% kết nối không dây.', 'DareU EK75 Wireless cân bằng giữa tính thẩm mỹ, trải nghiệm gõ và mức giá dễ tiếp cận.', 'hinhanh/DareU EK75 Wireless.jpg', 'dang_ban'),
(9, 4, 7, 'Logitech G102 Lightsync', 'MOU-LOGI-001', 399000, 30, 'Chuột gaming giá tốt, cảm biến ổn định.', 'Logitech G102 Lightsync là mẫu chuột phổ thông được nhiều người lựa chọn nhờ cảm giác cầm tốt và hiệu năng ổn định.', 'hinhanh/Logitech G102 Lightsync.jpg', 'dang_ban'),
(10, 4, 2, 'ASUS TUF M3 Gen II', 'MOU-ASUS-001', 549000, 16, 'Chuột gaming thiết kế công thái học.', 'ASUS TUF M3 Gen II phù hợp cho nhu cầu gaming và sử dụng hàng ngày với mức giá hợp lý.', 'hinhanh/ASUS TUF M3 Gen II.jpg', 'dang_ban'),
(11, 5, 9, 'Intel Core i5-12400F', 'CPU-INTEL-001', 3890000, 11, 'CPU 6 nhân phù hợp chơi game và làm việc.', 'Intel Core i5-12400F là lựa chọn phổ biến cho PC gaming tầm trung với hiệu năng đơn nhân tốt.', 'hinhanh/Intel Core i5-12400F.jpg', 'dang_ban'),
(12, 5, 10, 'AMD Ryzen 5 5600', 'CPU-AMD-001', 3190000, 13, 'CPU 6 nhân 12 luồng hiệu năng ổn định.', 'AMD Ryzen 5 5600 phù hợp cho cấu hình gaming phổ thông và công việc đa nhiệm cơ bản.', 'hinhanh/AMD Ryzen 5 5600.jpg', 'dang_ban'),
(13, 6, 2, 'ASUS Dual GeForce RTX 4060 8GB', 'GPU-ASUS-001', 9490000, 7, 'Card đồ họa tầm trung cho game Full HD.', 'ASUS Dual GeForce RTX 4060 8GB phù hợp cho người dùng cần hiệu năng chơi game tốt ở độ phân giải Full HD.', 'hinhanh/ASUS Dual GeForce RTX 4060 8GB.jpg', 'dang_ban'),
(14, 6, 11, 'MSI GeForce RTX 3060 Ventus 2X', 'GPU-MSI-001', 7990000, 0, 'Card đồ họa phổ biến cho gaming và đồ họa.', 'MSI GeForce RTX 3060 Ventus 2X đáp ứng tốt nhu cầu chơi game và làm đồ họa ở mức khá.', 'hinhanh/MSI GeForce RTX 3060 Ventus 2X.jpg', 'het_hang'),
(15, 7, 2, 'ASUS PRIME B760M-A WIFI D4', 'MB-ASUS-001', 3590000, 10, 'Mainboard micro-ATX hỗ trợ Intel thế hệ mới.', 'ASUS PRIME B760M-A WIFI D4 là bo mạch chủ cân bằng giữa tính năng, độ ổn định và khả năng nâng cấp.', 'hinhanh/ASUS PRIME B760M-A WIFI D4.jpg', 'dang_ban'),
(16, 8, 12, 'Kingston NV2 1TB NVMe SSD', 'SSD-KING-001', 1590000, 25, 'SSD NVMe 1TB tốc độ cao.', 'Kingston NV2 1TB NVMe SSD giúp tăng tốc khởi động hệ điều hành và mở ứng dụng nhanh hơn đáng kể.', 'hinhanh/Kingston NV2 1TB NVMe SSD.jpg', 'dang_ban');

-- =========================================
-- 7. HÌNH ẢNH SẢN PHẨM
-- =========================================
INSERT INTO hinh_anh_san_pham (id_hinh_anh, id_san_pham, duong_dan_anh, la_anh_chinh) VALUES
(1, 1, 'hinhanh/Dell Inspiron 15 3530.jpg', TRUE),
(2, 1, 'hinhanh/Dell Inspiron 15 3530.jpg', FALSE),
(3, 2, 'hinhanh/ASUS Vivobook 15 X1504.jpg', TRUE),
(4, 2, 'hinhanh/ASUS Vivobook 15 X1504.jpg', FALSE),
(5, 3, 'hinhanh/Lenovo LOQ 15IRH8.jpg', TRUE),
(6, 3, 'hinhanh/Lenovo LOQ 15IRH8.jpg', FALSE),
(7, 4, 'hinhanh/HP Pavilion 14.jpg', TRUE),
(8, 4, 'hinhanh/HP Pavilion 14.jpg', FALSE),
(9, 5, 'hinhanh/LG UltraGear 24GN60R-B.jpg', TRUE),
(10, 5, 'hinhanh/LG UltraGear 24GN60R-B.jpg', FALSE),
(11, 6, 'hinhanh/Samsung Essential S3 27 inch.png', TRUE),
(12, 6, 'hinhanh/Samsung Essential S3 27 inch.png', FALSE),
(13, 7, 'hinhanh/Logitech G Pro Mechanical Keyboard.jpg', TRUE),
(14, 7, 'hinhanh/Logitech G Pro Mechanical Keyboard.jpg', FALSE),
(15, 8, 'hinhanh/DareU EK75 Wireless.jpg', TRUE),
(16, 8, 'hinhanh/DareU EK75 Wireless.jpg', FALSE),
(17, 9, 'hinhanh/Logitech G102 Lightsync.jpg', TRUE),
(18, 9, 'hinhanh/Logitech G102 Lightsync.jpg', FALSE),
(19, 10, 'hinhanh/ASUS TUF M3 Gen II.jpg', TRUE),
(20, 10, 'hinhanh/ASUS TUF M3 Gen II.jpg', FALSE),
(21, 11, 'hinhanh/Intel Core i5-12400F.jpg', TRUE),
(22, 11, 'hinhanh/Intel Core i5-12400F.jpg', FALSE),
(23, 12, 'hinhanh/AMD Ryzen 5 5600.jpg', TRUE),
(24, 12, 'hinhanh/AMD Ryzen 5 5600.jpg', FALSE),
(25, 13, 'hinhanh/ASUS Dual GeForce RTX 4060 8GB.jpg', TRUE),
(26, 13, 'hinhanh/ASUS Dual GeForce RTX 4060 8GB.jpg', FALSE),
(27, 14, 'hinhanh/MSI GeForce RTX 3060 Ventus 2X.jpg', TRUE),
(28, 14, 'hinhanh/MSI GeForce RTX 3060 Ventus 2X.jpg', FALSE),
(29, 15, 'hinhanh/ASUS PRIME B760M-A WIFI D4.jpg', TRUE),
(30, 15, 'hinhanh/ASUS PRIME B760M-A WIFI D4.jpg', FALSE),
(31, 16, 'hinhanh/Kingston NV2 1TB NVMe SSD.jpg', TRUE),
(32, 16, 'hinhanh/Kingston NV2 1TB NVMe SSD.jpg', FALSE);
-- =========================================
-- 8. THÔNG SỐ KỸ THUẬT
-- =========================================
INSERT INTO thong_so_ky_thuat (id_thong_so, id_san_pham, ten_thong_so, gia_tri_thong_so) VALUES
(1, 1, 'CPU', 'Intel Core i5'),
(2, 1, 'RAM', '8GB'),
(3, 1, 'Luu tru', '512GB SSD'),
(4, 1, 'Man hinh', '15.6 inch Full HD'),
(5, 1, 'He dieu hanh', 'Windows 11'),

(6, 2, 'CPU', 'Intel Core i5'),
(7, 2, 'RAM', '16GB'),
(8, 2, 'Luu tru', '512GB SSD'),
(9, 2, 'Man hinh', '15.6 inch Full HD'),
(10, 2, 'Khoi luong', '1.7kg'),

(11, 3, 'CPU', 'Intel Core i5'),
(12, 3, 'RAM', '16GB'),
(13, 3, 'Luu tru', '512GB SSD'),
(14, 3, 'GPU', 'RTX 4050'),
(15, 3, 'Man hinh', '15.6 inch 144Hz'),

(16, 4, 'CPU', 'Intel Core i5'),
(17, 4, 'RAM', '16GB'),
(18, 4, 'Luu tru', '512GB SSD'),
(19, 4, 'Man hinh', '14 inch Full HD'),
(20, 4, 'Pin', '41Wh'),

(21, 5, 'Kich thuoc', '24 inch'),
(22, 5, 'Do phan giai', '1920 x 1080'),
(23, 5, 'Tan so quet', '144Hz'),
(24, 5, 'Tam nen', 'IPS'),
(25, 5, 'Cong ket noi', 'HDMI, DisplayPort'),

(26, 6, 'Kich thuoc', '27 inch'),
(27, 6, 'Do phan giai', '1920 x 1080'),
(28, 6, 'Tan so quet', '75Hz'),
(29, 6, 'Tam nen', 'IPS'),
(30, 6, 'Cong ket noi', 'HDMI, VGA'),

(31, 7, 'Loai', 'Ban phim co TKL'),
(32, 7, 'Ket noi', 'Co day'),
(33, 7, 'Den nen', 'RGB'),
(34, 7, 'Switch', 'GX Blue'),
(35, 7, 'Chat lieu', 'Nhua cao cap'),

(36, 8, 'Loai', 'Ban phim co 75%'),
(37, 8, 'Ket noi', 'Bluetooth, 2.4G, USB-C'),
(38, 8, 'Den nen', 'RGB'),
(39, 8, 'Switch', 'Dream Switch'),
(40, 8, 'Pin', '3750mAh'),

(41, 9, 'Cam bien', '8000 DPI'),
(42, 9, 'Ket noi', 'USB'),
(43, 9, 'So nut', '6'),
(44, 9, 'Trong luong', '85g'),
(45, 9, 'Den', 'RGB'),

(46, 10, 'Cam bien', '8000 DPI'),
(47, 10, 'Ket noi', 'USB'),
(48, 10, 'So nut', '6'),
(49, 10, 'Trong luong', '59g'),
(50, 10, 'Do ben switch', '60 trieu lan nhan'),

(51, 11, 'So nhan', '6'),
(52, 11, 'So luong (thread)', '12'),
(53, 11, 'Xung nhip co ban', '2.5GHz'),
(54, 11, 'Socket', 'LGA1700'),
(55, 11, 'TDP', '65W'),

(56, 12, 'So nhan', '6'),
(57, 12, 'So luong (thread)', '12'),
(58, 12, 'Xung nhip co ban', '3.5GHz'),
(59, 12, 'Socket', 'AM4'),
(60, 12, 'TDP', '65W'),

(61, 13, 'Bo nho', '8GB GDDR6'),
(62, 13, 'Giao tiep', 'PCIe 4.0'),
(63, 13, 'Cong xuat hinh', 'HDMI, DisplayPort'),
(64, 13, 'Nguon de xuat', '550W'),
(65, 13, 'Tan nhiet', '2 quat'),

(66, 14, 'Bo nho', '12GB GDDR6'),
(67, 14, 'Giao tiep', 'PCIe 4.0'),
(68, 14, 'Cong xuat hinh', 'HDMI, DisplayPort'),
(69, 14, 'Nguon de xuat', '550W'),
(70, 14, 'Tan nhiet', '2 quat'),

(71, 15, 'Socket', 'LGA1700'),
(72, 15, 'Chipset', 'B760'),
(73, 15, 'Kich thuoc', 'mATX'),
(74, 15, 'RAM ho tro', 'DDR4'),
(75, 15, 'Ket noi', 'WiFi, LAN, USB, HDMI'),

(76, 16, 'Dung luong', '1TB'),
(77, 16, 'Chuan giao tiep', 'NVMe PCIe 4.0'),
(78, 16, 'Toc do doc', '3500MB/s'),
(79, 16, 'Toc do ghi', '2100MB/s'),
(80, 16, 'Form factor', 'M.2 2280');

-- =========================================
-- 9. GIỎ HÀNG
-- Chỉ tạo cho khách hàng
-- =========================================
INSERT INTO gio_hang (id_gio_hang, id_nguoi_dung) VALUES
(1, 3),
(2, 4),
(3, 5),
(4, 6),
(5, 7),
(6, 8),
(7, 9),
(8, 10);

-- =========================================
-- 10. CHI TIẾT GIỎ HÀNG
-- =========================================
INSERT INTO chi_tiet_gio_hang (id_gio_hang, id_san_pham, so_luong, don_gia) VALUES
(1, 9, 1, 399000),
(1, 16, 1, 1590000),
(2, 5, 1, 4190000),
(2, 10, 1, 549000),
(3, 11, 1, 3890000),
(3, 15, 1, 3590000),
(4, 8, 1, 1290000),
(4, 9, 2, 399000),
(5, 2, 1, 16990000),
(6, 6, 1, 3590000),
(6, 7, 1, 2490000),
(7, 12, 1, 3190000),
(7, 16, 1, 1590000),
(8, 1, 1, 15990000);

-- =========================================
-- 11. ĐỊA CHỈ GIAO HÀNG
-- =========================================
INSERT INTO dia_chi_giao_hang (id_dia_chi, id_nguoi_dung, ten_nguoi_nhan, so_dien_thoai_nhan, dia_chi, la_mac_dinh) VALUES
(1, 3, 'Le Hoang An', '0901000003', '45 Le Loi, Hai Chau, Da Nang', TRUE),
(2, 4, 'Pham Bao Chau', '0901000004', '102 Nguyen Trai, Thanh Xuan, Ha Noi', TRUE),
(3, 5, 'Vo Minh Dat', '0901000005', '20 Tran Phu, Ngo Quyen, Hai Phong', TRUE),
(4, 6, 'Do Thu Ha', '0901000006', '11 Cach Mang Thang 8, Quan 10, TP.HCM', TRUE),
(5, 7, 'Bui Gia Huy', '0901000007', '19 Phan Chu Trinh, TP Hue', TRUE),
(6, 8, 'Nguyen Ngoc Linh', '0901000008', '88 Dien Bien Phu, Ninh Kieu, Can Tho', TRUE),
(7, 9, 'Tran Quynh Nhu', '0901000009', '14 Hoang Van Thu, TP Da Lat', TRUE),
(8, 10, 'Pham Tuan Kiet', '0901000010', '77 Nguyen Van Cu, TP Nha Trang', TRUE);

-- =========================================
-- 12. KHUYẾN MÃI
-- =========================================
INSERT INTO khuyen_mai (id_khuyen_mai, ten_khuyen_mai, phan_tram_giam, ngay_bat_dau, ngay_ket_thuc, trang_thai) VALUES
(1, 'Sale khai truong laptop', 10.00, '2026-04-01', '2026-04-30', 'dang_dien_ra'),
(2, 'Giam gia phu kien thang 4', 15.00, '2026-04-10', '2026-04-25', 'dang_dien_ra'),
(3, 'Sale SSD cuoi thang', 8.00, '2026-04-20', '2026-05-05', 'sap_dien_ra'),
(4, 'Khuyen mai man hinh mua he', 12.00, '2026-05-10', '2026-05-31', 'sap_dien_ra'),
(5, 'Flash sale GPU thang 3', 5.00, '2026-03-01', '2026-03-15', 'ket_thuc');

INSERT INTO san_pham_khuyen_mai (id_san_pham, id_khuyen_mai) VALUES
(1, 1),
(2, 1),
(3, 1),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(16, 3),
(5, 4),
(6, 4),
(13, 5);

-- =========================================
-- 13. ĐƠN HÀNG
-- =========================================
INSERT INTO don_hang (
    id_don_hang, id_nguoi_dung, id_phuong_thuc, ngay_dat, tong_tien,
    ten_nguoi_nhan, so_dien_thoai_nhan, dia_chi_giao_hang, ghi_chu, trang_thai_don_hang
) VALUES
(1, 3, 1, '2026-04-05 09:15:00', 0, 'Le Hoang An', '0901000003', '45 Le Loi, Hai Chau, Da Nang', 'Giao gio hanh chinh', 'da_giao'),
(2, 4, 2, '2026-04-08 14:20:00', 0, 'Pham Bao Chau', '0901000004', '102 Nguyen Trai, Thanh Xuan, Ha Noi', 'Goi truoc khi giao', 'dang_giao'),
(3, 5, 1, '2026-04-10 10:30:00', 0, 'Vo Minh Dat', '0901000005', '20 Tran Phu, Ngo Quyen, Hai Phong', 'Khach can xuat hoa don', 'cho_xac_nhan'),
(4, 6, 3, '2026-04-12 19:05:00', 0, 'Do Thu Ha', '0901000006', '11 Cach Mang Thang 8, Quan 10, TP.HCM', 'Giao sau 18h', 'da_xac_nhan'),
(5, 7, 2, '2026-04-14 08:40:00', 0, 'Bui Gia Huy', '0901000007', '19 Phan Chu Trinh, TP Hue', 'Khong ghi chu', 'da_huy'),
(6, 8, 1, '2026-04-06 10:00:00', 0, 'Nguyen Ngoc Linh', '0901000008', '88 Dien Bien Phu, Ninh Kieu, Can Tho', NULL, 'da_giao'),
(7, 9, 3, '2026-04-07 15:30:00', 0, 'Tran Quynh Nhu', '0901000009', '14 Hoang Van Thu, TP Da Lat', NULL, 'da_giao'),
(8, 10, 2, '2026-04-08 09:00:00', 0, 'Pham Tuan Kiet', '0901000010', '77 Nguyen Van Cu, TP Nha Trang', NULL, 'da_giao');

-- =========================================
-- 14. CHI TIẾT ĐƠN HÀNG
-- Trigger sẽ tự cập nhật tong_tien cho đơn hàng
-- =========================================
INSERT INTO chi_tiet_don_hang (id_don_hang, id_san_pham, so_luong, don_gia, thanh_tien) VALUES
(1, 1, 1, 15990000, 15990000),
(1, 9, 1, 399000, 399000),

(2, 5, 1, 4190000, 4190000),
(2, 10, 1, 549000, 549000),

(3, 11, 1, 3890000, 3890000),
(3, 15, 1, 3590000, 3590000),
(3, 16, 1, 1590000, 1590000),

(4, 2, 1, 16990000, 16990000),
(4, 8, 1, 1290000, 1290000),

(5, 12, 1, 3190000, 3190000),
(5, 16, 1, 1590000, 1590000),

(6, 16, 1, 1590000, 1590000),

(7, 9, 1, 399000, 399000),

(8, 2, 1, 16990000, 16990000);

-- =========================================
-- 15. THANH TOÁN
-- =========================================
INSERT INTO thanh_toan (id_thanh_toan, id_don_hang, id_phuong_thuc, so_tien, trang_thai_thanh_toan, ngay_thanh_toan) VALUES
(1, 1, 1, 16389000, 'da_thanh_toan', '2026-04-07 16:30:00'),
(2, 2, 2, 4739000, 'chua_thanh_toan', NULL),
(3, 3, 1, 9070000, 'chua_thanh_toan', NULL),
(4, 4, 3, 18280000, 'da_thanh_toan', '2026-04-12 19:20:00'),
(5, 5, 2, 4780000, 'that_bai', NULL),
(6, 6, 1, 1590000, 'da_thanh_toan', '2026-04-08 14:00:00'),
(7, 7, 3, 399000, 'da_thanh_toan', '2026-04-09 16:00:00'),
(8, 8, 2, 16990000, 'da_thanh_toan', '2026-04-10 11:00:00');

-- =========================================
-- 16. VẬN CHUYỂN
-- =========================================
INSERT INTO van_chuyen (id_van_chuyen, id_don_hang, don_vi_van_chuyen, phi_van_chuyen, ma_van_don, trang_thai_giao_hang) VALUES
(1, 1, 'Giao Hang Nhanh', 30000, 'GHN000001', 'giao_thanh_cong'),
(2, 2, 'Giao Hang Tiet Kiem', 25000, 'GHTK000002', 'dang_giao'),
(3, 3, 'Viettel Post', 35000, 'VTP000003', 'chuan_bi_hang'),
(4, 4, 'J&T Express', 30000, 'JNT000004', 'chuan_bi_hang'),
(5, 5, 'Giao Hang Nhanh', 30000, 'GHN000005', 'giao_that_bai'),
(6, 6, 'Giao Hang Nhanh', 30000, 'GHN000006', 'giao_thanh_cong'),
(7, 7, 'Viettel Post', 35000, 'VTP000007', 'giao_thanh_cong'),
(8, 8, 'Giao Hang Tiet Kiem', 25000, 'GHTK000008', 'giao_thanh_cong');

-- =========================================
-- 17. ĐÁNH GIÁ
-- =========================================
INSERT INTO danh_gia (id_danh_gia, id_nguoi_dung, id_san_pham, so_sao, noi_dung, ngay_danh_gia) VALUES
(1, 3, 1, 5, 'Laptop dùng ổn định, phù hợp học tập và làm việc văn phòng.', '2026-04-08 10:00:00'),
(2, 4, 5, 5, 'Màn hình hiển thị đẹp, tần số quét mượt trong tầm giá.', '2026-04-09 11:15:00'),
(3, 5, 11, 4, 'CPU hiệu năng tốt, lắp máy chạy khá mượt.', '2026-04-11 09:45:00'),
(4, 6, 8, 4, 'Bàn phím gõ tốt, ngoại hình đẹp.', '2026-04-13 20:10:00'),
(5, 7, 12, 5, 'Ryzen 5 5600 rất đáng tiền cho bộ PC tầm trung.', '2026-04-14 12:30:00'),
(6, 8, 16, 5, 'SSD tốc độ ổn, cài Windows và game rất nhanh.', '2026-04-15 08:20:00'),
(7, 9, 9, 4, 'Chuột nhẹ, bấm tốt, phù hợp chơi game phổ thông.', '2026-04-15 16:05:00'),
(8, 10, 2, 5, 'Máy đẹp, mỏng nhẹ, dùng học online và làm bài rất ổn.', '2026-04-16 09:00:00');

-- =========================================
-- 18. CẬP NHẬT THỬ MỘT SẢN PHẨM HẾT HÀNG ĐỂ ĐẢM BẢO LOGIC
-- =========================================
UPDATE san_pham
SET so_luong_ton = 0
WHERE id_san_pham = 14;

-- =========================================
-- 19. KIỂM TRA NHANH
-- =========================================
SELECT 'Seed data inserted successfully' AS thong_bao;