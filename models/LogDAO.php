<?php
// cargamos la conexión a la base de datos
require_once __DIR__ . '/../config/database.php';

class LogDAO {

    // esta función sirve para obtener todos los movimientos registrados en el panel
    // los ordena por fecha, mostrando primero lo más reciente
    public static function getAllLogs() {
        // nos conectamos a la base de datos
        $con = Database::connect();
        
        // hacemos un join para unir la tabla de logs con la de usuarios
        // así podemos saber el nombre del administrador que hizo la acción
        $sql = "SELECT 
                    l.log_id, 
                    l.action, 
                    l.affected_entity, 
                    l.timestamp,
                    u.name as admin_name
                FROM admin_log l
                LEFT JOIN user u ON l.admin_user = u.user_id
                ORDER BY l.timestamp DESC";

        // ejecutamos la consulta y preparamos el array para guardar los datos
        $result = $con->query($sql);
        $logs = [];

        // si la consulta tiene éxito, vamos guardando cada fila en el array
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
        }

        // cerramos la conexión para no dejar procesos abiertos
        $con->close();
        // devolvemos la lista de registros completa
        return $logs;
    }

    // esta función sirve para guardar una nueva acción en el historial
    // se usa cada vez que un admin borra, crea o cambia algo
    public static function logAction($adminId, $action, $entity) {
        // abrimos la conexión con la base de datos
        $con = Database::connect();
        
        // preparamos la inserción para evitar problemas de seguridad
        $stmt = $con->prepare("INSERT INTO admin_log (admin_user, action, affected_entity) VALUES (?, ?, ?)");
        // vinculamos los parámetros: id del admin, nombre de la acción y el objeto afectado
        $stmt->bind_param("iss", $adminId, $action, $entity);
        
        // ejecutamos la grabación y cerramos todo
        $stmt->execute();
        $stmt->close();
        $con->close();
    }
}
?>