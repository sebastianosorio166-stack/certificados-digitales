<?php

require_once __DIR__ . '/../config/Database.php';

class Bitacora
{
    private $conexion;
    private $tabla = "bitacora";

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    /**
     * Registrar acción
     */
    public function registrar($usuario_id, $accion, $descripcion)
    {
        $sql = "INSERT INTO {$this->tabla}

        (
            usuario_id,
            accion,
            descripcion
        )

        VALUES

        (
            :usuario_id,
            :accion,
            :descripcion
        )";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            ":usuario_id" => $usuario_id,

            ":accion" => $accion,

            ":descripcion" => $descripcion

        ]);
    }

    /**
     * Obtener registros
     */
    public function obtenerTodos()
    {
        $sql = "SELECT * FROM {$this->tabla}
                ORDER BY fecha DESC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}