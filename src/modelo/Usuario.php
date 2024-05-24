<?php

namespace App\Modelo;

/**
 * Clase que representa al usuario que está usando la aplicación
 */
class Usuario {

    /**
     * @var string $id identificador del usuario
     */
    private string $id;

    /**
     * @var string $nombre nombre del usuario
     */
    private string $nombre;

    /**
     * @var string $pwd Pwd del usuario
     */
    private string $clave;

    /**
     * @var string $email Email del usuario
     */
    private ?string $email;
   
    
    private ?string $rol;
   
    private ?int $partidasganadas ;
    
    private ?int $partidasperdidas ;

    /**
     * Constructor de la clase Usuario
     * 
     * @param string $nombre Nombre del usuario
     * @param string $pwd Pwd del usuario
     * @param string $email Email del usuario
     * 
     * @returns Hangman
     */
    public function __construct(string $nombre = null, string $clave = null, ?string $email = null, ?string $rol = null, ?int $partidasGanadas = null, ?int $partidasPerdidas = null) {
    if (!is_null($nombre)) {
        $this->nombre = $nombre;
    }
    if (!is_null($clave)) {
        $this->clave = $clave;
    }
    if (!is_null($email)) {
        $this->email = $email;
    }
    if (!is_null($rol)) {
        $this->rol = $rol;
    }    
    
    if (!is_null($partidasGanadas)) {
        $this->partidasganadas = $partidasGanadas;
    }
    if (!is_null($partidasPerdidas)) {
        $this->partidasperdidas = $partidasPerdidas;
    }
     
}


    /**
     * Recupera el Id del usuario
     * 
     * @returns int Id del usuario
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Recupera el nombre del usuario
     * 
     * @returns string Nombre del usuario
     */
    public function getNombre(): string {
        return $this->nombre;
    }

    public function setId(int $contador = 0) {
        $this->id = ++$contador;
    }

    /**
     * Establece el nombre del usuario
     * 
     * @param string $nombre Nombre del usuario
     * 
     * @returns void
     */
    public function setNombre(string $nombre) {
        $this->nombre = $nombre;
    }

    /**
     * Recupera la pwd del usuario
     * 
     * @returns string Pwd del usuario
     */
    public function getClave(): string {
        return $this->clave;
    }

    /**
     * Establece la pwd del usuario
     * 
     * @param string $pwd pwd del usuario
     * 
     * @returns void
     */
    public function setClave(string $clave) {
        $this->clave = $clave;
    }

    /**
     * Recupera el email del usuario
     * 
     * @returns string Email del usuario
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * Establece el email del usuario
     * 
     * @param string $email Email del usuario
     * 
     * @returns void
     */
    public function setEmail(string $email) {
        $this->email = $email;
    }

    /**
     * Recupera el rol del usuario
     * 
     * @returns string Rol del usuario
     */
    public function getRol(): ?string {
        return $this->rol;
    }

    /**
     * Establece el rol del usuario
     * 
     * @param string $rol Rol del usuario
     * 
     * @returns void
     */
    public function setRol(string $rol) {
        $this->rol = $rol;
    }

    public function esAdministrador() {
        return $this->rol === 'administrador'; //NO FUNCIONA BIEN.// VER
        // return $this->nombre === 'admin';
    }

    public function getPartidasGanadas(): ?int {
        return $this->partidasganadas;
    }

    public function getPartidasPerdidas(): ?int {
        return $this->partidasperdidas;
    }

    public function setPartidasGanadas(?int $partidasGanadas): void {
        $this->partidasganadas = $partidasGanadas;
    }

    public function setPartidasPerdidas(?int $partidasPerdidas): void {
        $this->partidasperdidas = $partidasPerdidas;
    }
}
