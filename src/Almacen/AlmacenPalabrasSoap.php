<?php

namespace App\Almacen;

use \SoapClient;

class AlmacenPalabrasSoap implements AlmacenPalabrasInterface {

    /**
     * 
     * @var SoapClient $clienteSoap Cliente Soap para acceder al servicio de palabras Soap
     */
    private SoapClient $cliente;

    /**
     * Constructor de la clase AlmacenPalabrasFichero
     * 
     * Lee todas las palabras del fichero indicado en el fichero de configuración y las almacena en la propiedad $listaPalabras
     * 
     * @param string $wsdl URL al wsdl del servicio
     * 
     * @returns AlmacenPalabrasSoap
     */
    public function __construct(string $wsdl) {
        $this->cliente = new SoapClient($wsdl);
    }

    /**
     * Obtiene una palabra aleatoria
     * 
     * 
     * @returns string Palabra aleatoria
     */
    public function obtenerPalabraAleatoria(): string {
        $palabra = $this->cliente->getPalabraAleatoria();
        return $palabra;
    }

}
