<?php
// activamos el almacenamiento en el búfer de salida para evitar errores de envío de cabeceras
ob_start();

// iniciamos o recuperamos la sesión actual del usuario
session_start();

// cargamos todos los controladores necesarios para que el router pueda usarlos
require_once '../controllers/ProductController.php';
require_once '../controllers/UserController.php';
require_once '../controllers/AdminController.php';
require_once '../controllers/CartController.php';

// si es la primera vez que entra el usuario, ponemos el contador del carrito a cero
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

// cargamos el archivo que permite conectar con la base de datos mysql
require_once '../config/Database.php';

// lógica del enrutador: decidimos qué página mostrar basándonos en la url
// si no se pide nada, por defecto cargamos el controlador home y la acción index
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'Home';
$actionName     = isset($_GET['action']) ? $_GET['action'] : 'index';

// comprobamos si lo que se está pidiendo es una respuesta de la api
$isApi = ($controllerName === 'Api');

// cargamos la cabecera y el menú de navegación solo si el usuario está viendo la web (no en la api)
if (!$isApi) {
    require_once '../views/layouts/header.php';
    require_once '../views/layouts/navbar.php';
}

// construimos el nombre de la clase del controlador (ej: productcontroller)
$controllerClassName = ucfirst($controllerName) . 'Controller';
$controllerFile = '../controllers/' . $controllerClassName . '.php';

// verificamos que el archivo del controlador solicitado exista en nuestra carpeta
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // si la clase existe, creamos el objeto y ejecutamos la función (acción) solicitada
    if (class_exists($controllerClassName)) {
        $controller = new $controllerClassName();
        if (method_exists($controller, $actionName)) {
            // ejecutamos la lógica (ej: mostrar productos o procesar login)
            $controller->{$actionName}();
        } else {
            // error si la función no existe dentro del controlador
            if (!$isApi) echo "<div class='container py-5 text-white'>Error: Action not found.</div>";
        }
    } else {
        // error si la clase no se ha definido correctamente
        if (!$isApi) echo "<div class='container py-5 text-white'>Error: Class not found.</div>";
    }
} else {
    // si el archivo no existe, mostramos un error 404 de página no encontrada
    if (!$isApi) echo "<div class='container py-5 text-center text-white'><h1>404</h1><p>Page not found</p></div>";
}

// cargamos el pie de página solo si no es una petición de datos api
if (!$isApi) {
    require_once '../views/layouts/footer.php';
}

// enviamos todo el contenido acumulado al navegador del usuario
ob_end_flush();
?>