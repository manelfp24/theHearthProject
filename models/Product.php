<?php

class Product {
    // definimos las propiedades privadas que representan las columnas de la tabla en la base de datos
    private $product_id;
    private $name;
    private $description;
    private $base_price;
    private $product_type; // guarda la categoría: carnes, vinos, etc.
    private $image;
    private $available;
    private $is_featured;

    // constructor vacío para que php pueda instanciar el objeto antes de llenarlo
    public function __construct() { }

    // GETTERS
    public function getProductId() { return $this->product_id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; } 
    public function getBasePrice() { return $this->base_price; }
    public function getProductType() { return $this->product_type; }
    public function getImage() { return $this->image; }
    public function getAvailable() { return $this->available; }

    // SETTERS
    public function setProductId($product_id) { $this->product_id = $product_id; }
    public function setName($name) { $this->name = $name; }
    public function setDescription($description) { $this->description = $description; } 
    public function setBasePrice($base_price) { $this->base_price = $base_price; }
    public function setProductType($product_type) { $this->product_type = $product_type; }
    public function setImage($image) { $this->image = $image; }
    public function setAvailable($available) { $this->available = $available; }
}
?>