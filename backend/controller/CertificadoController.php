<?php

require_once __DIR__ . '/../models/Certificado.php';

class CertificadoController
{
    private $certificado;

    public function __construct()
    {
        $this->certificado = new Certificado();
    }

    /**
     * Obtener todos los certificados
     */
    public function index()
    {
        return $this->certificado->obtenerTodos();
    }

    /**
     * Obtener certificado por ID
     */
    public function show($id)
    {
        return $this->certificado->obtenerPorId($id);
    }

    /**
     * Crear certificado
     */
    public function store($datos)
    {

        if (
            empty($datos["codigo"]) ||
            empty($datos["usuario_id"]) ||
            empty($datos["fecha_emision"]) ||
            empty($datos["fecha_vencimiento"])
        ) {

            return [
                "status" => false,
                "mensaje" => "Debe completar todos los campos."
            ];

        }

        $resultado = $this->certificado->crear($datos);

        if ($resultado) {

            return [
                "status" => true,
                "mensaje" => "Certificado registrado correctamente."
            ];

        }

        return [
            "status" => false,
            "mensaje" => "No fue posible registrar el certificado."
        ];

    }

    /**
     * Actualizar certificado
     */
    public function update($id, $datos)
    {

        $resultado = $this->certificado->actualizar($id, $datos);

        if ($resultado) {

            return [
                "status" => true,
                "mensaje" => "Certificado actualizado correctamente."
            ];

        }

        return [
            "status" => false,
            "mensaje" => "No fue posible actualizar el certificado."
        ];

    }

    /**
     * Eliminar certificado
     */
    public function destroy($id)
    {

        $resultado = $this->certificado->eliminar($id);

        if ($resultado) {

            return [
                "status" => true,
                "mensaje" => "Certificado eliminado correctamente."
            ];

        }

        return [
            "status" => false,
            "mensaje" => "No fue posible eliminar el certificado."
        ];

    }

}