<?php

namespace App\Almacen;

class AlmacenPalabrasRest implements AlmacenPalabrasInterface {

    /**
     * @var array Lista de palabras con las que poder jugar
     */
    private array $listaPalabras;

    /**
     * Constructor de la clase AlmacenPalabrasFichero
     *
     * Lee todas las palabras del archivo JSON indicado en la ruta y las almacena en la propiedad $listaPalabras
     *
     * @param string $rest Ruta relativa al archivo JSON de palabras
     * @returns AlmacenPalabrasFichero
     */
    public function __construct(string $rest) {
        $contenidoJSON = file_get_contents($rest);
        $datos = json_decode($contenidoJSON, true);

        // Asigna las palabras del archivo JSON a la propiedad $listaPalabras
        $this->listaPalabras = $datos['palabras'] ?? [];
    }

    /**
     * Obtiene una palabra aleatoria
     *
     * @returns string Palabra aleatoria
     */
    public function obtenerPalabraAleatoria(): string {
        return $this->listaPalabras[array_rand($this->listaPalabras)];
    }
}