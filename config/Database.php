<?php
// CONEXIÓN A LA BASE DE DATOS
 define('BASE_URL', 'http://localhost/DAW2/thehearth/public/');
class Database {
    // Definimos los parámetros por defecto para XAMPP
    public static function connect($host = 'localhost', $user = 'root', $pass = '', $db = 'thehearth') {
        
        // Creamos la instancia de mysqli
        $con = new mysqli($host, $user, $pass, $db);

        // Verificamos si hubo error en la conexión
        if ($con->connect_error) {
            die('Error al conectar a la BD');
        }

        //codificación UTF-8 para que se vean acentos y símbolos
        $con->set_charset("utf8");

        return $con;
    }
}
?>