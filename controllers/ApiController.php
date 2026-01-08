<?php
// Cargamos DAOs
require_once __DIR__ . '/../models/ProductDAO.php';
require_once __DIR__ . '/../models/OrderDAO.php';
require_once __DIR__ . '/../models/LogDAO.php'; // <--- Added LogDAO

class ApiController {

    public function __construct() {
        // Solo los admins pueden acceder a la API
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
    }

    // ---------------------------------------------------------
    // 1. PRODUCTS
    // ---------------------------------------------------------

    // URL: index.php?controller=Api&action=products
    public function products() {
        $products = ProductDAO::getAllProducts();
        
        $data = [];
        foreach ($products as $p) {
            $data[] = [
                'id' => $p->getProductId(),
                'name' => $p->getName(),
                'description' => $p->getDescription(),
                'price' => $p->getBasePrice(),
                'category' => $p->getProductType(),
                'available' => $p->getAvailable(),
                'image' => $p->getImage() 
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    // Action: Delete a Product
    public function delete_product() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'No ID provided']);
            exit();
        }

        $isDeleted = ProductDAO::delete($input['id']);

        header('Content-Type: application/json');
        if ($isDeleted) {
            // LOG THE ACTION
            $adminId = $_SESSION['user_id'] ?? 1; // Default to 1 if session missing
            LogDAO::logAction($adminId, 'Deleted Product', "Product ID: " . $input['id']);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not delete. It might be in an order.']);
        }
        exit();
    }

    // Action: Save Product (Create or Update)
    public function save_product() {
        $input = json_decode(file_get_contents('php://input'), true);

        $id = isset($input['id']) ? $input['id'] : null;
        $name = $input['name'];
        $description = $input['description'];
        $category = $input['category'];
        $price = $input['price'];
        $image = $input['image']; 

        $adminId = $_SESSION['user_id'] ?? 1;

        if ($id) {
            // --- UPDATE EXISTING ---
            $success = ProductDAO::update($id, $name, $description, $category, $price, $image);
            $message = "Product updated successfully";
            
            // LOG UPDATE
            if($success) {
                LogDAO::logAction($adminId, 'Updated Product', "Product: $name (ID: $id)");
            }
        } else {
            // --- CREATE NEW ---
            $newId = ProductDAO::insert($name, $description, $category, $price, $image);
            $success = $newId ? true : false;
            $id = $newId; 
            $message = "Product created successfully";

            // LOG CREATION
            if($success) {
                LogDAO::logAction($adminId, 'Created Product', "Product: $name");
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success, 
            'id' => $id,
            'message' => $message
        ]);
        exit();
    }

    // ---------------------------------------------------------
    // 2. ORDERS
    // ---------------------------------------------------------

    /**
     * URL: index.php?controller=Api&action=orders
     * Returns all orders with their items
     */
    public function orders() {
        $orders = OrderDAO::getAllOrdersWithItems();
        
        header('Content-Type: application/json');
        echo json_encode($orders);
        exit();
    }

    /**
     * URL: index.php?controller=Api&action=update_order_status
     */
    public function update_order_status() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['id']) || !isset($input['status'])) {
            echo json_encode(['success' => false, 'message' => 'Missing ID or Status']);
            exit();
        }

        // Update DB
        $success = OrderDAO::updateStatus($input['id'], $input['status']);

        // LOG STATUS CHANGE
        if($success) {
            $adminId = $_SESSION['user_id'] ?? 1;
            LogDAO::logAction($adminId, 'Updated Order Status', "Order #{$input['id']} changed to {$input['status']}");
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    // ---------------------------------------------------------
    // 3. LOGS (NEW SECTION)
    // ---------------------------------------------------------

    /**
     * URL: index.php?controller=Api&action=logs
     * Returns all admin logs
     */
    public function logs() {
        $logs = LogDAO::getAllLogs();
        header('Content-Type: application/json');
        echo json_encode($logs);
        exit();
    }
}
?>