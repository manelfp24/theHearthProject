<?php
// cargamos la configuración para conectar con la base de datos
require_once __DIR__ . '/../config/database.php';

class OrderDAO {
    
    // ---------------------------------------------------------
    // funciones para el proceso de compra y el perfil de usuario
    // ---------------------------------------------------------

    // registra un nuevo pedido en la base de datos procesando el carrito y los descuentos
    public static function createOrder($userId, $cartItems, $totalPrice, $couponId = null, $discountAmount = 0.00) {
        $con = Database::connect();
        // iniciamos una transacción para asegurar que se guarde todo o nada
        $con->begin_transaction();

        try {
            // calculamos el subtotal sumando el descuento al precio final
            $subtotal = $totalPrice + $discountAmount;

            // insertamos los datos generales en la tabla de pedidos principales
            $stmt = $con->prepare("INSERT INTO customer_order 
                (user_id, coupon_used_id, subtotal, total_discount, total_price, status, order_date) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
            
            $stmt->bind_param("iiddd", $userId, $couponId, $subtotal, $discountAmount, $totalPrice);
            
            $stmt->execute();
            // obtenemos el id generado para este pedido
            $orderId = $con->insert_id;
            $stmt->close();

            // insertamos cada producto del carrito en la tabla de líneas de pedido
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

            // si todo ha ido bien, confirmamos los cambios en la base de datos
            $con->commit();
            $con->close();
            return $orderId;

        } catch (Exception $e) {
            // si hay cualquier error, deshacemos todos los cambios para no dejar datos corruptos
            $con->rollback();
            $con->close();
            return false;
        }
    }

    // busca los productos del último pedido realizado por un usuario concreto
    public static function getMostRecentOrderItems($userId) {
        $con = Database::connect();
        
        // consulta con subquery para encontrar los artículos del pedido más nuevo
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
        // guardamos cada producto encontrado en un array
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        $stmt->close();
        $con->close();
        
        return $items; 
    }

    // comprueba rápidamente si un usuario ha realizado alguna compra anteriormente
    public static function hasPreviousOrder($userId) {
        $con = Database::connect();
        
        $stmt = $con->prepare("SELECT order_id FROM customer_order WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();
        
        // devolvemos verdadero si encontramos al menos una fila
        $exists = $stmt->num_rows > 0;
        
        $stmt->close();
        $con->close();
        
        return $exists;
    }

    // ---------------------------------------------------------
    // funciones nuevas para el panel de administración
    // ---------------------------------------------------------

    // obtiene todos los pedidos del sistema detallando los productos de cada uno
    public static function getAllOrdersWithItems() {
        $con = Database::connect();
        
        // unimos las tablas de pedidos, líneas y productos para tener toda la información
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

                // si es la primera vez que leemos este pedido, creamos su estructura base
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

                // añadimos los detalles del producto a la lista de artículos del pedido
                $ordersMap[$id]['items'][] = [
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity'],
                    'price' => $row['unit_price'],
                    'subtotal' => $row['quantity'] * $row['unit_price']
                ];
            }
        }

        $con->close();
        
        // devolvemos solo los valores para que el json sea una lista limpia
        return array_values($ordersMap);
    }

    // permite cambiar el estado de un pedido (por ejemplo, marcarlo como enviado)
    public static function updateStatus($orderId, $newStatus) {
        $con = Database::connect();
        
        // actualizamos la columna de estado filtrando por el id del pedido
        $stmt = $con->prepare("UPDATE customer_order SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
        
        $success = $stmt->execute();
        
        $stmt->close();
        $con->close();
        
        return $success;
    }

    // recupera un número determinado de pedidos recientes de un usuario específico
    public static function getLastOrdersByUser($userId, $limit = 3) {
        $con = Database::connect();
        
        $sql = "SELECT order_id, order_date, total_price, status 
                FROM customer_order 
                WHERE user_id = ? 
                ORDER BY order_date DESC 
                LIMIT ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        // llenamos el array con los resultados obtenidos
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        $stmt->close();
        $con->close();
        return $orders;
    }
}
?>