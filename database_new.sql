use webbanhang;

CREATE TABLE vai_tro (
    id_vai_tro INT AUTO_INCREMENT PRIMARY KEY,
    ten_vai_tro VARCHAR(50) NOT NULL UNIQUE,
    mo_ta VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE nguoi_dung (
    id_nguoi_dung INT AUTO_INCREMENT PRIMARY KEY,
    id_vai_tro INT NOT NULL,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    so_dien_thoai VARCHAR(20) UNIQUE,
    mat_khau VARCHAR(255) NOT NULL,
    dia_chi TEXT,
    trang_thai ENUM('hoat_dong', 'khoa') DEFAULT 'hoat_dong',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_nguoi_dung_vai_tro
        FOREIGN KEY (id_vai_tro) REFERENCES vai_tro(id_vai_tro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE danh_muc (
    id_danh_muc INT AUTO_INCREMENT PRIMARY KEY,
    ten_danh_muc VARCHAR(100) NOT NULL UNIQUE,
    mo_ta TEXT,
    trang_thai ENUM('hien', 'an') DEFAULT 'hien'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE thuong_hieu (
    id_thuong_hieu INT AUTO_INCREMENT PRIMARY KEY,
    ten_thuong_hieu VARCHAR(100) NOT NULL UNIQUE,
    quoc_gia VARCHAR(100),
    mo_ta TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE san_pham (
    id_san_pham INT AUTO_INCREMENT PRIMARY KEY,
    id_danh_muc INT NOT NULL,
    id_thuong_hieu INT NOT NULL,
    ten_san_pham VARCHAR(200) NOT NULL,
    ma_san_pham VARCHAR(50) NOT NULL UNIQUE,
    gia DECIMAL(15,2) NOT NULL DEFAULT 0,
    so_luong_ton INT NOT NULL DEFAULT 0,
    mo_ta_ngan VARCHAR(500),
    mo_ta_chi_tiet TEXT,
    hinh_anh_chinh VARCHAR(255),
    trang_thai ENUM('dang_ban', 'ngung_ban', 'het_hang') DEFAULT 'dang_ban',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_san_pham_danh_muc
        FOREIGN KEY (id_danh_muc) REFERENCES danh_muc(id_danh_muc),
    CONSTRAINT fk_san_pham_thuong_hieu
        FOREIGN KEY (id_thuong_hieu) REFERENCES thuong_hieu(id_thuong_hieu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE hinh_anh_san_pham (
    id_hinh_anh INT AUTO_INCREMENT PRIMARY KEY,
    id_san_pham INT NOT NULL,
    duong_dan_anh VARCHAR(255) NOT NULL,
    la_anh_chinh BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_hinh_anh_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE thong_so_ky_thuat (
    id_thong_so INT AUTO_INCREMENT PRIMARY KEY,
    id_san_pham INT NOT NULL,
    ten_thong_so VARCHAR(100) NOT NULL,
    gia_tri_thong_so VARCHAR(255) NOT NULL,
    CONSTRAINT fk_thong_so_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE gio_hang (
    id_gio_hang INT AUTO_INCREMENT PRIMARY KEY,
    id_nguoi_dung INT NOT NULL UNIQUE,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_gio_hang_nguoi_dung
        FOREIGN KEY (id_nguoi_dung) REFERENCES nguoi_dung(id_nguoi_dung)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE chi_tiet_gio_hang (
    id_gio_hang INT NOT NULL,
    id_san_pham INT NOT NULL,
    so_luong INT NOT NULL DEFAULT 1,
    don_gia DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (id_gio_hang, id_san_pham),
    CONSTRAINT fk_ctgh_gio_hang
        FOREIGN KEY (id_gio_hang) REFERENCES gio_hang(id_gio_hang)
        ON DELETE CASCADE,
    CONSTRAINT fk_ctgh_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE phuong_thuc_thanh_toan (
    id_phuong_thuc INT AUTO_INCREMENT PRIMARY KEY,
    ten_phuong_thuc VARCHAR(100) NOT NULL UNIQUE,
    mo_ta VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE don_hang (
    id_don_hang INT AUTO_INCREMENT PRIMARY KEY,
    id_nguoi_dung INT NOT NULL,
    id_phuong_thuc INT,
    ngay_dat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tong_tien DECIMAL(15,2) NOT NULL DEFAULT 0,
    ten_nguoi_nhan VARCHAR(100) NOT NULL,
    so_dien_thoai_nhan VARCHAR(20) NOT NULL,
    dia_chi_giao_hang TEXT NOT NULL,
    ghi_chu TEXT,
    trang_thai_don_hang ENUM(
        'cho_xac_nhan',
        'da_xac_nhan',
        'dang_giao',
        'da_giao',
        'da_huy'
    ) DEFAULT 'cho_xac_nhan',
    CONSTRAINT fk_don_hang_nguoi_dung
        FOREIGN KEY (id_nguoi_dung) REFERENCES nguoi_dung(id_nguoi_dung),
    CONSTRAINT fk_don_hang_phuong_thuc
        FOREIGN KEY (id_phuong_thuc) REFERENCES phuong_thuc_thanh_toan(id_phuong_thuc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE chi_tiet_don_hang (
    id_don_hang INT NOT NULL,
    id_san_pham INT NOT NULL,
    so_luong INT NOT NULL,
    don_gia DECIMAL(15,2) NOT NULL,
    thanh_tien DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (id_don_hang, id_san_pham),
    CONSTRAINT fk_ctdh_don_hang
        FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE CASCADE,
    CONSTRAINT fk_ctdh_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE thanh_toan (
    id_thanh_toan INT AUTO_INCREMENT PRIMARY KEY,
    id_don_hang INT NOT NULL,
    id_phuong_thuc INT NOT NULL,
    so_tien DECIMAL(15,2) NOT NULL,
    trang_thai_thanh_toan ENUM('chua_thanh_toan', 'da_thanh_toan', 'that_bai') DEFAULT 'chua_thanh_toan',
    ngay_thanh_toan TIMESTAMP NULL,
    CONSTRAINT fk_thanh_toan_don_hang
        FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE CASCADE,
    CONSTRAINT fk_thanh_toan_phuong_thuc
        FOREIGN KEY (id_phuong_thuc) REFERENCES phuong_thuc_thanh_toan(id_phuong_thuc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE danh_gia (
    id_danh_gia INT AUTO_INCREMENT PRIMARY KEY,
    id_nguoi_dung INT NOT NULL,
    id_san_pham INT NOT NULL,
    so_sao INT NOT NULL CHECK (so_sao BETWEEN 1 AND 5),
    noi_dung TEXT,
    ngay_danh_gia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_danh_gia_nguoi_dung
        FOREIGN KEY (id_nguoi_dung) REFERENCES nguoi_dung(id_nguoi_dung)
        ON DELETE CASCADE,
    CONSTRAINT fk_danh_gia_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE khuyen_mai (
    id_khuyen_mai INT AUTO_INCREMENT PRIMARY KEY,
    ten_khuyen_mai VARCHAR(150) NOT NULL,
    phan_tram_giam DECIMAL(5,2) NOT NULL DEFAULT 0,
    ngay_bat_dau DATE NOT NULL,
    ngay_ket_thuc DATE NOT NULL,
    trang_thai ENUM('sap_dien_ra', 'dang_dien_ra', 'ket_thuc') DEFAULT 'sap_dien_ra'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE san_pham_khuyen_mai (
    id_san_pham INT NOT NULL,
    id_khuyen_mai INT NOT NULL,
    PRIMARY KEY (id_san_pham, id_khuyen_mai),
    CONSTRAINT fk_spkm_san_pham
        FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE,
    CONSTRAINT fk_spkm_khuyen_mai
        FOREIGN KEY (id_khuyen_mai) REFERENCES khuyen_mai(id_khuyen_mai)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE van_chuyen (
    id_van_chuyen INT AUTO_INCREMENT PRIMARY KEY,
    id_don_hang INT NOT NULL,
    don_vi_van_chuyen VARCHAR(100),
    phi_van_chuyen DECIMAL(15,2) NOT NULL DEFAULT 0,
    ma_van_don VARCHAR(100),
    trang_thai_giao_hang ENUM(
        'chuan_bi_hang',
        'dang_giao',
        'giao_thanh_cong',
        'giao_that_bai'
    ) DEFAULT 'chuan_bi_hang',
    CONSTRAINT fk_van_chuyen_don_hang
        FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE dia_chi_giao_hang (
    id_dia_chi INT AUTO_INCREMENT PRIMARY KEY,
    id_nguoi_dung INT NOT NULL,
    ten_nguoi_nhan VARCHAR(100) NOT NULL,
    so_dien_thoai_nhan VARCHAR(20) NOT NULL,
    dia_chi TEXT NOT NULL,
    la_mac_dinh BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_dia_chi_nguoi_dung
        FOREIGN KEY (id_nguoi_dung) REFERENCES nguoi_dung(id_nguoi_dung)
        ON DELETE CASCADE
);


-- transaction dat hang
DELIMITER $$

CREATE PROCEDURE sp_DatHang (
    IN p_id_nguoi_dung INT,
    IN p_id_phuong_thuc INT,
    IN p_ten_nguoi_nhan VARCHAR(100),
    IN p_so_dien_thoai_nhan VARCHAR(20),
    IN p_dia_chi_giao_hang TEXT,
    IN p_ghi_chu TEXT
)
BEGIN
    DECLARE v_id_gio_hang INT;
    DECLARE v_id_don_hang INT;
    DECLARE v_tong_tien DECIMAL(15,2) DEFAULT 0;
    DECLARE v_so_san_pham INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Dat hang that bai.';
    END;

    START TRANSACTION;

    -- 1. Lấy giỏ hàng của người dùng
    SELECT id_gio_hang
    INTO v_id_gio_hang
    FROM gio_hang
    WHERE id_nguoi_dung = p_id_nguoi_dung;

    IF v_id_gio_hang IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Nguoi dung chua co gio hang.';
    END IF;

    -- 2. Kiểm tra giỏ hàng có sản phẩm không
    SELECT COUNT(*)
    INTO v_so_san_pham
    FROM chi_tiet_gio_hang
    WHERE id_gio_hang = v_id_gio_hang;

    IF v_so_san_pham = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Gio hang rong, khong the dat hang.';
    END IF;

    -- 3. Kiểm tra tồn kho
    IF EXISTS (
        SELECT 1
        FROM chi_tiet_gio_hang ctgh
        JOIN san_pham sp ON sp.id_san_pham = ctgh.id_san_pham
        WHERE ctgh.id_gio_hang = v_id_gio_hang
          AND ctgh.so_luong > sp.so_luong_ton
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Co san pham trong gio vuot qua so luong ton.';
    END IF;

    -- 4. Tính tổng tiền
    SELECT SUM(so_luong * don_gia)
    INTO v_tong_tien
    FROM chi_tiet_gio_hang
    WHERE id_gio_hang = v_id_gio_hang;

    -- 5. Tạo đơn hàng
    INSERT INTO don_hang (
        id_nguoi_dung,
        id_phuong_thuc,
        ngay_dat,
        tong_tien,
        ten_nguoi_nhan,
        so_dien_thoai_nhan,
        dia_chi_giao_hang,
        ghi_chu,
        trang_thai_don_hang
    )
    VALUES (
        p_id_nguoi_dung,
        p_id_phuong_thuc,
        CURRENT_TIMESTAMP,
        v_tong_tien,
        p_ten_nguoi_nhan,
        p_so_dien_thoai_nhan,
        p_dia_chi_giao_hang,
        p_ghi_chu,
        'cho_xac_nhan'
    );

    SET v_id_don_hang = LAST_INSERT_ID();

    -- 6. Tạo chi tiết đơn hàng từ giỏ hàng
    INSERT INTO chi_tiet_don_hang (
        id_don_hang,
        id_san_pham,
        so_luong,
        don_gia,
        thanh_tien
    )
    SELECT
        v_id_don_hang,
        ctgh.id_san_pham,
        ctgh.so_luong,
        ctgh.don_gia,
        ctgh.so_luong * ctgh.don_gia
    FROM chi_tiet_gio_hang ctgh
    WHERE ctgh.id_gio_hang = v_id_gio_hang;

    -- 7. Tạo bản ghi thanh toán
    INSERT INTO thanh_toan (
        id_don_hang,
        id_phuong_thuc,
        so_tien,
        trang_thai_thanh_toan,
        ngay_thanh_toan
    )
    VALUES (
        v_id_don_hang,
        p_id_phuong_thuc,
        v_tong_tien,
        'chua_thanh_toan',
        NULL
    );

    -- 8. Trừ tồn kho
    UPDATE san_pham sp
    JOIN chi_tiet_gio_hang ctgh
        ON sp.id_san_pham = ctgh.id_san_pham
    SET sp.so_luong_ton = sp.so_luong_ton - ctgh.so_luong
    WHERE ctgh.id_gio_hang = v_id_gio_hang;

    -- 9. Xóa chi tiết giỏ hàng sau khi đặt thành công
    DELETE FROM chi_tiet_gio_hang
    WHERE id_gio_hang = v_id_gio_hang;

    COMMIT;

    SELECT 
        'Dat hang thanh cong' AS thong_bao,
        v_id_don_hang AS id_don_hang_moi,
        v_tong_tien AS tong_tien;
END$$

DELIMITER ;


-- transaction huy don hang
DELIMITER $$

CREATE PROCEDURE sp_HuyDonHang (
    IN p_id_don_hang INT
)
BEGIN
    DECLARE v_trang_thai_don_hang VARCHAR(20);
    DECLARE v_ton_tai_don_hang INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Huy don that bai, da rollback du lieu.';
    END;

    START TRANSACTION;

    -- 1. Kiểm tra đơn hàng tồn tại
    SELECT COUNT(*)
    INTO v_ton_tai_don_hang
    FROM don_hang
    WHERE id_don_hang = p_id_don_hang;

    IF v_ton_tai_don_hang = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Don hang khong ton tai.';
    END IF;

    -- 2. Lấy trạng thái hiện tại
    SELECT trang_thai_don_hang
    INTO v_trang_thai_don_hang
    FROM don_hang
    WHERE id_don_hang = p_id_don_hang
    FOR UPDATE;

    -- 3. Chỉ cho hủy khi đơn chưa giao
    IF v_trang_thai_don_hang NOT IN ('cho_xac_nhan', 'da_xac_nhan') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Chi duoc huy don o trang thai cho_xac_nhan hoac da_xac_nhan.';
    END IF;

    -- 4. Hoàn lại tồn kho
    UPDATE san_pham sp
    JOIN chi_tiet_don_hang ctdh
        ON sp.id_san_pham = ctdh.id_san_pham
    SET sp.so_luong_ton = sp.so_luong_ton + ctdh.so_luong
    WHERE ctdh.id_don_hang = p_id_don_hang;

    -- 5. Cập nhật trạng thái đơn hàng
    UPDATE don_hang
    SET trang_thai_don_hang = 'da_huy'
    WHERE id_don_hang = p_id_don_hang;

    -- 6. Cập nhật thanh toán nếu chưa thanh toán thành công
    UPDATE thanh_toan
    SET trang_thai_thanh_toan = 'that_bai'
    WHERE id_don_hang = p_id_don_hang
      AND trang_thai_thanh_toan = 'chua_thanh_toan';

    COMMIT;

    SELECT 'Huy don hang thanh cong' AS thong_bao;
END$$

DELIMITER ;

-- transaction giao hang hoan tat
DELIMITER $$

CREATE PROCEDURE sp_GiaoHangThanhCong (
    IN p_id_don_hang INT
)
BEGIN
    DECLARE v_ton_tai_don_hang INT DEFAULT 0;
    DECLARE v_ton_tai_van_chuyen INT DEFAULT 0;
    DECLARE v_trang_thai_don_hang VARCHAR(20);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cap nhat giao hang that bai';
    END;

    START TRANSACTION;

    -- 1. Kiểm tra đơn hàng tồn tại
    SELECT COUNT(*)
    INTO v_ton_tai_don_hang
    FROM don_hang
    WHERE id_don_hang = p_id_don_hang;

    IF v_ton_tai_don_hang = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Don hang khong ton tai.';
    END IF;

    -- 2. Kiểm tra bản ghi vận chuyển tồn tại
    SELECT COUNT(*)
    INTO v_ton_tai_van_chuyen
    FROM van_chuyen
    WHERE id_don_hang = p_id_don_hang;

    IF v_ton_tai_van_chuyen = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Don hang chua co thong tin van chuyen.';
    END IF;

    -- 3. Kiểm tra trạng thái đơn hàng hiện tại
    SELECT trang_thai_don_hang
    INTO v_trang_thai_don_hang
    FROM don_hang
    WHERE id_don_hang = p_id_don_hang
    FOR UPDATE;

    IF v_trang_thai_don_hang = 'da_huy' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Don hang da huy, khong the giao thanh cong.';
    END IF;

    -- 4. Cập nhật vận chuyển
    UPDATE van_chuyen
    SET trang_thai_giao_hang = 'giao_thanh_cong'
    WHERE id_don_hang = p_id_don_hang;

    -- 5. Cập nhật đơn hàng
    UPDATE don_hang
    SET trang_thai_don_hang = 'da_giao'
    WHERE id_don_hang = p_id_don_hang;

    -- 6. Cập nhật thanh toán
    UPDATE thanh_toan
    SET trang_thai_thanh_toan = 'da_thanh_toan',
        ngay_thanh_toan = CURRENT_TIMESTAMP
    WHERE id_don_hang = p_id_don_hang
      AND trang_thai_thanh_toan <> 'da_thanh_toan';

    COMMIT;

    SELECT 'Cap nhat giao hang thanh cong' AS thong_bao;
END$$

DELIMITER ;



-- TRIGGER 
-- TRIGGER TÍNH TIỀN
-- TRƯỚC KHI CHÈN VÀO GIỎ HÀNG
DELIMITER $$

CREATE TRIGGER trg_ctdh_before_insert
BEFORE INSERT ON chi_tiet_don_hang
FOR EACH ROW
BEGIN
    SET NEW.thanh_tien = NEW.so_luong * NEW.don_gia;
END$$

DELIMITER ;
-- TRƯỚC KHI CẬP NHẬT GIỎ HÀNG
DELIMITER $$

CREATE TRIGGER trg_ctdh_before_update
BEFORE UPDATE ON chi_tiet_don_hang
FOR EACH ROW
BEGIN
    SET NEW.thanh_tien = NEW.so_luong * NEW.don_gia;
END$$

DELIMITER ;

-- TRIGGER CẬP NAHATJ TỔNG TIỀN CỦA ĐƠN HÀNG
-- SAU KHI CHÈN ĐƠN HÀNG 
DELIMITER $$

CREATE TRIGGER trg_ctdh_after_insert
AFTER INSERT ON chi_tiet_don_hang
FOR EACH ROW
BEGIN
    UPDATE don_hang
    SET tong_tien = (
        SELECT SUM(thanh_tien)
        FROM chi_tiet_don_hang
        WHERE id_don_hang = NEW.id_don_hang
    )
    WHERE id_don_hang = NEW.id_don_hang;
END$$

DELIMITER ;

-- SAU KHI UPDATE
DELIMITER $$

CREATE TRIGGER trg_ctdh_after_update
AFTER UPDATE ON chi_tiet_don_hang
FOR EACH ROW
BEGIN
    UPDATE don_hang
    SET tong_tien = (
        SELECT SUM(thanh_tien)
        FROM chi_tiet_don_hang
        WHERE id_don_hang = NEW.id_don_hang
    )
    WHERE id_don_hang = NEW.id_don_hang;
END$$

DELIMITER ;

-- SAU KHI BỎ SẢN PHẨM
DELIMITER $$

CREATE TRIGGER trg_ctdh_after_delete
AFTER DELETE ON chi_tiet_don_hang
FOR EACH ROW
BEGIN
    UPDATE don_hang
    SET tong_tien = (
        SELECT IFNULL(SUM(thanh_tien), 0)
        FROM chi_tiet_don_hang
        WHERE id_don_hang = OLD.id_don_hang
    )
    WHERE id_don_hang = OLD.id_don_hang;
END$$

DELIMITER ;

-- TRIGGER CẬP NHẬT TRẠNG THÁI TỒN KHBOO
DELIMITER $$

CREATE TRIGGER trg_sanpham_update_trangthai
BEFORE UPDATE ON san_pham
FOR EACH ROW
BEGIN
    IF NEW.so_luong_ton <= 0 THEN
        SET NEW.trang_thai = 'het_hang';
    ELSE
        SET NEW.trang_thai = 'dang_ban';
    END IF;
END$$

DELIMITER ;