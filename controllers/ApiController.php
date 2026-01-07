<?php
// Cargamos DAO
require_once __DIR__ . '/../models/ProductDAO.php';
// Añadiremos OrderDAO luego

class ApiController {

    public function __construct() {
        // Solo los admins pueden acceder a la API
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
    }

    
    //URL: index.php?controller=Api&action=products
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
                'available' => $p->getAvailable()
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    //Aquí añadiremos "orders" y "logs" mas tarde

    /**
     * Action: Delete a Product
     * Method: POST
     * URL: index.php?controller=Api&action=delete_product
     */
    public function delete_product() {
        // 1. Get the raw JSON input from JavaScript
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'No ID provided']);
            exit();
        }

        // 2. Call the DAO
        $isDeleted = ProductDAO::delete($input['id']);

        // 3. Respond
        header('Content-Type: application/json');
        if ($isDeleted) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not delete. It might be in an order.']);
        }
        exit();
    }
    /**
     * Action: Save Product (Create or Update)
     * Method: POST
     * URL: index.php?controller=Api&action=save_product
     */
    public function save_product() {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        $id = isset($input['id']) ? $input['id'] : null;
        $name = $input['name'];
        $description = $input['description'];
        $category = $input['category'];
        $price = $input['price'];
        $image = $input['image']; // In a real app, we would handle file uploads here

        if ($id) {
            // --- UPDATE EXISTING ---
            $success = ProductDAO::update($id, $name, $description, $category, $price, $image);
            $message = "Product updated successfully";
        } else {
            // --- CREATE NEW ---
            $newId = ProductDAO::insert($name, $description, $category, $price, $image);
            $success = $newId ? true : false;
            $id = $newId; // Return the new ID to the frontend
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
}
?>