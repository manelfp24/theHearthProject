<?php
// models/DiscountDAO.php
require_once __DIR__ . '/../config/Database.php';

class DiscountDAO {
    
    public static function getValidDiscount($code) {
        $con = Database::connect();
        $code = trim($code);
        
        // Check for active code that hasn't expired
        $stmt = $con->prepare("SELECT * FROM discount_code WHERE code = ? AND is_active = 1 AND (expiration_date >= CURDATE() OR expiration_date IS NULL)");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $discount = $result->fetch_assoc(); // Returns null if not found
        
        $stmt->close();
        $con->close();
        
        return $discount;
    }
}
?>