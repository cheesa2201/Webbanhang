<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/CartController.php';

class OrderController {
    private $db;
    private $cartController;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->cartController = new CartController();
    }

    public function checkoutPage() {
        $user_id = $_SESSION['user_id'];
        $cartItems = $this->cartController->getCart($user_id);

        if (empty($cartItems)) {
            header("Location: /cart");
            exit();
        }

        require_once __DIR__ . '/../views/checkout.php';
    }

    public function placeOrder() {
        $user_id = $_SESSION['user_id'];
        $cartItems = $this->cartController->getCart($user_id);

        if (empty($cartItems)) {
            die("Giỏ hàng trống!");
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        // tạo order
        $stmt = $this->db->prepare("
            INSERT INTO orders (user_id, total_price, name, phone, address, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("idsss", $user_id, $total, $name, $phone, $address);
        $stmt->execute();

        $order_id = $stmt->insert_id;

        // order items
        foreach ($cartItems as $item) {
            $stmt = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "iiid",
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            );
            $stmt->execute();
        }

        // clear cart
        $this->cartController->clearCart($user_id);

        header("Location: /order/complete?id=" . $order_id);
        exit();
    }

    public function orderComplete() {
        $order_id = $_GET['id'];

        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        require_once __DIR__ . '/../views/order_complete.php';
    }

    public function myOrders() {
        $user_id = $_SESSION['user_id'];

        $stmt = $this->db->prepare("
            SELECT * FROM orders
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        require_once __DIR__ . '/../views/my_orders.php';
    }

    public function orderDetail() {
        $order_id = $_GET['id'];
        $user_id = $_SESSION['user_id'];

        $stmt = $this->db->prepare("
            SELECT * FROM orders
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        if (!$order) die("Không có đơn!");

        $stmt = $this->db->prepare("
            SELECT oi.*, p.name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        require_once __DIR__ . '/../views/order_detail.php';
    }
}