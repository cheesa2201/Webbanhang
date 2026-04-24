<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class CartController {
    private $db;
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    // 1. Lấy giỏ hàng
    public function getCart($user_id) {
        $stmt = $this->db->prepare("
            SELECT c.product_id, c.quantity, p.name, p.price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 2. Thêm vào giỏ
    public function addToCart() {
        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'] ?? 1;
        if ($quantity <= 0) $quantity = 1;
        $stmt = $this->db->prepare("SELECT id FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows == 0) {
            die("Sản phẩm không tồn tại!");
        }
        $stmt = $this->db->prepare("
            SELECT quantity FROM cart
            WHERE user_id = ? AND product_id = ?
        ");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $newQty = $row['quantity'] + $quantity;
            $stmt = $this->db->prepare("
                UPDATE cart SET quantity = ?
                WHERE user_id = ? AND product_id = ?
            ");
            $stmt->bind_param("iii", $newQty, $user_id, $product_id);
            $stmt->execute();
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO cart (user_id, product_id, quantity)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $stmt->execute();
        }
        header("Location: /cart");
        exit();
    }

    // 3. Cập nhật số lượng
    public function updateCart() {
        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        if ($quantity <= 0) {
            $this->removeItem($product_id);
            return;
        }
        $stmt = $this->db->prepare("
            UPDATE cart SET quantity = ?
            WHERE user_id = ? AND product_id = ?
        ");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
        header("Location: /cart");
        exit();
    }

    // 4. Xóa 1 sản phẩm
    public function removeItem($product_id = null) {
        $user_id = $_SESSION['user_id'];
        $product_id = $product_id ?? $_GET['product_id'];
        $stmt = $this->db->prepare("
            DELETE FROM cart
            WHERE user_id = ? AND product_id = ?
        ");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        header("Location: /cart");
        exit();
    }

    // 5. Xóa toàn bộ giỏ
    public function clearCart($user_id) {
        $stmt = $this->db->prepare("
            DELETE FROM cart WHERE user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }

    // 6. Trang cart
    public function viewCart() {
        $user_id = $_SESSION['user_id'];
        $cartItems = $this->getCart($user_id);
        require_once __DIR__ . '/../views/cart.php';
    }
}