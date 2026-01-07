<?php

class AdminController {

    // Antes de dejar entrar a nadie, verificamos que sea realmente ADMIN
    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            // Si no es admin, lo mandamos al login
            header("Location: index.php?controller=User&action=login");
            exit();
        }
    }

    //Dashboard principal
    //URL: index.php?controller=Admin&action=dashboard
    public function dashboard() {
        include __DIR__ . '/../views/panel/dashboard.php';
    }
}
?>