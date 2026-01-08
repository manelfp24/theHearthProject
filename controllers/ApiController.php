<?php
// Cargamos DAO
require_once __DIR__ . '/../models/ProductDAO.php';
require_once __DIR__ . '/../models/OrderDAO.php';

class ApiController {

    public function __construct() {
        // Solo los admins pueden acceder a la API
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
    }

    // URL: index.php?controller=Api&action=products
    public function products() {
        $products = ProductDAO::getAllProducts();
        
        // Metemos los objetos en un array para el JSON
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

        if ($id) {
            // --- UPDATE EXISTING ---
            $success = ProductDAO::update($id, $name, $description, $category, $price, $image);
            $message = "Product updated successfully";
        } else {
            // --- CREATE NEW ---
            $newId = ProductDAO::insert($name, $description, $category, $price, $image);
            $success = $newId ? true : false;
            $id = $newId; 
            $message = "Product created successfully";
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success, 
            'id' => $id,
            'message' => $message
        ]);
        exit();
    }

    // ---------------------------------------------------
    // NEW ADDITIONS FOR ORDERS DASHBOARD
    // ---------------------------------------------------

    /**
     * URL: index.php?controller=Api&action=orders
     * Returns all orders with their items
     */
    public function orders() {
        // We call the OrderDAO to get all orders (grouped by ID)
        $orders = OrderDAO::getAllOrdersWithItems();
        
        header('Content-Type: application/json');
        echo json_encode($orders);
        exit();
    }

    /**
     * URL: index.php?controller=Api&action=update_order_status
     * Method: POST
     * Payload: { "id": 123, "status": "shipped" }
     */
    public function update_order_status() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['id']) || !isset($input['status'])) {
            echo json_encode(['success' => false, 'message' => 'Missing ID or Status']);
            exit();
        }

        // Update the status in the DB
        $success = OrderDAO::updateStatus($input['id'], $input['status']);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }
}
?>