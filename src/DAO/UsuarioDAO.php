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

    
    public function crea(Usuario $usuario): bool {
        $sql = "insert into usuarios (nombre, clave, email) values (:nombre, :clave, :email)";
        $sth = $this->bd->prepare($sql);
        $result = $sth->execute([":nombre" => $usuario->getNombre(), ":clave" => $usuario->getClave(), ":email" => $usuario->getEmail()]);
        return ($result);
    }

    public function modifica($usuario) {
        $sql = "update usuarios set nombre = :nombre, clave = :clave, email = :email where id = :id";
        $sth = $this->bd->prepare($sql);
        $result = $sth->execute([":nombre" => $usuario->getNombre(), ":clave" => $usuario->getClave(), ":email" => $usuario->getEmail(), ":id" => $usuario->getId()]);
        return ($result);
    }

    public function elimina(int $id) {
        $sql = "delete from usuarios where id = :id";
        $sth = $this->bd->prepare($sql);
        $result = $sth->execute([":id" => $id]);
        return ($result);
    }

    public function recuperaPorCredencial(string $nombre, string $clave): ?Usuario {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from usuarios where nombre=:nombre and clave=:pwd';
        $sth = $this->bd->prepare($sql);
        $sth->execute([":nombre" => $nombre, ":pwd" => $clave]);
        $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Usuario::class);
        $usuario = ($sth->fetch()) ?: null;
        return $usuario;
    }

     public function recuperaPorCredencialHashed(string $nombre, string $clave): ?Usuario {
        // Hashear la contraseña proporcionada usando SHA-256
        $pwdHashed = hash('sha256', $clave);
        // Seleccionar el usuario que coincida con el nombre y la contraseña hasheada
        $sql = 'SELECT * FROM usuarios WHERE usuario = :nombre AND clave = :pwdHashed';
        $sth = $this->bd->prepare($sql);
        $sth->execute([":nombre" => $nombre, ":pwdHashed" => $pwdHashed]);
        $sth->setFetchMode(PDO::FETCH_CLASS, Usuario::class);
        $usuario = ($sth->fetch()) ?: null;
        return $usuario;
    }

    public function recuperaPorRol(string $nombre, string $clave, string $rol): ?Usuario {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from usuarios where nombre=:nombre and clave=:pwd and rol=:rol';
        $sth = $this->bd->prepare($sql);
        $sth->execute([":nombre" => $nombre, ":pwd" => $clave, ":rol" => $rol]);
        $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Usuario::class);
        $usuario = ($sth->fetch()) ?: null;
        return $usuario;
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

    public function asignarRolAdministrador($idUsuario) {
        $sql = "UPDATE usuarios SET rol = 'administrador' WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $result = $stmt->execute();
        return ($result);
    }

    public function hashearContraseñasSHA() {
        // Seleccionar id y clave de todos los usuarios
        $sql = "SELECT id, clave FROM usuarios";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as $usuario) {
            // Hashear la contraseña usando SHA-256
            $hashedPassword = hash('sha256', $usuario['clave']);

            // Actualizar la base de datos con la nueva contraseña hasheada
            $updateSql = "UPDATE usuarios SET clave = :clave WHERE id = :id";
            $updateStmt = $this->bd->prepare($updateSql);
            $updateStmt->execute([
                ":clave" => $hashedPassword,
                ":id" => $usuario['id']
            ]);
        }
    }
    
    public function quitarHashContraseñas() {
        // Array de contraseñas originales, esto debería venir de una fuente segura
        $contraseñasOriginales = [ // Tenemos que seleccionar uno a uno los id para otorgarles contraseñas
            17 => '123456',
            20 => '123456',
            3 => '123456',
            // Añade más contraseñas originales según sea necesario
        ];

        foreach ($contraseñasOriginales as $id => $clave) {
            $updateSql = "UPDATE usuarios SET clave = :clave WHERE id = :id";
            $updateStmt = $this->bd->prepare($updateSql);
            $updateStmt->execute([
                ":clave" => $clave,
                ":id" => $id
            ]);
        }
    }

    public function quitarHashContraseñasAuto(string $nuevaClave) {
        // Seleccionar todos los IDs de los usuarios
        $sql = "SELECT id FROM usuarios";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as $usuario) {
            // Actualizar la base de datos con la nueva contraseña en texto plano
            $updateSql = "UPDATE usuarios SET clave = :clave WHERE id = :id";
            $updateStmt = $this->bd->prepare($updateSql);
            $updateStmt->execute([
                ":clave" => $nuevaClave,
                ":id" => $usuario['id']
            ]);
        }
    }
    
       }