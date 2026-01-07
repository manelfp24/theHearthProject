<?php
// Carga el DAO ara acceder a la logica de la bbdd
include_once __DIR__ . '/../models/ProductDAO.php';

class ProductController {

    //funcion para controlar el Menu
    public function index() {
        // Pedir al DAO la lista de todos los productos
        // y devuelve un array de objetos tipo producto
        $allProducts = ProductDAO::getAllProducts();

        // enviamos la lista a la vista. la lista usará $allProducts en vez de la consulta sql
        include __DIR__ . '/../views/menu/index.php';
    }
}
?>