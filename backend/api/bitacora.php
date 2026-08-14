<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    requireMethod('GET');
    requireAdmin();
    $entries = db()->query(
        'SELECT b.id, b.accion, b.descripcion, b.fecha, u.nombres, u.apellidos, u.documento
         FROM bitacora b LEFT JOIN usuarios u ON u.id = b.usuario_id
         ORDER BY b.fecha DESC'
    )->fetchAll();
    respond(true, 'Bitácora consultada.', $entries);
} catch (PDOException $exception) {
    respond(false, 'No fue posible consultar la bitácora.', null, 500);
}
