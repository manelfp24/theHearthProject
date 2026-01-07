<?php
//cargamos elDAO
include_once __DIR__ . '/../models/UserDAO.php';

class UserController {

    public function login() {
        include __DIR__ . '/../views/user/login.php';
    }

    //proceso de login
    //URL: index.php?controller=User&action=authenticate
    public function authenticate() {
        //miramos si el formulario se ha mandado con POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            //sacamos los datos del formulario
            $email = $_POST['email'];
            $password = $_POST['password'];

            //encontramos al usuario en la bbdd
            $user = UserDAO::getUserByEmail($email);

            //miramos si el usuario existe y si coincide con la contraseña
            //usamos password_verify() para hashear las contraseñas en la bbdd
            if ($user && password_verify($password, $user->getPassword())) {
                
                //si el login ha funcionado
                
                // guardamos la info del la sesion de usuario
                $_SESSION['user_id'] = $user->getUserId();
                $_SESSION['user_name'] = $user->getName();
                $_SESSION['user_role'] = $user->getRole();

                // redirigimos dependiendo del rol del usuario(Admin -> Dashboard, Customer -> Home)
                if ($user->getRole() === 'admin') {
                    header("Location: index.php?controller=Admin&action=dashboard");
                } else {
                    header("Location: index.php?controller=Home");
                }
                exit();

            } else {
                // si no funciona el login mostramos mensaje de error
                $error_message = "Invalid email or password.";
                // recargamos la pagina de login
                include __DIR__ . '/../views/user/login.php';
            }
        }
    }
    //mostramos formulario de registro
    //URL: index.php?controller=User&action=register
    public function register() {
        include __DIR__ . '/../views/user/register.php';
    }

    //guardamos nuevo usuario
    //URL: index.php?controller=User&action=store
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Recogemos los dtaos del formulario
            $name = $_POST['name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $password = $_POST['password'];

            //encriptamos las contraseña
            //lo encripta tipo "$2y$10$..." de forma automatica
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Lo guardamos en la base de datos
            $success = UserDAO::insertUser($name, $last_name, $email, $password_hash, $phone);

            if ($success) {
                // si funciona vamos a la pagina de login
                header("Location: index.php?controller=User&action=login");
            } else {
                // si hay error devolvemos a la pagia de registro
                // falta poner un error aqui
                header("Location: index.php?controller=User&action=register");
            }
        }
    }
    //logout
    //URL: index.php?controller=User&action=logout
    public function logout() {
        // borramos todas las variables de sesión
        session_unset();
        
        //destruimos la sesión
        session_destroy();

        // volvemos a la Home
        header("Location: index.php");
        exit();
    }
}
?>