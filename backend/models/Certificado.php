<?php

require_once __DIR__ . '/../config/Database.php';

class Certificado
{
    private $conexion;
    private $tabla = "certificados";

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    /**
     * Obtener todos los certificados
     */
    public function obtenerTodos()
    {
        $sql = "SELECT * FROM {$this->tabla}";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener certificado por ID
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM {$this->tabla}
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id" => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear certificado
     */
    public function crear($datos)
    {
        $sql = "INSERT INTO {$this->tabla}
        (
            codigo,
            usuario_id,
            fecha_emision,
            fecha_vencimiento,
            estado
        )
        VALUES
        (
            :codigo,
            :usuario_id,
            :fecha_emision,
            :fecha_vencimiento,
            :estado
        )";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":codigo" => $datos["codigo"],
            ":usuario_id" => $datos["usuario_id"],
            ":fecha_emision" => $datos["fecha_emision"],
            ":fecha_vencimiento" => $datos["fecha_vencimiento"],
            ":estado" => $datos["estado"]
        ]);
    }

    /**
     * Actualizar certificado
     */
    public function actualizar($id, $datos)
    {
        $sql = "UPDATE {$this->tabla}
                SET
                    codigo = :codigo,
                    fecha_emision = :fecha_emision,
                    fecha_vencimiento = :fecha_vencimiento,
                    estado = :estado
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":codigo" => $datos["codigo"],
            ":fecha_emision" => $datos["fecha_emision"],
            ":fecha_vencimiento" => $datos["fecha_vencimiento"],
            ":estado" => $datos["estado"],
            ":id" => $id
        ]);
    }

    /**
     * Eliminar certificado
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
}