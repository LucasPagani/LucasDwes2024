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
if (isset($_SESSION['usuario'])) {
    // Si se solicita cerrar la sesión
    if (isset($_REQUEST['botonlogout'])) {
        // Destruyo la sesión
        session_unset();
        session_destroy();
        setcookie(session_name(), '', 0, '/');
        // Invoco la vista del formulario de login
        echo $blade->run("formlogin");
        die;
    } 
    elseif (isset($_REQUEST['botonperfil'])) {
        $usuario = $_SESSION['usuario'];
        echo $blade->run("formperfil", ['nombre' => $usuario->getNombre(), 'clave' => $usuario->getClave(), 'email' => $usuario->getEmail()]);
        die;
    } 
    elseif (isset($_REQUEST['botonprocperfil'])) {
        $usuario = $_SESSION['usuario'];
        $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
        $clave = trim(filter_input(INPUT_POST, 'clave', FILTER_UNSAFE_RAW));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW));
        $errorNombre = empty($nombre) || !esNombreValido($nombre);
        $errorPassword = empty($clave) || !esPasswordValido($clave);
        $errorEmail = empty($email) || !esEmailValido($email);
        if ($errorNombre || $errorPassword || $errorEmail) {
            echo $blade->run("formperfil", compact('nombre', 'clave', 'email', 'errorNombre', 'errorPassword', 'errorEmail'));
            die;
        } else {
            $usuario->setNombre($nombre);
            $usuario->setClave($clave);
            $usuario->setEmail($email);
            try {
                $usuarioDAO->modifica($usuario);
            } catch (PDOException $e) {
                echo $blade->run("formperfil", ['errorBD' => true]);
                die();
            }
            $partida = $_SESSION['partida'];
            echo $blade->run("juego", compact('usuario', 'partida'));
            die();
        }
    } 
    elseif (isset($_REQUEST['botonbaja'])) { // Si se solicita la baja del usuario
        $usuario = $_SESSION['usuario'];
        $usuarioDAO->elimina($usuario->getId());
        session_unset();
        session_destroy();
        setcookie(session_name(), '', 0, '/');
        echo $blade->run("formlogin", ['mensaje' => 'Baja realizada con éxito']);
        die;
    } 
    else {
        if (isset($_SESSION['partida'])) { // Si hay una partida en curso
            header("Location:juego.php");
        } else {
            // Redirijo al cliente al script de gestión del juego
            header("Location:juego.php?botonnuevapartida");
            die;
        }
    }

    // Sino 
} 
else {
    if (isset($_REQUEST['botonproclogin'])) {
        // Lee los valores del formulario
        $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
        $clave = trim(filter_input(INPUT_POST, 'clave', FILTER_UNSAFE_RAW));
        $usuario = $usuarioDAO->recuperaPorCredencial($nombre, $clave);
        // Si los credenciales son correctos
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            // Redirijo al cliente al script de juego con una nueva partida
            header("Location:juego.php?botonnuevapartida");
            die;
        }/**
         * si invoco el boton Administrdor -> blade loginAdmin que es un formulario igual a login pero con el campo a rellenar de administrador
         */
        
        /** si el el usuario tiene el rol administrador redirige al blade perfil del administrador, 
         * donde puede ver todos los usuarios, crear usuarios y asignar roles, modificar y eliminar
         */
        // Si los credenciales son incorrectos
        else {
            // Invoco la vista del formulario de login con el flag de error activado
            echo $blade->run("formlogin", ['error' => true]);
            die;
        }
        // Si se solicita el formulario de registro
    } 
    elseif (isset($_REQUEST['botonregistro'])) {
        echo $blade->run("formregistro");
        die;
        // Si se solicita que se procese una petición de registro
    } 
    elseif (isset($_REQUEST['botonprocregistro'])) {
        $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
        $clave = trim(filter_input(INPUT_POST, 'clave', FILTER_UNSAFE_RAW));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW));
        $errorNombre = empty($nombre) || !esNombreValido($nombre);
        $errorPassword = empty($clave) || !esPasswordValido($clave);
        $errorEmail = !esEmailValido($email);
        $error = $errorNombre || $errorPassword || $errorEmail;
        if ($error) {
            echo $blade->run("formregistro", compact('nombre', 'clave', 'email', 'errorNombre', 'errorPassword', 'errorEmail'));
            die;
        } 
        else {
            $usuario = new Usuario($nombre, $clave, $email);
            try {
                $usuarioDAO->crea($usuario);
            } catch (PDOException $e) {
                echo $blade->run("formregistro", ['errorBD' => true]);
                die();
            }
            echo $blade->run("formlogin" , ['mensaje' => 'Usuario creado con éxito']);
            die();
        }
    } 
    else {
        // Invoco la vista del formulario de login
        echo $blade->run("formlogin");
        die;
    }
}