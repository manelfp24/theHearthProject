<?php
// cargamos la configuración de la base de datos y la clase producto
include_once __DIR__ . '/../config/Database.php';
include_once __DIR__ . '/Product.php';

class ProductDAO {
    
    // obtiene todos los productos disponibles de la base de datos
    public static function getAllProducts() {
        // conectamos a la base de datos
        $con = Database::connect();
        
        // preparamos la consulta para traer solo los productos disponibles ordenados por nombre
        $stmt = $con->prepare("SELECT * FROM product WHERE available = 1 ORDER BY name ASC");
        
        // ejecutamos la petición
        $stmt->execute();
        $result = $stmt->get_result();
        
        // creamos una lista para guardar los objetos producto
        $productList = [];
        
        // usamos fetch_object para que cada fila se convierta automáticamente en un objeto de la clase product
        while ($product = $result->fetch_object('Product')) {
            $productList[] = $product;
        }
        
        // cerramos la conexión para ahorrar recursos
        $con->close();
        return $productList;
    }
    
    // recupera los productos destacados para mostrarlos en el carrusel según su categoría
    public static function getFeaturedProducts($category) {
        $con = Database::connect();
        
        // usamos el signo '?' para evitar inyecciones sql y mejorar la seguridad
        $stmt = $con->prepare("SELECT * FROM product WHERE product_type = ? AND is_featured = 1");
        $stmt->bind_param("s", $category); 
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $featuredList = [];
        while ($product = $result->fetch_object('Product')) {
            $featuredList[] = $product;
        }
        
        $con->close();
        return $featuredList;
    }

    // elimina un producto de la base de datos usando su identificador único
    public static function delete($id) {
        $con = Database::connect();
        
        $stmt = $con->prepare("DELETE FROM product WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        
        try {
            // intentamos borrar el registro
            $result = $stmt->execute();
            $con->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            // si el producto está en algún pedido activo, la base de datos dará error y devolvemos falso
            $con->close();
            return false;
        }
    }

    // inserta un producto nuevo en la carta incluyendo su descripción
    public static function insert($name, $description, $category, $price, $image) {
        $con = Database::connect();
        // preparamos la sentencia con los campos necesarios
        $stmt = $con->prepare("INSERT INTO product (name, description, product_type, base_price, image, available) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sssds", $name, $description, $category, $price, $image);
        
        $result = $stmt->execute();
        // obtenemos el id que la base de datos le ha asignado al nuevo producto
        $id = $con->insert_id; 
        $con->close();
        
        return $id;
    }

    // actualiza la información de un producto que ya existe en el sistema
    public static function update($id, $name, $description, $category, $price, $image) {
        $con = Database::connect();
        // modificamos los valores basándonos en el id del producto
        $stmt = $con->prepare("UPDATE product SET name=?, description=?, product_type=?, base_price=?, image=? WHERE product_id=?");
        $stmt->bind_param("sssdsi", $name, $description, $category, $price, $image, $id);
        
        $result = $stmt->execute();
        $con->close();
        
        return $result;
    }

    // busca y devuelve los datos de un solo producto a través de su id
    public static function getProductById($id) {
        $con = Database::connect();
        $stmt = $con->prepare("SELECT * FROM product WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $product = null;
        if ($row = $result->fetch_assoc()) {
            // creamos el objeto producto vacío
            $product = new Product();
            
            // llenamos el objeto manualmente usando los setters de la clase
            $product->setProductId($row['product_id']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $product->setProductType($row['product_type']);
            $product->setBasePrice((float)$row['base_price']); 
            $product->setImage($row['image']);
            $product->setAvailable($row['available']);
        }
        
        $stmt->close();
        $con->close();
        return $product;
    }
}
?>