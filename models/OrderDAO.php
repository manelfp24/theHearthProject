<?php
// models/OrderDAO.php
require_once __DIR__ . '/../config/Database.php';

class OrderDAO {
    
    /**
     * Creates an order and its details in the database.
     * Returns the new Order ID or False.
     */
    public static function createOrder($userId, $cartItems, $totalPrice) {
        $con = Database::connect();
        
        // 1. START TRANSACTION
        $con->begin_transaction();

        try {
            // 2. Insert the HEADER (customer_order)
            // Note: table_number is NULL for online orders usually, or we could ask for it.
            // status default is 'pending'
            $stmt = $con->prepare("INSERT INTO customer_order (user_id, subtotal, total_price, status, order_date) VALUES (?, ?, ?, 'pending', NOW())");
            
            // Assuming no discount for now, so subtotal = total
            $stmt->bind_param("idd", $userId, $totalPrice, $totalPrice);
            $stmt->execute();
            
            $orderId = $con->insert_id;
            $stmt->close();

            // 3. Insert the LINES (order_line)
            $stmtLine = $con->prepare("INSERT INTO order_line (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            
            foreach ($cartItems as $item) {
                $prod = $item['product'];
                $qty = $item['quantity'];
                $price = $prod->getBasePrice(); // Get price from object to be safe
                $prodId = $prod->getProductId();

                $stmtLine->bind_param("iiid", $orderId, $prodId, $qty, $price);
                $stmtLine->execute();
            }
            $stmtLine->close();

            // 4. COMMIT (Save changes)
            $con->commit();
            $con->close();
            return $orderId;

        } catch (Exception $e) {
            // 5. ROLLBACK (Undo everything if error)
            $con->rollback();
            $con->close();
            return false;
        }
    }

    /**
     * 1. GET ITEMS: Retrieves the list of items from the user's last order.
     * Returns an empty array [] if no previous order exists (prevents crashes).
     */
    public static function getMostRecentOrderItems($userId) {
        $con = Database::connect();
        
        // This query finds the latest order_id for the user, 
        // then grabs all the items (lines) belonging to that ID.
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
        
        if (!$stmt) {
            // Debugging: If query fails (e.g., table name typo), return empty to avoid crash
            return [];
        }

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

    /**
     * 2. CHECK EXISTENCE: Checks if the user has any previous orders.
     * Helpful for deciding whether to show or hide the button (Point 2).
     */
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
}
?>