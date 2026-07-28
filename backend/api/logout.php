<?php

/**
 * -------------------------------------------------------
 * API Logout
 * Proyecto: Sistema de Gestión de Certificados Digitales
 * SENA - GA7
 * -------------------------------------------------------
 */

header("Content-Type: application/json; charset=UTF-8");

session_start();

/**
 * Solo se permite POST
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
 * Destruir sesión
 */

session_unset();

session_destroy();

/**
 * Respuesta
 */

echo json_encode([

    "status" => true,

    "mensaje" => "Sesión finalizada correctamente.",

    "data" => null

]);

exit;