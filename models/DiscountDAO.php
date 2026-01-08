<?php
// models/DiscountDAO.php
// cargamos la configuración de la base de datos para poder conectar
require_once __DIR__ . '/../config/Database.php';

class DiscountDAO {
    
    // función para buscar un cupón que sea válido y no haya caducado
    public static function getValidDiscount($code) {
        // conectamos con la base de datos
        $con = Database::connect();
        // limpiamos espacios en blanco del código que escribe el usuario
        $code = trim($code);
        
        // preparamos la consulta para buscar el código si está activo y en fecha
        // comprobamos que is_active sea 1 y que la fecha de hoy sea menor a la de caducidad
        $stmt = $con->prepare("SELECT * FROM discount_code WHERE code = ? AND is_active = 1 AND (expiration_date >= CURDATE() OR expiration_date IS NULL)");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // sacamos los datos del cupón en forma de array asociativo
        $discount = $result->fetch_assoc(); 
        
        // cerramos la sentencia y la conexión para liberar recursos
        $stmt->close();
        $con->close();
        
        // devolvemos el descuento encontrado o null si no existe o no vale
        return $discount;
    }
}
?>