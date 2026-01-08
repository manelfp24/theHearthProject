<?php
// incluimos el archivo del dao para poder usar las funciones que conectan con la base de datos
include_once __DIR__ . '/../models/ProductDAO.php';

class ProductController {

    // esta función gestiona la carga de la página del menú
    public function index() {
        // Llamamos al dao para obtener la lista completa de productos desde la base de datos
        // el resultado será un array donde cada elemento es un objeto de la clase producto
        $allProducts = ProductDAO::getAllProducts();

        // cargamos el archivo de la vista del menú para que el usuario pueda ver los productos
        // dentro de ese archivo se usará la variable $allProducts para pintar las cartas en el html
        include __DIR__ . '/../views/menu/index.php';
    }
}
?>