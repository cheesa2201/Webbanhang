<?php

class Category
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Lấy danh mục đang hiển thị
    public function getActive()
    {
        $sql = "SELECT *
                FROM danh_muc
                WHERE trang_thai = 'hien'
                ORDER BY ten_danh_muc ASC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh mục + số sản phẩm
    public function getWithCount()
    {
        $sql = "SELECT dm.*, COUNT(sp.id_san_pham) AS so_luong
                FROM danh_muc dm
                LEFT JOIN san_pham sp 
                    ON sp.id_danh_muc = dm.id_danh_muc
                    AND sp.trang_thai = 'dang_ban'
                WHERE dm.trang_thai = 'hien'
                GROUP BY dm.id_danh_muc";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}