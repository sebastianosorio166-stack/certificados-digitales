<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../controllers/BitacoraController.php';

$controller = new BitacoraController();

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":

        echo json_encode(

            $controller->index()

        );

        break;

    case "POST":

        $datos = json_decode(file_get_contents("php://input"), true);

        echo json_encode(

            $controller->store($datos)

        );

        break;

    default:

        http_response_code(405);

        echo json_encode([

            "status" => false,

            "mensaje" => "Método HTTP no permitido."

        ]);

}
