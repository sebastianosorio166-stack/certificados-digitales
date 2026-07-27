<?php

require_once __DIR__ . '/../config/Database.php';

class Solicitud
{
    private $conexion;
    private $tabla = "solicitudes";

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    public function obtenerTodas()
    {
        $sql = "SELECT * FROM {$this->tabla}";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM {$this->tabla}
                WHERE id=:id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id"=>$id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos)
    {
        $sql = "INSERT INTO {$this->tabla}

        (
            usuario_id,
            observaciones
        )

        VALUES

        (
            :usuario_id,
            :observaciones
        )";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            ":usuario_id"=>$datos["usuario_id"],

            ":observaciones"=>$datos["observaciones"]

        ]);

    }

    public function actualizar($id,$datos)
    {
        $sql="UPDATE {$this->tabla}

        SET

        estado=:estado,

        observaciones=:observaciones

        WHERE id=:id";

        $stmt=$this->conexion->prepare($sql);

        return $stmt->execute([

            ":estado"=>$datos["estado"],

            ":observaciones"=>$datos["observaciones"],

            ":id"=>$id

        ]);

    }

    public function eliminar($id)
    {
        $sql="DELETE FROM {$this->tabla}

        WHERE id=:id";

        $stmt=$this->conexion->prepare($sql);

        return $stmt->execute([

            ":id"=>$id

        ]);
    }

}