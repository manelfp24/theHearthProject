<?php
// 1. Output Buffering (Keeps HTML in memory)
ob_start();

// 2.iniciamos sesion
session_start();
// public/index.php
require_once '../controllers/ProductController.php';
require_once '../controllers/UserController.php';
require_once '../controllers/AdminController.php';
// ADD THIS LINE:
require_once '../controllers/CartController.php';

// Initialize cart count if it doesn't exist
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

// 3.incluimos bbdd
require_once '../config/Database.php';

// 4. ROUTER LOGIC (Moved up!)
// We need to know WHICH controller is requested BEFORE we decide to load the header
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'Home';
$actionName     = isset($_GET['action']) ? $_GET['action'] : 'index';

// CHECK: Is this an API request?
$isApi = ($controllerName === 'Api');

// 5. LOAD HEADER & NAVBAR (Only if NOT API)
if (!$isApi) {
    require_once '../views/layouts/header.php';
    require_once '../views/layouts/navbar.php';
}

// 6. EXECUTE CONTROLLER
$controllerClassName = ucfirst($controllerName) . 'Controller';
$controllerFile = '../controllers/' . $controllerClassName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    if (class_exists($controllerClassName)) {
        $controller = new $controllerClassName();
        if (method_exists($controller, $actionName)) {
            // Execute the action (e.g., get products)
            $controller->{$actionName}();
        } else {
            // Error handling (Only show HTML error if NOT API)
            if (!$isApi) echo "<div class='container py-5 text-white'>Error: Action not found.</div>";
        }
    } else {
        if (!$isApi) echo "<div class='container py-5 text-white'>Error: Class not found.</div>";
    }
} else {
    if (!$isApi) echo "<div class='container py-5 text-center text-white'><h1>404</h1><p>Page not found</p></div>";
}

// 7. LOAD FOOTER (Only if NOT API)
if (!$isApi) {
    require_once '../views/layouts/footer.php';
}

// 8. Flush Buffer
ob_end_flush();
?>