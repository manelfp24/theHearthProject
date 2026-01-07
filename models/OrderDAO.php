<?php
// models/OrderDAO.php
require_once __DIR__ . '/../config/Database.php';

class OrderDAO {
    
    /**
     * Creates an order and its details in the database.
     * Returns the new Order ID or False.
     */
    // models/OrderDAO.php

    /**
     * UPDATED: Now accepts Coupon ID and Discount Amount
     */
    public static function createOrder($userId, $cartItems, $totalPrice, $couponId = null, $discountAmount = 0.00) {
        $con = Database::connect();
        $con->begin_transaction();

        try {
            // Calculate Subtotal (Total Price + Discount Amount)
            // Logic: Final Price = Subtotal - Discount
            // So: Subtotal = Final Price + Discount
            $subtotal = $totalPrice + $discountAmount;

            // 1. Insert HEADER with Coupon and Discount info
            // Added: coupon_used_id, total_discount
            $stmt = $con->prepare("INSERT INTO customer_order 
                (user_id, coupon_used_id, subtotal, total_discount, total_price, status, order_date) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
            
            // Types: i (int), i (int), d (double), d (double), d (double)
            $stmt->bind_param("iiddd", $userId, $couponId, $subtotal, $discountAmount, $totalPrice);
            
            $stmt->execute();
            $orderId = $con->insert_id;
            $stmt->close();

            // 2. Insert LINES (Same as before)
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
            return false; // Or throw $e for debugging
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