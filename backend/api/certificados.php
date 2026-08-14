<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    requireMethod('GET');
    $user = requireUser();
    $sql = 'SELECT c.id, c.codigo, c.fecha_emision, c.fecha_vencimiento, c.estado,
                   u.nombres, u.apellidos, u.documento
            FROM certificados c INNER JOIN usuarios u ON u.id = c.usuario_id';
    $params = [];
    if ($user['rol'] !== 'Administrador') {
        $sql .= ' WHERE c.usuario_id = :usuario_id';
        $params[':usuario_id'] = $user['id'];
    }
    $sql .= ' ORDER BY c.fecha_emision DESC';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    respond(true, 'Certificados consultados.', $stmt->fetchAll());
} catch (PDOException $exception) {
    respond(false, 'No fue posible consultar los certificados.', null, 500);
}
