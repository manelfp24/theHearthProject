<?php
include_once __DIR__ . '/../config/Database.php';
include_once __DIR__ . '/../models/User.php';

class UserDAO {

    //buscamos un usuario en la bbdd por su mail
    //y devuelve un objeto User si lo encuentra, sino NULL
    public static function getUserByEmail($email) {
        $con = Database::connect();
        
        // Preparamos query
        $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        
        // la ejecutamos
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Cogemos el objeto con fetch
        // y mapea las columnas de la bbdd automaticamente para la clase USer
        $user = $result->fetch_object('User');
        
        $con->close();
        
        return $user; // devuelve el objeto User o false/null
    }
    //registramos nuevo usuario
     // devuelve TRUE si va bien, FALSE si no (ej: email ya existe)
    public static function insertUser($name, $last_name, $email, $password_hash, $phone) {
        $con = Database::connect();
        
        // el rol es 'customer' a caso que un admin lo cambie
        $role = 'customer';

        $stmt = $con->prepare("INSERT INTO user (name, last_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $last_name, $email, $password_hash, $phone, $role);
        
        // Ejecutamos y comprobamos success
        $success = $stmt->execute();
        
        $con->close();
        return $success;
    }
}
?>