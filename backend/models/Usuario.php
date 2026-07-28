<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * Modelo Usuario
 * Proyecto: Sistema de Gestión de Certificados Digitales
 * SENA - GA7
 */

class Usuario
{
    private $conexion;
    private $tabla = "usuarios";

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos()
    {
        $sql = "SELECT
                    id,
                    nombres,
                    apellidos,
                    documento,
                    correo,
                    telefono,
                    estado,
                    rol_id,
                    fecha_registro
                FROM {$this->tabla}";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT
                    id,
                    nombres,
                    apellidos,
                    documento,
                    correo,
                    telefono,
                    estado,
                    rol_id,
                    fecha_registro
                FROM {$this->tabla}
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(":id", $id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar usuario por documento
     */
    public function obtenerPorDocumento($documento)
    {
        $sql = "SELECT *
                FROM {$this->tabla}
                WHERE documento = :documento
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(":documento", $documento);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Registrar usuario
     */
    public function crear($datos)
    {
        $sql = "INSERT INTO {$this->tabla}

        (
            nombres,
            apellidos,
            documento,
            correo,
            password,
            telefono,
            rol_id
        )

        VALUES

        (
            :nombres,
            :apellidos,
            :documento,
            :correo,
            :password,
            :telefono,
            :rol_id
        )";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            ":nombres" => $datos["nombres"],

            ":apellidos" => $datos["apellidos"],

            ":documento" => $datos["documento"],

            ":correo" => $datos["correo"],

            ":password" => password_hash(
                $datos["password"],
                PASSWORD_DEFAULT
            ),

            ":telefono" => $datos["telefono"],

            ":rol_id" => $datos["rol_id"]

        ]);
    }

    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos)
    {
        $sql = "UPDATE {$this->tabla}

                SET

                nombres = :nombres,

                apellidos = :apellidos,

                documento = :documento,

                correo = :correo,

                telefono = :telefono,

                rol_id = :rol_id

                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            ":nombres" => $datos["nombres"],

            ":apellidos" => $datos["apellidos"],

            ":documento" => $datos["documento"],

            ":correo" => $datos["correo"],

            ":telefono" => $datos["telefono"],

            ":rol_id" => $datos["rol_id"],

            ":id" => $id

        ]);
    }

    /**
     * Eliminar usuario
     */
    public function eliminar($id)
    {
        $sql = "DELETE FROM {$this->tabla}
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            ":id" => $id

        ]);
    }

    /**
     * Validar credenciales
     */
    public function login($documento, $password)
    {
        $usuario = $this->obtenerPorDocumento($documento);

        if (!$usuario) {

            return false;

        }

        if (!password_verify($password, $usuario["password"])) {

            return false;

        }

        unset($usuario["password"]);

        return $usuario;
    }
}