<?php

namespace App\DAO;

use \PDO;
use App\Modelo\Usuario;

class UsuarioDAO {

    /**
     * @var $bd Conexión a la Base de Datos
     */
    private PDO $bd;

    /**
     * Constructor de la clase UsuarioDAO
     * 
     * @param PDO $bd Conexión a la base de datos
     * 
     * @returns UsuarioDAO
     */
    public function __construct(PDO $bd) {
        $this->bd = $bd;
    }

    /**
     * Inserta un objeto usuario en la tabla usuarios
     * 
     * @param Usuario $usuario Usuario a persistir 
     * 
     * @returns bool Resultado de la operación de inserción
     */
    public function crea(Usuario $usuario): bool {
        // Hashear la contraseña antes de insertarla
        $hashedPassword = password_hash($usuario->getClave(), PASSWORD_BCRYPT);
        $sql = "insert into usuarios (nombre, clave, email) values (:nombre, :clave, :email)";
        $sth = $this->bd->prepare($sql);
        $result = $sth->execute([":nombre" => $usuario->getNombre(), ":clave" => $hashedPassword, ":email" => $usuario->getEmail()]);
        return ($result);
    }

    public function modifica($usuario) {
        // Hashear la contraseña antes de actualizarla
        $hashedPassword = password_hash($usuario->getClave(), PASSWORD_BCRYPT);
        $sql = "update usuarios set nombre = :nombre, clave = :clave, email = :email where id = :id";
        $sth = $this->bd->prepare($sql);
        $result = $sth->execute([":nombre" => $usuario->getNombre(), ":clave" => $hashedPassword, ":email" => $usuario->getEmail(), ":id" => $usuario->getId()]);
        return ($result);
    }

    public function elimina(int $id) {
        $sql = "delete from usuarios where id = :id";
        $sth = $this->bd->prepare($sql);
        $result = $sth->execute([":id" => $id]);
        return ($result);
    }

    /**
     * Recupera un objeto usuario dado su nombre de usuario y clave
     * 
     * @param string $nombre Nombre de usuario
     * @param string $clave Clave del usuario
     * 
     * @returns Usuario que corresponde a ese nombre y clave o null en caso contrario
     */
    public function recuperaPorCredencialHash(string $nombre, string $clave): ?Usuario {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from usuarios where nombre=:nombre';
        $sth = $this->bd->prepare($sql);
        $sth->execute([":nombre" => $nombre]);
        $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Usuario::class);
        $usuario = ($sth->fetch()) ?: null;

        if ($usuario && password_verify($clave, $usuario->getClave())) {
            return $usuario;
        } else {
            return $usuario;
        }
    }

    public function recuperaPorRol(string $nombre, string $clave, string $rol): ?Usuario {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from usuarios where nombre=:nombre and rol=:rol';
        $sth = $this->bd->prepare($sql);
        $sth->execute([":nombre" => $nombre, ":rol" => $rol]);
        $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Usuario::class);
        $usuario = ($sth->fetch()) ?: null;

        if ($usuario && password_verify($clave, $usuario->getClave())) {
            return $usuario;
        } else {
            return null;
        }
    }

    public function obtenerTodos(): array {
        $sql = "select * from usuarios";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, Usuario::class);
        $usuarios = $stmt->fetchAll();
        $stmt->closeCursor();
        return $usuarios;
    }

    /**
     * Hashear las contraseñas de todos los usuarios en la base de datos.
     */
    public function hashearContraseñas() {
        $sql = "select id, clave from usuarios";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as $usuario) {
            $hashedPassword = password_hash($usuario['clave'], PASSWORD_BCRYPT);
            $updateSql = "update usuarios set clave = :clave where id = :id";
            $updateStmt = $this->bd->prepare($updateSql);
            $updateStmt->execute([":clave" => $hashedPassword, ":id" => $usuario['id']]);
        }
    }
}

