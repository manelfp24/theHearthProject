<?php

// cargamos los daos necesarios para interactuar con la base de datos
require_once __DIR__ . '/../models/ProductDAO.php';
require_once __DIR__ . '/../models/OrderDAO.php';
require_once __DIR__ . '/../models/LogDAO.php'; 

class ApiController {

    public function __construct() {
        // seguridad para que solo los administradores puedan usar estas funciones
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
    }

    // ---------------------------------------------------------
    // 1. productos
    // ---------------------------------------------------------

    // devuelve la lista de todos los productos en formato json
    public function products() {
        $products = ProductDAO::getAllProducts();
        
        $data = [];
        // recorremos los productos para preparar los datos exactos que necesita el js
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
    
    // elimina un producto recibiendo su id mediante json
    public function delete_product() {
        // leemos el contenido que llega en el cuerpo de la petición
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'No ID provided']);
            exit();
        }

        $isDeleted = ProductDAO::delete($input['id']);

        header('Content-Type: application/json');
        if ($isDeleted) {
            // guardamos el rastro de la eliminación en la tabla de logs
            $adminId = $_SESSION['user_id'] ?? 1; 
            LogDAO::logAction($adminId, 'Deleted Product', "Product ID: " . $input['id']);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not delete. It might be in an order.']);
        }
        exit();
    }

    // sirve tanto para crear un producto nuevo como para actualizar uno existente
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
            // si hay id, significa que estamos editando un producto que ya existe
            $success = ProductDAO::update($id, $name, $description, $category, $price, $image);
            $message = "Product updated successfully";
            
            // registramos el cambio en el historial de logs
            if($success) {
                LogDAO::logAction($adminId, 'Updated Product', "Product: $name (ID: $id)");
            }
        } else {
            // si no hay id, creamos un registro nuevo en la base de datos
            $newId = ProductDAO::insert($name, $description, $category, $price, $image);
            $success = $newId ? true : false;
            $id = $newId; 
            $message = "Product created successfully";

            // registramos la creación en los logs
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
    // 2. pedidos
    // ---------------------------------------------------------

    // obtiene todos los pedidos incluyendo los productos que hay dentro de cada uno
    public function orders() {
        $orders = OrderDAO::getAllOrdersWithItems();
        
        header('Content-Type: application/json');
        echo json_encode($orders);
        exit();
    }

    // cambia el estado de un pedido (ej: de pendiente a enviado)
    public function update_order_status() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['id']) || !isset($input['status'])) {
            echo json_encode(['success' => false, 'message' => 'Missing ID or Status']);
            exit();
        }

        $success = OrderDAO::updateStatus($input['id'], $input['status']);

        // guardamos en los logs quién ha cambiado el estado del pedido
        if($success) {
            $adminId = $_SESSION['user_id'] ?? 1;
            LogDAO::logAction($adminId, 'Updated Order Status', "Order #{$input['id']} changed to {$input['status']}");
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    // ---------------------------------------------------------
    // 3. historial (logs)
    // ---------------------------------------------------------

    // devuelve todos los registros de acciones realizadas por los administradores
    public function logs() {
        $logs = LogDAO::getAllLogs();
        header('Content-Type: application/json');
        echo json_encode($logs);
        exit();
    }

    // ---------------------------------------------------------
    // 4. usuarios
    // ---------------------------------------------------------

    // lista todos los usuarios registrados en el sistema
    public function users() {
        $users = UserDAO::getAllUsers();
        header('Content-Type: application/json');
        echo json_encode($users);
        exit();
    }

    // permite al admin cambiar el rango de un usuario (ej: pasar de cliente a admin)
    public function update_user_role() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['id']) || !isset($input['role'])) {
            echo json_encode(['success' => false]);
            exit();
        }

        $success = UserDAO::updateRole($input['id'], $input['role']);
        
        // registramos el cambio de rol en el historial de acciones
        if($success) {
            $adminId = $_SESSION['user_id'] ?? 1;
            LogDAO::logAction($adminId, 'Updated User Role', "User ID: {$input['id']} changed to {$input['role']}");
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }
}
?>