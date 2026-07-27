<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../controllers/AuthController.php';

$controller = new AuthController();

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    http_response_code(405);

    echo json_encode([
        "status" => false,
        "mensaje" => "Método no permitido."
    ]);

    exit;

}

$datos = json_decode(file_get_contents("php://input"), true);

if (
    empty($datos["correo"]) ||
    empty($datos["password"])
) {

    echo json_encode([
        "status" => false,
        "mensaje" => "Debe enviar correo y contraseña."
    ]);

    exit;

}

echo json_encode(

    $controller->login(

        $datos["correo"],
        $datos["password"]

    )

);