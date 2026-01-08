<?php
// incluimos la conexión a la base de datos y la clase usuario
include_once __DIR__ . '/../config/Database.php';
include_once __DIR__ . '/../models/User.php';

class UserDAO {

    // busca un usuario por su correo electrónico para el login
    // devuelve un objeto user si lo encuentra o null si no existe
    public static function getUserByEmail($email) {
        // abrimos la conexión con la base de datos
        $con = Database::connect();
        
        // preparamos la consulta para evitar ataques
        $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        
        // ejecutamos la búsqueda
        $stmt->execute();
        $result = $stmt->get_result();
        
        // convertimos el resultado directamente en un objeto de la clase user
        $user = $result->fetch_object('User');
        
        $con->close();
        
        return $user; 
    }

    // registra un nuevo cliente en el sistema
    // por defecto se le asigna el rol de cliente (customer)
    public static function insertUser($name, $last_name, $email, $password_hash, $phone) {
        $con = Database::connect();
        
        // valor predeterminado para nuevos registros
        $role = 'customer';

        // insertamos todos los campos en la tabla de usuarios
        $stmt = $con->prepare("INSERT INTO user (name, last_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $last_name, $email, $password_hash, $phone, $role);
        
        // ejecutamos y devolvemos si la operación ha tenido éxito
        $success = $stmt->execute();
        
        $con->close();
        return $success;
    }

    // obtiene los datos de un usuario a partir de su id único
    public static function getUserById($id) {
        $con = Database::connect();
        $stmt = $con->prepare("SELECT * FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        // devuelve el usuario como un objeto genérico
        $user = $result->fetch_object(); 
        
        $stmt->close();
        $con->close();
        
        return $user;
    }

    // permite actualizar el nombre y el email desde el perfil del usuario
    public static function updateUser($id, $name, $email) {
        $con = Database::connect();
        
        // ejecutamos el cambio filtrando por el id del usuario conectado
        $stmt = $con->prepare("UPDATE user SET name = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);
        
        $success = $stmt->execute();
        
        $stmt->close();
        $con->close();
        
        return $success;
    }

    // devuelve una lista de todos los usuarios registrados para el panel de administración
    public static function getAllUsers() {
        $con = Database::connect();
        // sacamos los datos básicos ordenados por su id
        $sql = "SELECT user_id, name, email, role FROM user ORDER BY user_id ASC";
        $result = $con->query($sql);
        $users = [];
        
        // guardamos cada usuario en un array asociativo
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $con->close();
        return $users;
    }

    // permite a un administrador cambiar el rol de un usuario (ej: hacerlo admin)
    public static function updateRole($id, $newRole) {
        $con = Database::connect();
        $stmt = $con->prepare("UPDATE user SET role = ? WHERE user_id = ?");
        $stmt->bind_param("si", $newRole, $id);
        
        $success = $stmt->execute();
        $stmt->close();
        $con->close();
        return $success;
    }
}
?>