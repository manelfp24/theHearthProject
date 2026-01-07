<?php

class Product {
    private $product_id;
    private $name;
    private $description;
    private $base_price;
    private $product_type; //Meats, Wines, etc.
    private $image;
    private $available;
    private $is_featured;

    // Constructor
    public function __construct() { }

    // --- GETTERS ---
    public function getProductId() { return $this->product_id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; } // <--- NEW GETTER
    public function getBasePrice() { return $this->base_price; }
    public function getProductType() { return $this->product_type; }
    public function getImage() { return $this->image; }
    public function getAvailable() { return $this->available; }

    // --- SETTERS ---
    public function setProductId($product_id) { $this->product_id = $product_id; }
    public function setName($name) { $this->name = $name; }
    public function setDescription($description) { $this->description = $description; } // <--- NEW SETTER
    public function setBasePrice($base_price) { $this->base_price = $base_price; }
    public function setProductType($product_type) { $this->product_type = $product_type; }
    public function setImage($image) { $this->image = $image; }
    public function setAvailable($available) { $this->available = $available; }
}
?>