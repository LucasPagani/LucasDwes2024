<?php

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
        $usuarios = $usuarioDAO->obtenerTodos();

        session_unset();
        session_destroy();
        setcookie(session_name(), '', 0, '/');
        echo $blade->run("perfilAdministrador", ['usuarios' => $usuarios, 'mensaje' => 'Baja realizada con éxito']);
        die;
    } 
    elseif (isset($_REQUEST['botonCrearUsuario'])) {

        echo $blade->run("formregistro");
        die;
    } 
    elseif (isset($_REQUEST['otorgarRolAdmin'])) {

        $idUsuario = $_REQUEST['id'];
        $usuarioDAO->asignarRolAdministrador($idUsuario);
        $usuarios = $usuarioDAO->obtenerTodos();

        echo $blade->run("perfilAdministrador", ['usuarios' => $usuarios, 'mensaje' => 'Rol otorgado con Exito']);
    } 
    elseif (isset($_REQUEST['datosPartidas'])) {
        $idUsuario = $_REQUEST['id']; // Obtener el ID del usuario desde el formulario
        $usuarios = $usuarioDAO->obtenerTodos();
    if (isset($_SESSION['partidas'][$idUsuario])) {
        $partidasUsuario = $_SESSION['partidas'][$idUsuario];
        $partidasGanadas = [];
        $partidasPerdidas = [];
        
        
        foreach ($partidasUsuario as $partida) {
            if ($partida->esPalabraDescubierta()) {
                $partidasGanadas[$partida->getPalabraSecreta()] = $partida->getNumErrores();
            } else {
                $partidasPerdidas[] = $partida->getPalabraSecreta();
            }
        }

        ksort($partidasGanadas);
        sort($partidasPerdidas);

        echo $blade->run("perfilAdministrador", compact('partidasGanadas', 'partidasPerdidas', 'idUsuario','usuarios'));
    } 
    else {
        // Manejar el caso en que no hay partidas para este usuario
        echo $blade->run("perfilAdministrador", [
            'usuarios' => $usuarios,
            'partidasGanadas' => [],
            'partidasPerdidas' => [],
            'idUsuario' => $idUsuario,
            
        ]);
    }
    die;
}
    elseif (isset($_REQUEST['botonHashearContraseñas'])) {


            $usuarioDAO->hashearContraseñas();
            $usuarios = $usuarioDAO->obtenerTodos();

            echo $blade->run("perfilAdministrador", ['usuarios' => $usuarios, 'mensaje' => 'Contraseña Hasheada']);
        } 
    elseif (isset($_REQUEST['quitarHashContraseñas'])) {


            $usuarioDAO->quitarHashContraseñas();
            $usuarios = $usuarioDAO->obtenerTodos();

            echo $blade->run("perfilAdministrador", ['usuarios' => $usuarios, 'mensaje' => 'Hash Eliminado']);
        } 
    elseif (isset($_REQUEST['irFormaAdmin'])) {
            $usuarios = $usuarioDAO->obtenerTodos();

            echo $blade->run("perfilAdministrador", ['usuarios' => $usuarios]);
        } 
    else {
            $usuarios = $usuarioDAO->obtenerTodos();

            echo $blade->run("perfilAdministrador", ['usuarios' => $usuarios]);
        }
    } 
    else {
        if (isset($_REQUEST['botonloginAdmin'])) {
            /** Aqui voy a a indicar que redirija a la vista de login como administrador */
            echo $blade->run("formLoginAdmin");
            die;
        } 
        elseif (isset($_REQUEST['botonprologinAdmin'])) {
            /*             * ya en la vista de login administrador tiene que tener los mismos campos que login pero con una
             * adicional en el que indique el rol "administrador" el cual a la hora de validar tiene que 
             * coincidir el id del usuario con el rol administrador
             * -luego me redirige a la vista personalizada del administrador donde puede visualizar todos
             * usuarios y realizar un crud  */
            // Lee los valores del formulario
            $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
            $clave = trim(filter_input(INPUT_POST, 'clave', FILTER_UNSAFE_RAW));

            // $usuario = $usuarioDAO->recuperaPorCredencialHash($nombre, $clave); para logearse con la contraseña hasheada(NO FUNCIONA)
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
                } else {
                    echo $blade->run("formloginAdmin", ['mensaje' => 'El Usuario NO es Administrador']);
                }
            }
            // Si los credenciales son incorrectos
            else {
                // Invoco la vista del formulario de login con el flag de error activado
                echo $blade->run("formloginAdmin", ['error' => true]);
                die;
            }
        } 
        else {
            // Invoco la vista del formulario de login con el flag de error activado
            echo $blade->run("formloginAdmin");
            die;
        }
    }
    