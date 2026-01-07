<?php
// controllers/CartController.php
require_once '../models/ProductDAO.php';
// We need OrderDAO available for the index to check history
require_once '../models/OrderDAO.php'; 

class CartController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ... [KEEP YOUR EXISTING add() FUNCTION HERE] ... 

    public function add() {
        // (Paste your existing add() code here exactly as it was)
        ob_clean(); 
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = isset($input['id']) ? (int)$input['id'] : 0;
        $quantity  = isset($input['quantity']) ? (int)$input['quantity'] : 1;
        if ($productId <= 0) { echo json_encode(['success' => false, 'message' => 'Invalid ID']); exit(); }
        if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
        if (isset($_SESSION['cart'][$productId])) { $_SESSION['cart'][$productId] += $quantity; } 
        else { $_SESSION['cart'][$productId] = $quantity; }
        $totalItems = array_sum($_SESSION['cart']);
        $_SESSION['cart_count'] = $totalItems;
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'newCount' => $totalItems]);
        exit();
    }

    /**
     * Action: Show the Cart Page
     */
    public function index() {
        // 1. Get keys (Product IDs) from session
        $cartIds = isset($_SESSION['cart']) ? array_keys($_SESSION['cart']) : [];
        
        $cartItems = [];
        $cartTotal = 0;

        // 2. Fetch full product details for each item
        foreach ($cartIds as $id) {
            $product = ProductDAO::getProductById($id);
            
            if ($product) {
                $qty = $_SESSION['cart'][$id];
                $lineTotal = $product->getBasePrice() * $qty;
                
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'line_total' => $lineTotal
                ];
                
                $cartTotal += $lineTotal;
            }
        }

        // ... (inside index method, after calculating $cartTotal) ...

        // Check if coupon is applied
        $discountAmount = 0;
        $finalTotal = $cartTotal;
        
        if (isset($_SESSION['applied_coupon'])) {
            $coupon = $_SESSION['applied_coupon'];
            if ($coupon['type'] == 'percentage') {
                $discountAmount = $cartTotal * ($coupon['value'] / 100);
            } else {
                $discountAmount = $coupon['value'];
            }
            $finalTotal = $cartTotal - $discountAmount;
        }


        // --- NEW LOGIC FOR RE-ORDER BUTTON ---
        // We check this separately so we can show the button even if cart is empty
        $hasPreviousOrder = false;
        if (isset($_SESSION['user_id'])) {
            // Check if this user has ordered before
            $hasPreviousOrder = OrderDAO::hasPreviousOrder($_SESSION['user_id']);
        }

        // 3. Load the View
        require_once '../views/cart/index.php'; 
    }

    // ... [KEEP YOUR update_quantity(), remove(), and checkout() HERE] ...
    
    public function update_quantity() {
        // (Paste your existing update_quantity code)
        if (ob_get_length()) ob_clean();
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;
        $change = isset($input['change']) ? (int)$input['change'] : 0;
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $change;
            if ($_SESSION['cart'][$id] <= 0) { unset($_SESSION['cart'][$id]); }
            $_SESSION['cart_count'] = array_sum($_SESSION['cart']);
            echo json_encode(['success' => true]);
        } else { echo json_encode(['success' => false, 'message' => 'Item not found']); }
        exit();
    }

    public function remove() {
        // (Paste your existing remove code)
        if (ob_get_length()) ob_clean();
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            $_SESSION['cart_count'] = array_sum($_SESSION['cart']);
            echo json_encode(['success' => true]);
        } else { echo json_encode(['success' => false]); }
        exit();
    }

    /**
     * Action: Show the Checkout Form (Address & Payment)
     */
    public function checkout() {
        // Security: Cart cannot be empty
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: index.php?controller=Product");
            exit();
        }

        // Just load the view
        require_once '../views/cart/checkout.php';
    }

    /**
     * Action: Handle the form submission and save to DB
     */
    // controllers/CartController.php

    public function processOrder() {
        require_once '../models/OrderDAO.php';

        // 1. Security Check
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: index.php?controller=Product");
            exit();
        }

        // 2. Prepare Items & Calculate Initial Subtotal
        $cartIds = array_keys($_SESSION['cart']);
        $cartItems = [];
        $calculatedSubtotal = 0; // This is the price BEFORE discount

        foreach ($cartIds as $id) {
            $product = ProductDAO::getProductById($id);
            if ($product) {
                $qty = $_SESSION['cart'][$id];
                $price = $product->getBasePrice();
                
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $qty
                ];
                $calculatedSubtotal += ($price * $qty);
            }
        }

        // 3. Handle Coupon Logic
        $couponId = null;        // Default: NULL in DB
        $discountAmount = 0.00;  // Default: 0.00
        $finalPrice = $calculatedSubtotal;

        if (isset($_SESSION['applied_coupon'])) {
             $coupon = $_SESSION['applied_coupon'];
             
             // Save the ID to send to DB
             $couponId = $coupon['id']; 

             // Calculate Discount
             if ($coupon['type'] == 'percentage') {
                 $discountAmount = $calculatedSubtotal * ($coupon['value'] / 100);
             } else {
                 $discountAmount = $coupon['value'];
             }
             
             // Calculate Final Price
             $finalPrice = max(0, $calculatedSubtotal - $discountAmount);
        }

        // 4. Get User Info
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // 5. Save to Database (Passing new variables!)
        // function createOrder($userId, $cartItems, $totalPrice, $couponId, $discountAmount)
        $orderId = OrderDAO::createOrder($userId, $cartItems, $finalPrice, $couponId, $discountAmount);

        if ($orderId) {
            // Cleanup Session
            unset($_SESSION['cart']);
            unset($_SESSION['cart_count']);
            unset($_SESSION['applied_coupon']); // Important: clear the used coupon
            
            $_SESSION['last_order_id'] = $orderId;
            
            header("Location: index.php?controller=Cart&action=success");
            exit();
        } else {
            // Optional: Log error or show message
            echo "Error processing order. Please try again.";
        }
    }

    public function success() {
        require_once '../views/cart/success.php';
    }

    // --- REPEAT ORDER FUNCTION ---
    public function repeatLastOrder() {
        // 1. Security check
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=User&action=login");
            exit();
        }
    
        $userId = $_SESSION['user_id'];
        
        // 2. Fetch the LAST order items using the STATIC method we made
        // Note: Using static :: call, not new OrderDAO()
        $lastOrderItems = OrderDAO::getMostRecentOrderItems($userId);
    
        if (!empty($lastOrderItems)) {
            
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
    
            foreach ($lastOrderItems as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
    
                // Logic to merge: Add to existing quantity if item is already in cart
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = $quantity;
                }
            }
            
            // Update count for the badge
            $_SESSION['cart_count'] = array_sum($_SESSION['cart']);
        }
    
        // 4. Redirect back to Cart view
        header("Location: index.php?controller=Cart");
        exit();
    }

    /**
     * Action: Apply Coupon (AJAX)
     */
    public function applyCoupon() {
        require_once '../models/DiscountDAO.php';
        
        // Clean buffer
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $code = isset($input['code']) ? $input['code'] : '';

        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a code']);
            exit();
        }

        // 1. Validate Code
        $discount = DiscountDAO::getValidDiscount($code);

        if (!$discount) {
            echo json_encode(['success' => false, 'message' => 'Invalid coupon']);
            exit();
        }

        // 2. Save to Session
        $_SESSION['applied_coupon'] = [
            'id' => $discount['discount_code_id'],
            'code' => $discount['code'],
            'value' => (float)$discount['discount_value'],
            'type' => $discount['discount_type'] // 'percentage' or 'fixed'
        ];

        // 3. Recalculate Totals immediately to send back
        // (Simplified calculation logic for the response)
        $cartTotal = 0;
        foreach ($_SESSION['cart'] as $id => $qty) {
            $prod = ProductDAO::getProductById($id);
            if ($prod) $cartTotal += $prod->getBasePrice() * $qty;
        }
        
        $discountAmount = 0;
        if ($discount['discount_type'] === 'percentage') {
            $discountAmount = $cartTotal * ($discount['discount_value'] / 100);
        } else {
            $discountAmount = $discount['discount_value'];
        }
        
        $finalTotal = max(0, $cartTotal - $discountAmount);

        echo json_encode([
            'success' => true, 
            'message' => 'Coupon applied!',
            'newTotal' => number_format($finalTotal, 2),
            'discountAmount' => number_format($discountAmount, 2)
        ]);
        exit();
    }
    
    /**
     * Action: Remove Coupon (Optional but recommended)
     */
    public function removeCoupon() {
        if (isset($_SESSION['applied_coupon'])) {
            unset($_SESSION['applied_coupon']);
        }
        header("Location: index.php?controller=Cart");
        exit();
    }
}
?>