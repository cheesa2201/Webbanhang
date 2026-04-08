<?php
class User {
    private $conn;
    private $table = "nguoi_dung";

    public function __construct($db) {
        $this->conn = $db;
    }
    // Tìm user theo email
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        $email = trim($email);
        $stmt->bindParam(":email", $email);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm user theo ID
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_nguoi_dung = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Kiểm tra email tồn tại
    public function existsByEmail($email) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $stmt = $this->conn->prepare($sql);

        $email = trim($email);
        $stmt->bindParam(":email", $email);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return isset($row['count']) && $row['count'] > 0;
    }

    // Tạo user mới (đăng ký)
    public function create($data) {

        // Check email trùng
        if ($this->existsByEmail($data['email'])) {
            return [
                "success" => false,
                "message" => "Email đã tồn tại"
            ];
        }

        $sql = "INSERT INTO {$this->table} 
            (id_vai_tro, ho_ten, email, so_dien_thoai, mat_khau, dia_chi)
            VALUES
            (:id_vai_tro, :ho_ten, :email, :so_dien_thoai, :mat_khau, :dia_chi)";

        $stmt = $this->conn->prepare($sql);

        // Hash password
        $hashedPassword = password_hash($data['mat_khau'], PASSWORD_BCRYPT);

        // Trim dữ liệu
        $ho_ten = trim($data['ho_ten']);
        $email = trim($data['email']);
        $so_dien_thoai = trim($data['so_dien_thoai']);
        $dia_chi = trim($data['dia_chi']);

        $stmt->bindParam(":id_vai_tro", $data['id_vai_tro']);
        $stmt->bindParam(":ho_ten", $ho_ten);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":so_dien_thoai", $so_dien_thoai);
        $stmt->bindParam(":mat_khau", $hashedPassword);
        $stmt->bindParam(":dia_chi", $dia_chi);

        // Execute + xử lý lỗi
        if ($stmt->execute()) {
            return [
                "success" => true,
                "message" => "Tạo user thành công"
            ];
        } else {
            return [
                "success" => false,
                "message" => "Lỗi khi tạo user",
                "error" => $stmt->errorInfo()
            ];
        }
    }
}
?>