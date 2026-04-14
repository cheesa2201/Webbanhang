<?php

class Product
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Lấy tất cả sản phẩm
    public function getAll()
    {
        $sql = "SELECT sp.*, dm.ten_danh_muc, th.ten_thuong_hieu
                FROM san_pham sp
                JOIN danh_muc dm ON sp.id_danh_muc = dm.id_danh_muc
                JOIN thuong_hieu th ON sp.id_thuong_hieu = th.id_thuong_hieu
                WHERE sp.trang_thai = 'dang_ban'
                ORDER BY sp.ngay_tao DESC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy theo ID
    public function getById($id)
    {
        $sql = "SELECT sp.*, dm.ten_danh_muc, th.ten_thuong_hieu
                FROM san_pham sp
                JOIN danh_muc dm ON sp.id_danh_muc = dm.id_danh_muc
                JOIN thuong_hieu th ON sp.id_thuong_hieu = th.id_thuong_hieu
                WHERE sp.id_san_pham = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lọc theo danh mục
    public function getByCategory($categoryId)
    {
        $sql = "SELECT *
                FROM san_pham
                WHERE id_danh_muc = :id
                  AND trang_thai = 'dang_ban'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $categoryId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm kiếm
    public function search($keyword)
    {
        $sql = "SELECT *
                FROM san_pham
                WHERE ten_san_pham LIKE :keyword";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'keyword' => "%$keyword%"
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}