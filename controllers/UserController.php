<?php
// incluimos el archivo para poder usar las funciones de la base de datos de usuarios
include_once __DIR__ . '/../models/UserDAO.php';

class UserController {

    // carga la página con el formulario para iniciar sesión
    public function login() {
        include __DIR__ . '/../views/user/login.php';
    }

    // gestiona el intento de entrada del usuario al sistema
    // se usa la url: index.php?controller=User&action=authenticate
    public function authenticate() {
        // comprobamos que los datos vienen por el método post del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // guardamos el email y la clave que ha escrito el usuario
            $email = $_POST['email'];
            $password = $_POST['password'];

            // buscamos si existe algún usuario con ese email en la base de datos
            $user = UserDAO::getUserByEmail($email);

            // verificamos que el usuario exista y que la clave sea la correcta
            // usamos la función nativa de php para comparar con el hash guardado
            if ($user && password_verify($password, $user->getPassword())) {
                
                // si los datos son válidos, iniciamos la sesión del usuario
                // guardamos su id, nombre y rol para usarlos en toda la web
                $_SESSION['user_id'] = $user->getUserId();
                $_SESSION['user_name'] = $user->getName();
                $_SESSION['user_role'] = $user->getRole();

                // enviamos al usuario a un sitio u otro según si es admin o cliente
                if ($user->getRole() === 'admin') {
                    header("Location: index.php?controller=Admin&action=dashboard");
                } else {
                    header("Location: index.php?controller=Home");
                }
                exit();

            } else {
                // si los datos no coinciden, preparamos un mensaje de aviso
                $error_message = "Invalid email or password.";
                // volvemos a mostrar el login pero con el error
                include __DIR__ . '/../views/user/login.php';
            }
        }
    }

    // muestra la página para que un nuevo cliente se registre
    // se usa la url: index.php?controller=User&action=register
    public function register() {
        include __DIR__ . '/../views/user/register.php';
    }

    // recibe los datos del registro y los guarda en el sistema
    // se usa la url: index.php?controller=User&action=store
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // capturamos todos los campos necesarios del formulario
            $name = $_POST['name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $password = $_POST['password'];

            // encriptamos la contraseña antes de guardarla por seguridad
            // esto genera una cadena segura que no se puede descifrar fácilmente
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // llamamos al dao para insertar el nuevo usuario en la base de datos
            $success = UserDAO::insertUser($name, $last_name, $email, $password_hash, $phone);

            if ($success) {
                // si se guarda bien, lo mandamos al login para que entre
                header("Location: index.php?controller=User&action=login");
            } else {
                // si algo falla, lo devolvemos al formulario de registro
                header("Location: index.php?controller=User&action=register");
            }
        }
    }

    // cierra la sesión actual del usuario
    // se usa la url: index.php?controller=User&action=logout
    public function logout() {
        // limpiamos todas las variables que habíamos guardado en la sesión
        session_unset();
        
        // borramos la sesión por completo del servidor
        session_destroy();

        // devolvemos al usuario a la página de inicio
        header("Location: index.php");
        exit();
    }

    // muestra el perfil privado del usuario con sus datos y pedidos
    public function profile() {
        // seguridad básica: si no está logueado, lo mandamos al login
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=User&action=login");
            exit();
        }

        $userId = $_SESSION['user_id'];

        // pedimos al dao toda la información del usuario conectado
        $user = UserDAO::getUserById($userId);

        // obtenemos los 3 pedidos más recientes para mostrarlos en el historial
        $recentOrders = OrderDAO::getLastOrdersByUser($userId, 3);
        
        // cargamos la vista del perfil con toda la información obtenida
        require_once __DIR__ . '/../views/user/profile.php'; 
    }

    // procesa los cambios que el usuario haga en sus datos personales
    // se usa la url: index.php?controller=User&action=update_profile
    public function update_profile() {
        // comprobamos que sea una petición de envío y que el usuario esté logueado
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $newName = $_POST['name'];
            $newEmail = $_POST['email'];

            // mandamos los nuevos datos a la base de datos
            $success = UserDAO::updateUser($userId, $newName, $newEmail);

            if ($success) {
                // si se actualiza bien, cambiamos también el nombre en la sesión actual
                $_SESSION['user_name'] = $newName; 
                header("Location: index.php?controller=User&action=profile&success=1");
            } else {
                // si hay error, volvemos al perfil avisando del fallo
                header("Location: index.php?controller=User&action=profile&error=1");
            }
            exit();
        }
    }
}
?>