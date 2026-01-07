<?php

class User {
    private $user_id;
    private $name;
    private $last_name;
    private $email;
    private $password; // guardo aqui la contraseña encriptada
    private $phone;
    private $role;     // 'admin' o 'customer'

    public function __construct() {}

    // GETTERS & SETTERS
    public function getUserId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getLastName() { return $this->last_name; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getPhone() { return $this->phone; }
    public function getRole() { return $this->role; }

    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setName($name) { $this->name = $name; }
    public function setLastName($last_name) { $this->last_name = $last_name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setRole($role) { $this->role = $role; }
}
?>