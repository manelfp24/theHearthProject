<?php
// configuración básica de la url del proyecto
define('BASE_URL', 'http://localhost/DAW2/thehearth/public/');

class Database {
    // función para establecer la conexión con los datos de xampp
    public static function connect($host = 'localhost', $user = 'root', $pass = '', $db = 'thehearth') {
        
        // creamos la conexión con la base de datos mysql
        $con = new mysqli($host, $user, $pass, $db);

        // revisamos si la conexión ha dado algún error para frenar la web
        if ($con->connect_error) {
            // si falla, cortamos todo y avisamos del error
            die('Error al conectar a la BD');
        }

        // forzamos que use utf8 para que funcionen bien las tildes y las eñes
        $con->set_charset("utf8");

        // devolvemos la conexión lista para ser usada en los daos
        return $con;
    }
}
?>