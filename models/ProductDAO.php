<?php
// Cargamos header y navbar
include_once __DIR__ . '/../config/Database.php';
include_once __DIR__ . '/Product.php';

class ProductDAO {
    
    //cogemos los productos de la base de datos
    public static function getAllProducts() {
        // Conectamos a la bbdd
        $con = Database::connect();
        
        // preparamos la consulta sql
        // filtramos por available y nombre
        $stmt = $con->prepare("SELECT * FROM product WHERE available = 1 ORDER BY name ASC");
        
        // ejecutamos
        $stmt->execute();
        $result = $stmt->get_result();
        
        //guardamos los resultados en un array
        $productList = [];
        
        // con 'fetch_object' creamos instancias de la clase producto y 
        //llenamos sus propiedades (product_id, name, etc.)
        while ($product = $result->fetch_object('Product')) {
            $productList[] = $product;
        }
        
        $con->close();
        return $productList;
    }
    
    //hacemos fetch de los productos del carrousel (is_featured)
    public static function getFeaturedProducts($category) {
        $con = Database::connect();
        
        // usamos'?' placeholders para la segurirdad
        $stmt = $con->prepare("SELECT * FROM product WHERE product_type = ? AND is_featured = 1");
        $stmt->bind_param("s", $category); // "s" significa string
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $featuredList = [];
        while ($product = $result->fetch_object('Product')) {
            $featuredList[] = $product;
        }
        
        $con->close();
        return $featuredList;
    }

    /**
     * Deletes a product by ID.
     * Returns TRUE if successful, FALSE if it fails (e.g. foreign key constraint).
     */
    public static function delete($id) {
        $con = Database::connect();
        
        $stmt = $con->prepare("DELETE FROM product WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        
        try {
            $result = $stmt->execute();
            $con->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            // If it fails (e.g. product is in an active order), return false
            $con->close();
            return false;
        }
    }
    /**
     * Inserts a new product (Now includes Description).
     */
    public static function insert($name, $description, $category, $price, $image) {
        $con = Database::connect();
        // Added 'description' column and placeholder '?'
        $stmt = $con->prepare("INSERT INTO product (name, description, product_type, base_price, image, available) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sssds", $name, $description, $category, $price, $image);
        
        $result = $stmt->execute();
        $id = $con->insert_id; 
        $con->close();
        
        return $id;
    }

    /**
     * Updates an existing product (Now includes Description).
     */
    public static function update($id, $name, $description, $category, $price, $image) {
        $con = Database::connect();
        // Added 'description=?' to the SET clause
        $stmt = $con->prepare("UPDATE product SET name=?, description=?, product_type=?, base_price=?, image=? WHERE product_id=?");
        $stmt->bind_param("sssdsi", $name, $description, $category, $price, $image, $id);
        
        $result = $stmt->execute();
        $con->close();
        
        return $result;
    }

    /**
     * Fetch a single product by ID.
     * Uses Setters to ensure data is loaded correctly.
     */
    public static function getProductById($id) {
        $con = Database::connect();
        $stmt = $con->prepare("SELECT * FROM product WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $product = null;
        if ($row = $result->fetch_assoc()) {
            // 1. Create empty object
            $product = new Product();
            
            // 2. Manually fill it using Setters
            $product->setProductId($row['product_id']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $product->setProductType($row['product_type']);
            $product->setBasePrice((float)$row['base_price']); // Force float type
            $product->setImage($row['image']);
            $product->setAvailable($row['available']);
        }
        
        $stmt->close();
        $con->close();
        return $product;
    }
}
?>