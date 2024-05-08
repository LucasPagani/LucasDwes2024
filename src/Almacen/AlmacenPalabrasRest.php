<?php
namespace App\Almacen;

class AlmacenPalabrasRest implements AlmacenPalabrasInterface {

    /**
     * @var string $url URL al servicio REST
     */
    private string $url;

    /**
     * Constructor de la clase AlmacenPalabrasRest
     *
     * @param string $url URL al servicio REST
     *
     * @return AlmacenPalabrasRest
     */
    public function __construct(string $url) {
        $this->url = $url;
    }

    /**
     * Obtiene una palabra aleatoria del servicio REST
     *
     * @return string Palabra aleatoria
     */
    public function obtenerPalabraAleatoria(): string {
        // Realiza una solicitud HTTP GET al servicio REST para obtener una palabra aleatoria
        $response = file_get_contents($this->url);
        if ($response === false) {
            // Manejo de errores
            return ""; // Devuelve una cadena vacía en caso de error
        }
        // Decodifica la respuesta JSON y devuelve la palabra aleatoria
        $data = json_decode($response, true);
        return isset($data['word']) ? $data['word'] : ""; // Suponiendo que el JSON devuelto tiene una clave 'word' para la palabra aleatoria
    }
}
