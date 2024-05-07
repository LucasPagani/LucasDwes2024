<?php

namespace App\Modelo;

use App\Almacen\AlmacenPalabrasInterface;

/**
 * Clase que representa una partida del juego del ahorcado
 */
class Hangman {

    /**
     * @var int $numErrores Número de errores cometidos en la partida
     */
    private int $numErrores = 0;

    /**
     * @var $palabraSecreta Palabra secreta usada en la partida
     */
    private string $palabraSecreta;

    /**
     * @var $palabraDescubierta Estado de la palabra según va siendo descubierta. Por ejemplo c_c_e
     */
    private string $palabraDescubierta;

    /**
     * @var $letras Lista de jugadas que ha realizado el jugador en la partida
     */
    private string $letras = "";

    /**
     * @var $manNumErrores Número de errores permitido en la partida
     */
    private int $maxNumErrores;

    /**
     * Constructor de la clase Hangman
     * 
     * @param AlmacenPalabrasInterface $almacen Almacen de donde obtener palabras para el juego
     * @param int $maxNumErrores Número maximo de errores
     * 
     * @returns Hangman
     */
   // public function __construct(AlmacenPalabrasInterface $almacen, int $maxNumErrores) {
   //     $this->setPalabraSecreta(strtoupper($almacen->obtenerPalabraAleatoria()));
        // Inicializa la estado de la palabra descubierta a una secuencia de guiones, uno por letra de la palabra oculta
    //    $this->setPalabraDescubierta(preg_replace('/\w+?/', '_', $this->getPalabraSecreta()));
     //   $this->maxNumErrores = $maxNumErrores;
    //}
    
     public function __construct($almacen, $maxNumErrores = 5, $options = []) {
        $esCorrecta = false;
        while (!$esCorrecta) {
            $palabra = strtoupper($almacen->obtenerPalabraAleatoria());
            if ((strlen($palabra) >= ($options['minLongitud'] ?? 0)) && (strlen($palabra) <= ($options['maxLongitud'] ?? PHP_INT_MAX)) &&
                    (array_sum(array_map(fn($x) => !empty($x) && str_contains($palabra, $x), str_split(strtoupper($options['contiene'] ?? "")))) === strlen($options['contiene'] ?? ""))) {
                $esCorrecta = true;
            }
        }
        $this->setPalabraSecreta($palabra);
// Inicializa la estado de la palabra descubierta a una secuencia de guiones, uno por letra de la palabra oculta
        $this->setPalabraDescubierta(preg_replace('/\w+?/', '_', $this->getPalabraSecreta()));
        $this->maxNumErrores = $maxNumErrores;
    }


    /**
     * Recupera la palabra secreta de la partida
     * 
     * @returns string Palabra secreta de la partida
     */
    public function getPalabraSecreta(): string {
        return $this->palabraSecreta;
    }

    /**
     * Establece la palabra secreta de la partida
     * 
     * @param string $palabraSecreta Palabra secreta de la partida
     * 
     * @returns void
     */
    public function setPalabraSecreta(string $palabraSecreta): void {
        $this->palabraSecreta = $palabraSecreta;
    }

    /**
     * Recupera el estado de la palabra descubierta de la partida
     * 
     * @returns string Estado de la palabra descubierta de la partida
     */
    public function getPalabraDescubierta(): string {
        return $this->palabraDescubierta;
    }

    /**
     * Establece el estado de la palabra descubierta de la partida
     * 
     * @param string $palabraDescubierta El estado de la palabra descubierta de la partida
     * 
     * @returns void
     */
    public function setPalabraDescubierta(string $palabraDescubierta): void {
        $this->palabraDescubierta = $palabraDescubierta;
    }

    /**
     * Recupera el listado de letras jugadas en la partida
     * 
     * @returns string Listado de letras jugadas en la partida
     */
    public function getLetras(): string {
        return $this->letras;
    }

    /**
     * Establece el listado de letras jugadas en la partida
     * 
     * @param string $letras Listado de letras jugadas en la partida
     * 
     * @returns void
     */
    public function setLetras(string $letras): void {
        $this->letras = $letras;
    }

    /**
     * Recupera el número máximo de errores de la partida
     * 
     * @returns int Número máximo de errores de la partida
     */
    public function getMaxNumErrores(): int {
        return $this->maxNumErrores;
    }

    /**
     * Establece el número máximo de errores de la partida
     * 
     * @param int $maxNumErrores Número máximo de errores de la partida
     * 
     * @returns void
     */
    public function setMaxNumErrores($maxNumErrores): void {
        $this->maxNumErrores = $maxNumErrores;
    }

    /**
     * Recupera el número de errores cometido en la partida
     * 
     * @returns int Número de errores cometido en la partida
     */
    public function getNumErrores(): int {
        return $this->numErrores;
    }

    /**
     * Establece el número de errores cometido en la partida
     * 
     * @param int $numErrores Número de errores cometido en la partida
     * 
     * @returns void
     */
    public function setNumErrores($numErrores): void {
        $this->numErrores = $numErrores;
    }

    /**
     * Determina si una letra jugada es válida para el juego. Una letra es válida si se trata de una
     * letra en minúsculas o mayúsculas y si no ha sido jugada anteriormente
     * 
     * @param string $letra Letra elegida por el jugador
     * 
     * @returns bool Indica si la letra es válisa
     */
    public function esLetraValida(string $letra): bool {
        return ((strpos($this->getLetras(), strtoupper($letra)) === false) &&
                preg_match("/^[A-Za-z]$/", $letra));
    }

    /**
     * Comprueba la letra elegida por el jugador, modifica el estado de la palabra descubierta y añade la letra
     * 
     * @param string $letra Letra elegida por el jugador
     * 
     * @returns string El estado de la palabra descubierta
     */
    public function compruebaLetra(string $letra): string {
        $nuevaPalabraDescubierta = implode(array_map(function ($letraSecreta, $letraDescubierta) use ($letra) {
                    return ((strtoupper($letra) === $letraSecreta) ? $letraSecreta : $letraDescubierta);
                }, str_split($this->getPalabraSecreta()), str_split($this->getPalabraDescubierta())));
        if ($nuevaPalabraDescubierta == $this->getPalabraDescubierta()) {
            $this->numErrores++;
        } else {
            $this->setPalabraDescubierta($nuevaPalabraDescubierta);
        }
        $this->setLetras("{$this->getLetras()}$letra");
        return ($nuevaPalabraDescubierta);
    }

    /**
     * Comprueba si la palabra oculta el juego ya ha sido descubierta
     * 
     * @returns bool Verdadero si ya ha sido descubierta y falso en caso contrario
     */
    public function esPalabraDescubierta(): bool {
        // Si ya no hay guiones en la palabra descubierta
        return (!(strstr($this->getPalabraDescubierta(), "_")));
    }

    /**
     * Comprueba si la partida se ha acabado
     * 
     * @returns bool Verdadero si ya se ha acabado y falso en caso contrario
     */
    public function esFin(): bool {
        return ($this->esPalabraDescubierta() || ($this->getNumErrores() === $this->getMaxNumErrores()));
    }

    /**
     * Calcula la letra a mostrar cuando se solicita una pista
     * Letra con mayor número de ocurrencias y ordenada alfabéticamente
     * 
     * @returns string Letra de pista, si ya no hay letras ocultas se devuelve la cadena vacía
     */
    public function damePista(): string {
        $resultado = "";
        $ocurrencias = []; // Guarda el número de ocurrencias de las letras de la palabra
        for ($i = 0; $i < strlen($this->getPalabraSecreta()); $i++) {
            if (isset($ocurrencias[$this->getPalabraSecreta()[$i]])) {
                $ocurrencias[$this->getPalabraSecreta()[$i]]++;
            } else {
                $ocurrencias[$this->getPalabraSecreta()[$i]] = 1;
            }
        }
        ksort($ocurrencias); // Ordeno alfabéticamente por la clave que representa la letra
        arsort($ocurrencias); // Ordeno por el número de ocurrencias de cada letra. Se mantiene la relación con la clave
        foreach ($ocurrencias as $letra => $num) { // Busco la primera letra de la lista que está en la palabra secreta y no en la palabra descubierta
            if ((strpos($this->getPalabraDescubierta(), $letra) === false) && (strpos($this->getPalabraSecreta(), $letra) !== false)) {
                $resultado = $letra;
                break;
            }
        }
        return $resultado;
    }
    
     /**
     * Devuelve la puntuación de la palabra.
     * 
     * Si la palabra NO ha sido acertada la puntuación es 0.
     * Si la palabra ha sido acertada la puntuación es 1.
     * Se suma un punto si la palabra tiene entre 3 y 5 letras.
     * Se suma un punto por cada combinación de dos vocales seguidas de la palabra.
     * Se suma un punto si se descubre en 3 o menos fallos.
     *  
     * @returns int Puntuación de la palabra
     */
        public function getPuntuacion(): int {
        $puntuacion = 0;
        if ($this->esPalabraDescubierta()) {
            $puntuacion = 1 + preg_match_all('/[AEIOU]{2,}/', $this->getPalabraSecreta()) + 
                    (strlen($this->getPalabraSecreta()) >= 3 && strlen($this->getPalabraSecreta()) <= 5) + 
                    ($this->getNumErrores() <= 3);
        }
        return $puntuacion;
    }


}
