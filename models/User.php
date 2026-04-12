<?php

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, vt.ten_vai_tro
            FROM nguoi_dung u
            LEFT JOIN vai_tro vt ON u.id_vai_tro = vt.id_vai_tro
            WHERE u.email = :email
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM nguoi_dung WHERE email = :email"
        );
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function create(array $data): array
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO nguoi_dung 
                    (ho_ten, email, so_dien_thoai, mat_khau, dia_chi, id_vai_tro, trang_thai)
                VALUES 
                    (:ho_ten, :email, :so_dien_thoai, :mat_khau, :dia_chi, :id_vai_tro, 'hoat_dong')
            ");
            $stmt->execute([
                ':ho_ten'        => $data['ho_ten'],
                ':email'         => $data['email'],
                ':so_dien_thoai' => $data['so_dien_thoai'] ?? null,
                ':mat_khau'      => $data['mat_khau'],
                ':dia_chi'       => $data['dia_chi'] ?? null,
                ':id_vai_tro'    => $data['id_vai_tro'] ?? 2,
            ]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}