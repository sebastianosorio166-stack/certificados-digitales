<?php

require_once __DIR__ . '/../models/Usuario.php';

/**
 * -------------------------------------------------------
 * Controlador de Autenticación
 * Proyecto: Sistema de Gestión de Certificados Digitales
 * SENA - GA7
 * -------------------------------------------------------
 */

class AuthController
{
    private $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();
    }

    /**
     * Iniciar sesión
     *
     * @param array $datos
     * @return array
     */
    public function login($datos)
    {

        if (
            empty($datos["documento"]) ||
            empty($datos["password"])
        ) {

            return [

                "status" => false,

                "mensaje" => "Debe ingresar el documento y la contraseña.",

                "data" => null

            ];

        }

        $usuario = $this->usuario->login(

            $datos["documento"],

            $datos["password"]

        );

        if (!$usuario) {

            return [

                "status" => false,

                "mensaje" => "Documento o contraseña incorrectos.",

                "data" => null

            ];

        }

        return [

            "status" => true,

            "mensaje" => "Inicio de sesión exitoso.",

            "data" => $usuario

        ];

    }

}