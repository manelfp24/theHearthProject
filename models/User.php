<?php

class User {
    // definimos las propiedades privadas que representan los datos del usuario en la base de datos
    private $user_id;
    private $name;
    private $last_name;
    private $email;
    private $password; // guardo aqui la contraseña encriptada para mayor seguridad
    private $phone;
    private $role;     // distingue si el usuario es 'admin' o 'customer'

    // constructor vacío para permitir que el dao llene el objeto después de la consulta
    public function __construct() {}

    // GETTERS
    public function getUserId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getLastName() { return $this->last_name; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getPhone() { return $this->phone; }
    public function getRole() { return $this->role; }

    // SETTERS
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setName($name) { $this->name = $name; }
    public function setLastName($last_name) { $this->last_name = $last_name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setRole($role) { $this->role = $role; }
}
?>