<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../controllers/SolicitudController.php';

$controller = new SolicitudController();

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":

        if (isset($_GET["id"])) {

            echo json_encode(
                $controller->show($_GET["id"])
            );

        } else {

            echo json_encode(
                $controller->index()
            );

        }

        break;

    case "POST":

        $datos = json_decode(file_get_contents("php://input"), true);

        echo json_encode(
            $controller->store($datos)
        );

        break;

    case "PUT":

        if (!isset($_GET["id"])) {

            echo json_encode([
                "status" => false,
                "mensaje" => "Debe indicar el ID."
            ]);

            exit;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        echo json_encode(
            $controller->update($_GET["id"], $datos)
        );

        break;

    case "DELETE":

        if (!isset($_GET["id"])) {

            echo json_encode([
                "status" => false,
                "mensaje" => "Debe indicar el ID."
            ]);

            exit;
        }

        echo json_encode(
            $controller->destroy($_GET["id"])
        );

        break;

    default:

        http_response_code(405);

        echo json_encode([
            "status" => false,
            "mensaje" => "Método HTTP no permitido."
        ]);
}