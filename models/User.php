<?php
class User {
    private $conn;
    private $table = "nguoi_dung";

    public function __construct($db) {
        $this->conn = $db;
    }

    //Tìm user theo email
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Tìm user theo ID
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_nguoi_dung = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Kiểm tra email đã tồn tại chưa
    public function existsByEmail($email) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    //Tạo user mới (đăng ký)
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
            (id_vai_tro, ho_ten, email, so_dien_thoai, mat_khau, dia_chi)
            VALUES
            (:id_vai_tro, :ho_ten, :email, :so_dien_thoai, :mat_khau, :dia_chi)";

        $stmt = $this->conn->prepare($sql);

        // hash password
        $hashedPassword = password_hash($data['mat_khau'], PASSWORD_BCRYPT);

        $stmt->bindParam(":id_vai_tro", $data['id_vai_tro']);
        $stmt->bindParam(":ho_ten", $data['ho_ten']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":so_dien_thoai", $data['so_dien_thoai']);
        $stmt->bindParam(":mat_khau", $hashedPassword);
        $stmt->bindParam(":dia_chi", $data['dia_chi']);

        return $stmt->execute();
    }
}
?>