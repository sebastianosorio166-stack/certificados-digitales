<?php

require_once __DIR__ . '/../models/Bitacora.php';

class BitacoraController
{
    private $bitacora;

    public function __construct()
    {
        $this->bitacora = new Bitacora();
    }

    /**
     * Listar registros
     */
    public function index()
    {
        return $this->bitacora->obtenerTodos();
    }

    /**
     * Registrar acción
     */
    public function store($datos)
    {
        if (
            empty($datos["usuario_id"]) ||
            empty($datos["accion"])
        ) {

            return [

                "status" => false,

                "mensaje" => "Información incompleta."

            ];

        }

        $resultado = $this->bitacora->registrar(

            $datos["usuario_id"],

            $datos["accion"],

            $datos["descripcion"]

        );

        if ($resultado) {

            return [

                "status" => true,

                "mensaje" => "Registro almacenado."

            ];

        }

        return [

            "status" => false,

            "mensaje" => "No fue posible registrar."

        ];

    }

}