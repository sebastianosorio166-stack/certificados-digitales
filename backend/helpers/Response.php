<?php

class Response
{

    public static function success($mensaje = "", $data = [])
    {

        http_response_code(200);

        echo json_encode([

            "status" => true,

            "mensaje" => $mensaje,

            "data" => $data

        ]);

        exit;

    }

    public static function error($mensaje = "", $codigo = 400)
    {

        http_response_code($codigo);

        echo json_encode([

            "status" => false,

            "mensaje" => $mensaje,

            "data" => null

        ]);

        exit;

    }

}