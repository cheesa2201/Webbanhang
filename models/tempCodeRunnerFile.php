<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database_connect.php';

class Cart
{
    private ?mysqli $conn = null;
    private int $id_gio_hang = 0;
    private int $id_nguoi_dung = 0;

    public function __construct(?mysqli $conn = null)
    {
        if ($conn === null) {
            $conn = db_connect();
        }
        $this->conn = $conn;
    }

    /**
     * Lấy hoặc tạo giỏ hàng cho người dùng
     * 
     * @param int $id_nguoi_dung ID của người dùng
     * @return bool Trả về true nếu thành công
     */
    public function getOrCreateCartByUserId(int $id_nguoi_dung): bool
    {
        try {
            if ($id_nguoi_dung <= 0) {
                throw new Exception("ID người dùng không hợp lệ");
            }

            // Kiểm tra xem giỏ hàng đã tồn tại chưa
            $stmt = $this->conn->prepare("SELECT id_gio_hang FROM gio_hang WHERE id_nguoi_dung = ?");
            $stmt->bind_param("i", $id_nguoi_dung);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Giỏ hàng đã tồn tại
                $row = $result->fetch_assoc();
                $this->id_gio_hang = (int)$row['id_gio_hang'];
                $this->id_nguoi_dung = $id_nguoi_dung;
                $stmt->close();
                return true;
            }

            $stmt->close();

            // Tạo giỏ hàng mới
            $stmt = $this->conn->prepare("INSERT INTO gio_hang (id_nguoi_dung, ngay_tao) VALUES (?, NOW())");
            $stmt->bind_param("i", $id_nguoi_dung);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi tạo giỏ hàng: " . $stmt->error);
            }

            $this->id_gio_hang = $this->conn->insert_id;
            $this->id_nguoi_dung = $id_nguoi_dung;
            $stmt->close();

            return true;
        } catch (Exception $e) {
            error_log("Lỗi getOrCreateCartByUserId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách các mặt hàng trong giỏ hàng
     * 
     * @return array|null Mảng các mặt hàng hoặc null nếu không có hoặc lỗi
     */
    public function getCartItems(): ?array
    {
        try {
            if ($this->id_gio_hang <= 0) {
                throw new Exception("Giỏ hàng chưa được khởi tạo");
            }

            $query = "
                SELECT 
                    ctgh.id_san_pham,
                    ctgh.so_luong,
                    ctgh.don_gia,
                    (ctgh.so_luong * ctgh.don_gia) as thanh_tien,
                    sp.ten_san_pham,
                    sp.ma_san_pham,
                    sp.hinh_anh_chinh,
                    sp.so_luong_ton,
                    sp.trang_thai
                FROM chi_tiet_gio_hang ctgh
                JOIN san_pham sp ON ctgh.id_san_pham = sp.id_san_pham
                WHERE ctgh.id_gio_hang = ?
                ORDER BY ctgh.id_san_pham DESC
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $this->id_gio_hang);
            $stmt->execute();
            $result = $stmt->get_result();

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            $stmt->close();
            return $items;
        } catch (Exception $e) {
            error_log("Lỗi getCartItems: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     * 
     * @param int $id_san_pham ID sản phẩm
     * @param int $so_luong Số lượng (mặc định 1)
     * @param float|null $don_gia Đơn giá (nếu null sẽ lấy từ DB)
     * @return bool Trả về true nếu thành công
     */
    public function addItem(int $id_san_pham, int $so_luong = 1, ?float $don_gia = null): bool
    {
        try {
            if ($this->id_gio_hang <= 0) {
                throw new Exception("Giỏ hàng chưa được khởi tạo");
            }

            if ($id_san_pham <= 0 || $so_luong <= 0) {
                throw new Exception("ID sản phẩm hoặc số lượng không hợp lệ");
            }

            // Lấy giá sản phẩm từ database nếu không cung cấp
            if ($don_gia === null) {
                $stmt = $this->conn->prepare("SELECT gia FROM san_pham WHERE id_san_pham = ?");
                $stmt->bind_param("i", $id_san_pham);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception("Sản phẩm không tồn tại");
                }

                $row = $result->fetch_assoc();
                $don_gia = (float)$row['gia'];
                $stmt->close();
            }

            // Kiểm tra sản phẩm đã có trong giỏ không
            $stmt = $this->conn->prepare(
                "SELECT so_luong FROM chi_tiet_gio_hang 
                 WHERE id_gio_hang = ? AND id_san_pham = ?"
            );
            $stmt->bind_param("ii", $this->id_gio_hang, $id_san_pham);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Sản phẩm đã có, cập nhật số lượng
                $row = $result->fetch_assoc();
                $so_luong_moi = (int)$row['so_luong'] + $so_luong;
                $stmt->close();

                return $this->updateItem($id_san_pham, $so_luong_moi, $don_gia);
            }

            $stmt->close();

            // Thêm sản phẩm mới
            $stmt = $this->conn->prepare(
                "INSERT INTO chi_tiet_gio_hang (id_gio_hang, id_san_pham, so_luong, don_gia) 
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("iid", $this->id_gio_hang, $id_san_pham, $so_luong, $don_gia);

            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi thêm sản phẩm: " . $stmt->error);
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("Lỗi addItem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     * 
     * @param int $id_san_pham ID sản phẩm
     * @param int $so_luong Số lượng mới
     * @param float|null $don_gia Đơn giá mới (tùy chọn)
     * @return bool Trả về true nếu thành công
     */
    public function updateItem(int $id_san_pham, int $so_luong, ?float $don_gia = null): bool
    {
        try {
            if ($this->id_gio_hang <= 0) {
                throw new Exception("Giỏ hàng chưa được khởi tạo");
            }

            if ($id_san_pham <= 0) {
                throw new Exception("ID sản phẩm không hợp lệ");
            }

            // Nếu số lượng <= 0, xóa sản phẩm
            if ($so_luong <= 0) {
                return $this->removeItem($id_san_pham);
            }

            if ($don_gia !== null && $don_gia < 0) {
                throw new Exception("Đơn giá không hợp lệ");
            }

            // Kiểm tra sản phẩm có tồn tại trong giỏ
            $stmt = $this->conn->prepare(
                "SELECT don_gia FROM chi_tiet_gio_hang 
                 WHERE id_gio_hang = ? AND id_san_pham = ?"
            );
            $stmt->bind_param("ii", $this->id_gio_hang, $id_san_pham);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Sản phẩm không có trong giỏ hàng");
            }

            if ($don_gia === null) {
                $row = $result->fetch_assoc();
                $don_gia = $row['don_gia'];
            }

            $stmt->close();

            // Cập nhật số lượng và đơn giá
            if ($don_gia !== null) {
                $stmt = $this->conn->prepare(
                    "UPDATE chi_tiet_gio_hang 
                     SET so_luong = ?, don_gia = ? 
                     WHERE id_gio_hang = ? AND id_san_pham = ?"
                );
                $stmt->bind_param("idii", $so_luong, $don_gia, $this->id_gio_hang, $id_san_pham);
            } else {
                $stmt = $this->conn->prepare(
                    "UPDATE chi_tiet_gio_hang 
                     SET so_luong = ? 
                     WHERE id_gio_hang = ? AND id_san_pham = ?"
                );
                $stmt->bind_param("iii", $so_luong, $this->id_gio_hang, $id_san_pham);
            }

            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi cập nhật sản phẩm: " . $stmt->error);
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("Lỗi updateItem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     * 
     * @param int $id_san_pham ID sản phẩm
     * @return bool Trả về true nếu thành công
     */
    public function removeItem(int $id_san_pham): bool
    {
        try {
            if ($this->id_gio_hang <= 0) {
                throw new Exception("Giỏ hàng chưa được khởi tạo");
            }

            if ($id_san_pham <= 0) {
                throw new Exception("ID sản phẩm không hợp lệ");
            }

            $stmt = $this->conn->prepare(
                "DELETE FROM chi_tiet_gio_hang 
                 WHERE id_gio_hang = ? AND id_san_pham = ?"
            );
            $stmt->bind_param("ii", $this->id_gio_hang, $id_san_pham);

            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi xóa sản phẩm: " . $stmt->error);
            }

            $affected = $stmt->affected_rows;
            $stmt->close();

            return $affected > 0;
        } catch (Exception $e) {
            error_log("Lỗi removeItem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     * 
     * @return bool Trả về true nếu thành công
     */
    public function clearCart(): bool
    {
        try {
            if ($this->id_gio_hang <= 0) {
                throw new Exception("Giỏ hàng chưa được khởi tạo");
            }

            $stmt = $this->conn->prepare(
                "DELETE FROM chi_tiet_gio_hang WHERE id_gio_hang = ?"
            );
            $stmt->bind_param("i", $this->id_gio_hang);

            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi xóa giỏ hàng: " . $stmt->error);
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("Lỗi clearCart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tổng tiền giỏ hàng
     * 
     * @return float|null Tổng tiền hoặc null nếu lỗi
     */
    public function getTotalPrice(): ?float
    {
        try {
            if ($this->id_gio_hang <= 0) {
                throw new Exception("Giỏ hàng chưa được khởi tạo");
            }

            $stmt = $this->conn->prepare(
                "SELECT SUM(so_luong * don_gia) as tong_tien 
                 FROM chi_tiet_gio_hang 
                 WHERE id_gio_hang = ?"
            );
            $stmt->bind_param("i", $this->id_gio_hang);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            return $row['tong_tien'] ? (float)$row['tong_tien'] : 0.0;
        } catch (Exception $e) {
            error_log("Lỗi getTotalPrice: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy số lượng mặt hàng trong giỏ hàng
     * 
     * @return int|null Số lượng mặt hàng hoặc null nếu lỗi
     */
    public function getItemCount(): ?int
    {
        try {
            if ($this->id_gio_hang <= 0) {
                throw new Exception("Giỏ hàng chưa được khởi tạo");
            }

            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as so_luong_mat_hang 
                 FROM chi_tiet_gio_hang 
                 WHERE id_gio_hang = ?"
            );
            $stmt->bind_param("i", $this->id_gio_hang);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            return (int)$row['so_luong_mat_hang'];
        } catch (Exception $e) {
            error_log("Lỗi getItemCount: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy ID giỏ hàng hiện tại
     * 
     * @return int ID giỏ hàng
     */
    public function getCartId(): int
    {
        return $this->id_gio_hang;
    }

    /**
     * Lấy ID người dùng
     * 
     * @return int ID người dùng
     */
    public function getUserId(): int
    {
        return $this->id_nguoi_dung;
    }

    /**
     * Đóng kết nối database
     */
    public function __destruct()
    {
        db_close($this->conn);
    }
}