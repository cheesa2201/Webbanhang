<?php
class User {
    private $conn;
    private $table = "nguoi_dung";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function findByEmail($email) {
        if (empty($email)) return null;
        
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $email = trim($email);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        if (empty($id)) return null;
        
        $sql = "SELECT * FROM {$this->table} WHERE id_nguoi_dung = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT); // Ép kiểu INT
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existsByEmail($email) {
        if (empty($email)) return false;
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $email = trim($email);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (bool) $row['count']; // Clean hơn
    }

    public function create($data) {
        // Validation cơ bản
        $required = ['ho_ten', 'email', 'so_dien_thoai', 'mat_khau', 'id_vai_tro'];
        foreach ($required as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                return [
                    "success" => false,
                    "message" => "Trường $field không được để trống"
                ];
            }
        }

        if ($this->existsByEmail($data['email'])) {
            return ["success" => false, "message" => "Email đã tồn tại"];
        }

        $sql = "INSERT INTO {$this->table} 
            (id_vai_tro, ho_ten, email, so_dien_thoai, mat_khau, dia_chi)
            VALUES (:id_vai_tro, :ho_ten, :email, :so_dien_thoai, :mat_khau, :dia_chi)";

        $stmt = $this->conn->prepare($sql);
        $hashedPassword = password_hash($data['mat_khau'], PASSWORD_DEFAULT); // PASSWORD_DEFAULT linh hoạt hơn

        // Trim tất cả
        $params = [
            'id_vai_tro' => $data['id_vai_tro'],
            'ho_ten' => trim($data['ho_ten']),
            'email' => trim($data['email']),
            'so_dien_thoai' => trim($data['so_dien_thoai']),
            'dia_chi' => trim($data['dia_chi'] ?? '')
        ];

        // Bind params gọn hơn
        foreach ($params as $key => $value) {
            $stmt->bindParam(":$key", $params[$key]);
        }
        $stmt->bindParam(":mat_khau", $hashedPassword);

        if ($stmt->execute()) {
            return [
                "success" => true,
                "message" => "Tạo user thành công",
                "id" => $this->conn->lastInsertId() // Trả về ID user mới
            ];
        }
        
        return [
            "success" => false,
            "message" => "Lỗi khi tạo user",
            "error" => $stmt->errorInfo()[2] ?? 'Unknown error' // Chỉ lấy message
        ];
    }
}
?>