<?php
require_once __DIR__ . '/../config/database.php';

class OrderDAO {
    
    // ---------------------------------------------------------
    // EXISTING FUNCTIONS (For Checkout & User Profile)
    // ---------------------------------------------------------

    public static function createOrder($userId, $cartItems, $totalPrice, $couponId = null, $discountAmount = 0.00) {
        $con = Database::connect();
        $con->begin_transaction();

        try {
            $subtotal = $totalPrice + $discountAmount;

            // Table: customer_order
            $stmt = $con->prepare("INSERT INTO customer_order 
                (user_id, coupon_used_id, subtotal, total_discount, total_price, status, order_date) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
            
            $stmt->bind_param("iiddd", $userId, $couponId, $subtotal, $discountAmount, $totalPrice);
            
            $stmt->execute();
            $orderId = $con->insert_id;
            $stmt->close();

            // Table: order_line
            $stmtLine = $con->prepare("INSERT INTO order_line (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            
            foreach ($cartItems as $item) {
                $prod = $item['product'];
                $qty = $item['quantity'];
                $price = $prod->getBasePrice();
                $prodId = $prod->getProductId();

                $stmtLine->bind_param("iiid", $orderId, $prodId, $qty, $price);
                $stmtLine->execute();
            }
            $stmtLine->close();

            $con->commit();
            $con->close();
            return $orderId;

        } catch (Exception $e) {
            $con->rollback();
            $con->close();
            return false;
        }
    }

    public static function getMostRecentOrderItems($userId) {
        $con = Database::connect();
        
        $sql = "SELECT ol.product_id, ol.quantity 
                FROM order_line ol
                WHERE ol.order_id = (
                    SELECT order_id 
                    FROM customer_order 
                    WHERE user_id = ? 
                    ORDER BY order_date DESC 
                    LIMIT 1
                )";

        $stmt = $con->prepare($sql);
        
        if (!$stmt) return [];

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        $stmt->close();
        $con->close();
        
        return $items; 
    }

    public static function hasPreviousOrder($userId) {
        $con = Database::connect();
        
        $stmt = $con->prepare("SELECT order_id FROM customer_order WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();
        
        $exists = $stmt->num_rows > 0;
        
        $stmt->close();
        $con->close();
        
        return $exists;
    }

    // ---------------------------------------------------------
    // NEW FUNCTIONS (FOR ADMIN DASHBOARD)
    // ---------------------------------------------------------

    /**
     * Returns all orders joined with their items using your DB structure:
     * Tables: customer_order, order_line, product
     */
    public static function getAllOrdersWithItems() {
        $con = Database::connect();
        
        // We select the correct columns based on your SQL dump
        $sql = "SELECT 
                    o.order_id, 
                    o.user_id, 
                    o.order_date, 
                    o.total_price, 
                    o.status,
                    ol.product_id, 
                    ol.quantity, 
                    ol.unit_price,
                    p.name as product_name
                FROM customer_order o
                JOIN order_line ol ON o.order_id = ol.order_id
                JOIN product p ON ol.product_id = p.product_id
                ORDER BY o.order_date DESC";

        $result = $con->query($sql);
        $ordersMap = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['order_id'];

                // If first time seeing this order, initialize it
                if (!isset($ordersMap[$id])) {
                    $ordersMap[$id] = [
                        'id' => $id,
                        'user_id' => $row['user_id'],
                        'date' => $row['order_date'],
                        'total' => $row['total_price'],
                        'status' => $row['status'],
                        'items' => [] 
                    ];
                }

                // Add the item details
                $ordersMap[$id]['items'][] = [
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity'],
                    'price' => $row['unit_price'],
                    'subtotal' => $row['quantity'] * $row['unit_price']
                ];
            }
        }

        $con->close();
        
        // Reset array keys to be 0,1,2... for clean JSON
        return array_values($ordersMap);
    }

    /**
     * Updates order status (e.g., pending -> shipped)
     */
    public static function updateStatus($orderId, $newStatus) {
        $con = Database::connect();
        
        // Table: customer_order
        // Column: status
        $stmt = $con->prepare("UPDATE customer_order SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
        
        $success = $stmt->execute();
        
        $stmt->close();
        $con->close();
        
        return $success;
    }
}
?>