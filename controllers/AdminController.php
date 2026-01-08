<?php

class AdminController {

    // el constructor se ejecuta siempre al inicio para proteger la zona privada
    public function __construct() {
        // comprobamos si el usuario tiene permiso de administrador en la sesión
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            // si no es admin, lo redirigimos fuera para que no pueda cotillear
            header("Location: index.php?controller=User&action=login");
            exit();
        }
    }

    // función para cargar la vista del panel de control principal
    // se accede mediante la url: index.php?controller=Admin&action=dashboard
    public function dashboard() {
        // incluimos el archivo html/php que contiene el diseño del dashboard
        include __DIR__ . '/../views/panel/dashboard.php';
    }
}
?>