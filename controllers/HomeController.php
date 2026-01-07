<?php
class HomeController {
    public function index() {
        // La ruta es relativa desde 'public/index.php'
        include '../views/home/index.php'; 
    }
}
?>