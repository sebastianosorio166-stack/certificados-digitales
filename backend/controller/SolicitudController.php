<?php

require_once __DIR__ . '/../models/Solicitud.php';

class SolicitudController
{
    private $solicitud;

    public function __construct()
    {
        $this->solicitud = new Solicitud();
    }

    /**
     * Obtener todas las solicitudes
     */
    public function index()
    {
        return $this->solicitud->obtenerTodas();
    }

    /**
     * Obtener solicitud por ID
     */
    public function show($id)
    {
        return $this->solicitud->obtenerPorId($id);
    }

    /**
     * Registrar solicitud
     */
    public function store($datos)
    {
        if (empty($datos["usuario_id"])) {

            return [
                "status" => false,
                "mensaje" => "Debe indicar el usuario."
            ];
        }

        $resultado = $this->solicitud->crear($datos);

        if ($resultado) {

            return [
                "status" => true,
                "mensaje" => "Solicitud registrada correctamente."
            ];
        }

        return [
            "status" => false,
            "mensaje" => "No fue posible registrar la solicitud."
        ];
    }

    /**
     * Actualizar solicitud
     */
    public function update($id, $datos)
    {
        $resultado = $this->solicitud->actualizar($id, $datos);

        if ($resultado) {

            return [
                "status" => true,
                "mensaje" => "Solicitud actualizada correctamente."
            ];
        }

        return [
            "status" => false,
            "mensaje" => "No fue posible actualizar la solicitud."
        ];
    }

    /**
     * Eliminar solicitud
     */
    public function destroy($id)
    {
        $resultado = $this->solicitud->eliminar($id);

        if ($resultado) {

            return [
                "status" => true,
                "mensaje" => "Solicitud eliminada correctamente."
            ];
        }

        return [
            "status" => false,
            "mensaje" => "No fue posible eliminar la solicitud."
        ];
    }
}