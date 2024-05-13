<?php

/**
 *  --- Lógica del script --- 
 * 
 * Establece conexión a la base de datos PDO
 * Si el usuario ya está validado
 *   Si se solicita cerrar la sesión
 *     Destruyo la sesión
 *     Invoco la vista del formulario de login
 *    Sino redirección a juego para jugar una partida
 *  Sino 
 *   Si se pide procesar los datos del formulario
 *       Lee los valores del formulario
 *       Si los credenciales son correctos
 *       Redirijo al cliente al script de juego con una nueva partida
 *        Sino Invoco la vista del formulario de login con el flag de error
 *    Sino si se solicita el formulario de registro
 *     Invoco la vista del formulario de registro
 *    Sino si se solicita procesar el formulario de registro
 *     Leo los datos
 *     Establezco flags de error
 *     Si hay errores
 *        Invoco la vista de formulario de registro  con información sobre los errores
 *      Sino persisto el usuario en la base de datos
 *          Invoco la vista de formulario de login 
 *   Sino (En cualquier otro caso)
 *      Invoco la vista del formulario de login
 */
require "../vendor/autoload.php";

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;
use App\BD\BD;
use App\Modelo\Usuario;
use App\DAO\UsuarioDAO;

session_start();

// Inicializa el acceso a las variables de entorno

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// Inicializa el acceso a las variables de entorno

$views = __DIR__ . '/../vistas';
$cache = __DIR__ . '/../cache';
$blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

// Funciones de validación de datos del formulario de registro
// Validación del nombre con expresión regular
function esNombreValido(string $nombre): bool {
    return preg_match("/^\w{3,15}$/", $nombre);
}

// Validación de la clave con 6 dígitos
function esPasswordValido(string $clave): bool {
    return preg_match("/^[0-9]{6}$/", $clave);
}

// Validación de correo que puede ser vacío o con e lformato correcto
function esEmailValido(string $email): bool {
    return (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL));
}

// Establece conexión a la base de datos PDO
try {
    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    $database = $_ENV['DB_DATABASE'];
    $usuario = $_ENV['DB_USUARIO'];
    $password = $_ENV['DB_PASSWORD'];
    $bd = BD::getConexion($host, $port, $database, $usuario, $password);
} catch (PDOException $error) {
    echo $blade->run("cnxbderror", compact('error'));
    die;
}

$usuarioDAO = new UsuarioDAO($bd);
// Si el usuario ya está validado


if (isset($_SESSION ['usuario'])) {
    if (isset($_REQUEST['botonEliminarUsuario'])) {
        
        $idUsuario = $_REQUEST['id'];        
        $usuarioDAO->elimina($idUsuario);
        $usuarios= $usuarioDAO->obtenerTodos();
        
        session_unset();
        session_destroy();
        setcookie(session_name(), '', 0, '/');
        echo $blade->run("perfilAdministrador", ['usuarios' => $usuarios,'mensaje' => 'Baja realizada con éxito']);
        die;
    } 
    elseif (isset($_REQUEST['botonCrearUsuario'])) {

        echo $blade->run("formregistro");
        die;
    }
    elseif (isset($_REQUEST['otorgarRolAdmin'])) {
      
      $idUsuario = $_REQUEST['id'];        
      $usuarioDAO ->asignarRolAdministrador($idUsuario);
      $usuarios= $usuarioDAO->obtenerTodos();
      
       echo $blade->run("perfilAdministrador", ['usuarios' => $usuarios, 'mensaje' => 'Rol otorgado con Exito']);

      
    }
} else {
    if (isset($_REQUEST['botonloginAdmin'])) {
        /** Aqui voy a a indicar que redirija a la vista de login como administrador */
        echo $blade->run("formLoginAdmin");
        die;
    } 
    elseif (isset($_REQUEST['botonprologinAdmin'])) {
        /*         * ya en la vista de login administrador tiene que tener los mismos campos que login pero con una
         * adicional en el que indique el rol "administrador" el cual a la hora de validar tiene que 
         * coincidir el id del usuario con el rol administrador
         * -luego me redirige a la vista personalizada del administrador donde puede visualizar todos
         * usuarios y realizar un crud  */
        // Lee los valores del formulario
        $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
        $clave = trim(filter_input(INPUT_POST, 'clave', FILTER_UNSAFE_RAW));

        $usuario = $usuarioDAO->recuperaPorCredencial($nombre, $clave);
        $usuarios = $usuarioDAO->obtenerTodos();
        // Si los credenciales son correctos
        if ($usuario) {
            $usuarioAdmin = $usuario->esAdministrador();

            if ($usuarioAdmin) {
                $_SESSION['usuario'] = $usuario;
                // Redirijo al administrador al script privado 
                echo $blade->run("perfilAdministrador", ['usuarios' => $usuarios, 'mensaje' => 'Bienvenido Administrador']);
                die;
            }
        }
        // Si los credenciales son incorrectos
        else {
            // Invoco la vista del formulario de login con el flag de error activado
            echo $blade->run("formloginAdmin", ['error' => true]);
            die;
        }
    }
}
