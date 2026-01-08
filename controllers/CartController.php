<?php
// controladores/CartController.php

// cargamos los modelos necesarios para productos, pedidos y cupones
require_once '../models/ProductDAO.php';
require_once '../models/OrderDAO.php'; 

class CartController {

    // al crear el objeto, nos aseguramos de que la sesión esté iniciada
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // añade un producto al carrito desde la tienda
    public function add() {
        ob_clean(); 
        // leemos los datos que vienen del javascript en formato json
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = isset($input['id']) ? (int)$input['id'] : 0;
        $quantity  = isset($input['quantity']) ? (int)$input['quantity'] : 1;
        
        // si el id no es válido, avisamos del error
        if ($productId <= 0) { echo json_encode(['success' => false, 'message' => 'Invalid ID']); exit(); }
        
        // preparamos el carrito en la sesión si todavía no existe
        if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
        
        // si el producto ya estaba, sumamos la cantidad; si no, lo añadimos
        if (isset($_SESSION['cart'][$productId])) { $_SESSION['cart'][$productId] += $quantity; } 
        else { $_SESSION['cart'][$productId] = $quantity; }
        
        // calculamos el total de objetos para actualizar el icono de la cesta
        $totalItems = array_sum($_SESSION['cart']);
        $_SESSION['cart_count'] = $totalItems;
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'newCount' => $totalItems]);
        exit();
    }

    // muestra la página principal del carrito con todos los detalles
    public function index() {
        // obtenemos los ids de los productos guardados en la sesión
        $cartIds = isset($_SESSION['cart']) ? array_keys($_SESSION['cart']) : [];
        
        $cartItems = [];
        $cartTotal = 0;

        // recorremos los ids para sacar la información real de la base de datos
        foreach ($cartIds as $id) {
            $product = ProductDAO::getProductById($id);
            
            if ($product) {
                $qty = $_SESSION['cart'][$id];
                $lineTotal = $product->getBasePrice() * $qty;
                
                // guardamos la info procesada en un array para la vista
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'line_total' => $lineTotal
                ];
                
                // vamos sumando el total acumulado del carrito
                $cartTotal += $lineTotal;
            }
        }

        // lógica para aplicar descuentos si hay un cupón activo
        $discountAmount = 0;
        $finalTotal = $cartTotal;
        
        if (isset($_SESSION['applied_coupon'])) {
            $coupon = $_SESSION['applied_coupon'];
            // calculamos si el descuento es por porcentaje o cantidad fija
            if ($coupon['type'] == 'percentage') {
                $discountAmount = $cartTotal * ($coupon['value'] / 100);
            } else {
                $discountAmount = $coupon['value'];
            }
            $finalTotal = $cartTotal - $discountAmount;
        }

        // comprobamos si el usuario tiene pedidos antiguos para mostrar el botón de repetir
        $hasPreviousOrder = false;
        if (isset($_SESSION['user_id'])) {
            $hasPreviousOrder = OrderDAO::hasPreviousOrder($_SESSION['user_id']);
        }

        // cargamos el archivo de la vista para mostrar el carrito al usuario
        require_once '../views/cart/index.php'; 
    }

    // permite subir o bajar la cantidad de un objeto desde el carrito
    public function update_quantity() {
        if (ob_get_length()) ob_clean();
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;
        $change = isset($input['change']) ? (int)$input['change'] : 0;
        
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $change;
            // si la cantidad llega a cero o menos, quitamos el producto
            if ($_SESSION['cart'][$id] <= 0) { unset($_SESSION['cart'][$id]); }
            $_SESSION['cart_count'] = array_sum($_SESSION['cart']);
            echo json_encode(['success' => true]);
        } else { echo json_encode(['success' => false, 'message' => 'Item not found']); }
        exit();
    }

    // elimina un producto del carrito completamente
    public function remove() {
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

    // muestra la página de confirmación de dirección y pago
    public function checkout() {
        // si el carrito está vacío, no dejamos entrar y volvemos a la tienda
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: index.php?controller=Product");
            exit();
        }

        // cargamos la vista del formulario de pago
        require_once '../views/cart/checkout.php';
    }

    // procesa el pedido final y lo guarda en la base de datos
    public function processOrder() {
        require_once '../models/OrderDAO.php';

        // comprobación de seguridad para evitar pedidos vacíos
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: index.php?controller=Product");
            exit();
        }

        $cartIds = array_keys($_SESSION['cart']);
        $cartItems = [];
        $calculatedSubtotal = 0; 

        // preparamos los productos para guardarlos en la base de datos
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

        // inicializamos las variables del cupón por si no se usa ninguno
        $couponId = null;        
        $discountAmount = 0.00;  
        $finalPrice = $calculatedSubtotal;

        // aplicamos los descuentos del cupón al precio final si existen
        if (isset($_SESSION['applied_coupon'])) {
             $coupon = $_SESSION['applied_coupon'];
             $couponId = $coupon['id']; 

             if ($coupon['type'] == 'percentage') {
                 $discountAmount = $calculatedSubtotal * ($coupon['value'] / 100);
             } else {
                 $discountAmount = $coupon['value'];
             }
             $finalPrice = max(0, $calculatedSubtotal - $discountAmount);
        }

        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // mandamos toda la info al dao para crear el registro en la base de datos
        $orderId = OrderDAO::createOrder($userId, $cartItems, $finalPrice, $couponId, $discountAmount);

        if ($orderId) {
            // limpiamos el carrito y el cupón de la sesión tras el éxito
            unset($_SESSION['cart']);
            unset($_SESSION['cart_count']);
            unset($_SESSION['applied_coupon']); 
            
            $_SESSION['last_order_id'] = $orderId;
            
            // vamos a la página de agradecimiento
            header("Location: index.php?controller=Cart&action=success");
            exit();
        } else {
            echo "Error processing order. Please try again.";
        }
    }

    // muestra el mensaje de que el pedido se ha realizado correctamente
    public function success() {
        require_once '../views/cart/success.php';
    }

    // recupera los productos del último pedido y los mete al carrito actual
    public function repeatLastOrder() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=User&action=login");
            exit();
        }
    
        $userId = $_SESSION['user_id'];
        
        // buscamos los productos del pedido más reciente de este usuario
        $lastOrderItems = OrderDAO::getMostRecentOrderItems($userId);
    
        if (!empty($lastOrderItems)) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
    
            foreach ($lastOrderItems as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
    
                // si ya estaban en el carrito, sumamos la cantidad antigua a la nueva
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = $quantity;
                }
            }
            
            // actualizamos el contador visual del carrito
            $_SESSION['cart_count'] = array_sum($_SESSION['cart']);
        }
    
        header("Location: index.php?controller=Cart");
        exit();
    }

    // verifica y activa un cupón de descuento mediante ajax
    public function applyCoupon() {
        require_once '../models/DiscountDAO.php';
        
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $code = isset($input['code']) ? $input['code'] : '';

        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a code']);
            exit();
        }

        // comprobamos en la base de datos si el código es válido
        $discount = DiscountDAO::getValidDiscount($code);

        if (!$discount) {
            echo json_encode(['success' => false, 'message' => 'Invalid coupon']);
            exit();
        }

        // guardamos la info del cupón en la sesión para el checkout
        $_SESSION['applied_coupon'] = [
            'id' => $discount['discount_code_id'],
            'code' => $discount['code'],
            'value' => (float)$discount['discount_value'],
            'type' => $discount['discount_type'] 
        ];

        // recalculamos los totales para responder al javascript rápidamente
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

        // enviamos la respuesta de éxito y los nuevos precios calculados
        echo json_encode([
            'success' => true, 
            'message' => 'Coupon applied!',
            'newTotal' => number_format($finalTotal, 2),
            'discountAmount' => number_format($discountAmount, 2)
        ]);
        exit();
    }
    
    // permite al usuario quitar el cupón si decide no usarlo
    public function removeCoupon() {
        if (isset($_SESSION['applied_coupon'])) {
            unset($_SESSION['applied_coupon']);
        }
        header("Location: index.php?controller=Cart");
        exit();
    }
}
?>