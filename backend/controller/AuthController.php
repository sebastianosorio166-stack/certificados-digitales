<?php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    private $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();
    }

    /**
     * Iniciar sesión
     */
    public function login($correo, $password)
    {
        $usuario = $this->usuario->obtenerPorCorreo($correo);

        if (!$usuario) {

            return [
                "status" => false,
                "mensaje" => "El usuario no existe."
            ];

        }

        if (!password_verify($password, $usuario["password"])) {

            return [
                "status" => false,
                "mensaje" => "Contraseña incorrecta."
            ];

        }

        unset($usuario["password"]);

        return [
            "status" => true,
            "mensaje" => "Inicio de sesión exitoso.",
            "usuario" => $usuario
        ];
    }
}