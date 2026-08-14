<?php

require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController
{
    private $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();
    }

    /**
     * Listar todos los usuarios
     */
    public function index()
    {
        return $this->usuario->obtenerTodos();
    }

    /**
     * Consultar un usuario por ID
     */
    public function show($id)
    {
        return $this->usuario->obtenerPorId($id);
    }

    /**
     * Registrar un usuario
     */
    public function store($datos)
    {

        if (
            empty($datos["nombres"]) ||
            empty($datos["apellidos"]) ||
            empty($datos["documento"]) ||
            empty($datos["correo"]) ||
            empty($datos["password"])
        ) {

            return [
                "status" => false,
                "mensaje" => "Todos los campos obligatorios deben estar completos."
            ];
        }

        $resultado = $this->usuario->crear($datos);

        if ($resultado) {

            return [
                "status" => true,
                "mensaje" => "Usuario registrado correctamente."
            ];
        }

        return [
            "status" => false,
            "mensaje" => "No fue posible registrar el usuario."
        ];
    }

    /**
     * Actualizar usuario
     */
    public function update($id, $datos)
    {

        $resultado = $this->usuario->actualizar($id, $datos);

        if ($resultado) {

            return [
                "status" => true,
                "mensaje" => "Usuario actualizado correctamente."
            ];
        }

        return [
            "status" => false,
            "mensaje" => "No fue posible actualizar el usuario."
        ];
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {

        $resultado = $this->usuario->eliminar($id);

        if ($resultado) {

            return [
                "status" => true,
                "mensaje" => "Usuario eliminado correctamente."
            ];
        }

        return [
            "status" => false,
            "mensaje" => "No fue posible eliminar el usuario."
        ];
    }
}