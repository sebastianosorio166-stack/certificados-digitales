<?php

/**
 * -------------------------------------------------------
 * API Login
 * Proyecto: Sistema de Gestión de Certificados Digitales
 * SENA - GA7
 * -------------------------------------------------------
 */

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../controllers/AuthController.php';

$controller = new AuthController();

/**
 * Solo se permite el método POST
 */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([

        "status" => false,

        "mensaje" => "Método HTTP no permitido.",

        "data" => null

    ]);

    exit;

}

/**
 * Leer datos enviados en formato JSON
 */

$datos = json_decode(file_get_contents("php://input"), true);

/**
 * Validar que exista información
 */

if (!$datos) {

    http_response_code(400);

    echo json_encode([

        "status" => false,

        "mensaje" => "No se recibieron datos.",

        "data" => null

    ]);

    exit;

}

/**
 * Ejecutar autenticación
 */

$resultado = $controller->login($datos);

/**
 * Respuesta
 */

if ($resultado["status"]) {

    http_response_code(200);

} else {

    http_response_code(401);

}

echo json_encode($resultado);

exit;