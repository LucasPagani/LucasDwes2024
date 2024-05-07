<?php

/**
 *  --- Lógica del script --- 
 * 
 * Establece conexión a la base de datos PDO
 * Si el usuario ya está validado
 *   Si se pide jugar con una letra
 *     Leo la letra
 *     Si no hay error en la letra introducida
 *       Solicito a la partida que compruebe la letra
 *     Invoco la vista de juego con los datos obtenidos
 *   Sino si se solicita una pista
 *     Obtengo una pista para la partida
 *     Envío la pista en JSON al cliente
 *   Sino si se solicita una nueva partida
 *     Se crea una nueva partida
 *     Invoco la vista del juego para empezar a jugar
 *   Sino Invoco la vista de juego
 *  Sino (En cualquier otro caso)
 *      Invoco la vista del formulario de login
 */
require "../vendor/autoload.php";

use eftec\bladeone\BladeOne;
use Dotenv\Dotenv;
use App\Modelo\Hangman;
use App\Almacen\AlmacenPalabrasFichero;
//use App\Almacen\AlmacenPalabrasSoap;   //Para usar con el la opcion de servicio Soap

session_start();

define('MAX_NUM_ERRORES', 5);

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$views = __DIR__ . '/../vistas';
$cache = __DIR__ . '/../cache';
$blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

function esLongitudMinimaError ($lon) {
    return !(filter_var($lon, FILTER_VALIDATE_INT, ['options' => ['min_range' => 3, 'max_range' => 14]]));
}

function esLongitudMaximaError ($lon) {
    return !(filter_var($lon, FILTER_VALIDATE_INT, ['options' => ['min_range' => 5, 'max_range' => 20]]));
}

function esContieneError ($letras) {
    return !(filter_var($letras, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z]{1,3}$/']]));
}

// Si el usuario ya está validado
if (isset($_SESSION['usuario'])) {
    $usuario = $_SESSION['usuario'];
// Si se pide jugar con una letra
    if (isset($_POST['botonenviarjugada'])) {
// Leo la letra
        $letra = trim(filter_input(INPUT_POST, 'letra', FILTER_UNSAFE_RAW));
        $partida = $_SESSION['partida'];
// Compruebo si la letra no es válida (carácter no válido o ya introducida)
        $error = !$partida->esLetraValida($letra);
        // Si no hay error compruebo la letra
        if (!$error) {
            $partida->compruebaLetra(strtoupper($letra));
            if ($partida->esFin()) {
                $_SESSION['partidas'][] = $partida;
            }
        }
        // Sigo jugando
        echo $blade->run("juego", compact('usuario', 'partida', 'error'));
        die;
// Sino si se solicita una nueva partida
    } 
    elseif (isset($_REQUEST['botonpista'])) {
        $partida = $_SESSION['partida'];
        $pista = $partida->damePista();
        header('Content-type: application/json');
        echo json_encode(['letra' => $pista]);
        die;
    } 
    elseif (isset($_REQUEST['botonnuevapartida'])) { // Se arranca una nueva partida
       
        //$wsdl = $_ENV['WSDL_ALMACEN_PALABRAS'];
        //$almacenPalabras = new AlmacenPalabrasSoap($wsdl);       // sustituimos la ruta para buscar las palabras
        $rutaFichero = $_ENV['RUTA_ALMACEN_PALABRAS'];
        $almacenPalabras = new AlmacenPalabrasFichero($rutaFichero);
        
        $partida = new Hangman($almacenPalabras, MAX_NUM_ERRORES);
        $_SESSION['partida'] = $partida;
// Invoco la vista del juego para empezar a jugar
        echo $blade->run("juego", compact('usuario', 'partida'));
        die;
    } 
    elseif (isset($_REQUEST['botonpuntuacionpartidas'])) {// Se arranca una nueva partida
        $partidas = $_SESSION['partidas'] ?? [];
        $panelPuntuacion = [];
        foreach ($partidas as $partida) {
            $panelPuntuacion[] = [$partida->getPalabraSecreta(), $partida->getNumErrores(), $partida->getPuntuacion()];
        }
        echo $blade->run("puntuacionpartidas", compact('panelPuntuacion', 'usuario'));
        die;
    }
    elseif (isset($_REQUEST['botonresumenpartidas'])) {// Se arranca una nueva partida
        $partidas = $_SESSION['partidas'] ?? [];
        $partidasGanadas = [];
        $partidasPerdidas = [];
        foreach ($partidas as $partida) {
            if ($partida->esPalabraDescubierta()) {
                $partidasGanadas[$partida->getPalabraSecreta()] = $partida->getNumErrores();
            } else {
                $partidasPerdidas[] = $partida->getPalabraSecreta();
            }
        }
        ksort($partidasGanadas);
        sort($partidasPerdidas);

        echo $blade->run("resumenpartidas", compact('partidasGanadas', 'partidasPerdidas', 'usuario'));
        die;
    }
    elseif (isset($_REQUEST['botonformpartidapersonalizada'])) {// Se arranca una nueva partida
        echo $blade->run("formpartidapersonalizada", compact('usuario'));
        die;
    } 
    elseif (isset($_REQUEST['botonpartidapersonalizada'])) {// Se arranca una nueva partida
        $minLongitud = filter_input(INPUT_POST, 'minlongitud');
        $minLongitudError = !empty($minLongitud) && esLongitudMinimaError($minLongitud);
        $maxLongitud = filter_input(INPUT_POST, 'maxlongitud');
        $maxLongitudError = !empty($maxLongitud) && esLongitudMaximaError($maxLongitud);;
        $maxminError = !empty($minLongitud) && !empty($maxLongitud) 
                && !esLongitudMinimaError($minLongitud) && !esLongitudMaximaError($maxLongitud) 
                && ($minLongitud > $maxLongitud);
        $contiene = trim(filter_input(INPUT_POST, 'contiene'));
        $contieneError = !empty($contiene) && esContieneError($contiene);
        $error = $minLongitudError || $maxLongitudError || $maxminError || $contieneError;
        if ($error) {
            echo $blade->run("formpartidapersonalizada", compact('minLongitud', 'minLongitudError', 'maxLongitud', 'maxLongitudError', 'maxminError', 'contiene', 'contieneError', 'usuario'));
            die;
        } else {
            $rutaFichero = $_ENV['RUTA_ALMACEN_PALABRAS'];
            $almacenPalabras = new AlmacenPalabrasFichero($rutaFichero);
            $options = array_filter (compact('minLongitud', 'maxLongitud', 'contiene'), fn ($x) => !empty($x));
            $partida = new Hangman($almacenPalabras, MAX_NUM_ERRORES, $options);
            $_SESSION['partida'] = $partida;
// Invoco la vista del juego para empezar a jugar
            echo $blade->run("juego", compact('usuario', 'partida'));
            die;
        }
    }
    else { //En cualquier otro caso
        $partida = $_SESSION['partida'];
        echo $blade->run("juego", compact('usuario', 'partida'));
        die;
    }
// En otro caso se muestra el formulario de login
} else {
    echo $blade->run("formlogin");
    die;
}
