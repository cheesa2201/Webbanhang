<?php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Brand.php';

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $brandModel;

    public function __construct($pdo)
    {
        $this->productModel = new Product($pdo);
        $this->categoryModel = new Category($pdo);
        $this->brandModel = new Brand($pdo);
    }

    // Trang shop (list + filter)
    public function shop()
    {
        $categoryId = $_GET['category'] ?? null;
        $keyword = $_GET['search'] ?? null;

        if ($keyword) {
            return $this->productModel->search($keyword);
        }

        if ($categoryId) {
            return $this->productModel->getByCategory($categoryId);
        }

        return $this->productModel->getAll();
    }

    // Lấy danh mục
    public function categories()
    {
        return $this->categoryModel->getWithCount();
    }

    // Lấy brand
    public function brands()
    {
        return $this->brandModel->getAll();
    }

    // Chi tiết sản phẩm
    public function detail($id)
    {
        return $this->productModel->getById($id);
    }
}
