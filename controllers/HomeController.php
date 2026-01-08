<?php
class HomeController {
    // esta función se encarga de cargar la página de bienvenida de la web
    public function index() {
        // incluimos el archivo de la vista que contiene el diseño de la home
        // la ruta se calcula desde la carpeta public que es donde entra el usuario
        include '../views/home/index.php'; 
    }
}
?>