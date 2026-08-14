<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $user = requireUser();
    $pdo = db();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $sql = 'SELECT s.id, s.usuario_id, s.fecha_solicitud, s.estado, s.observaciones,
                       u.nombres, u.apellidos, u.documento, u.correo, u.telefono
                FROM solicitudes s INNER JOIN usuarios u ON u.id = s.usuario_id';
        $params = [];
        if ($user['rol'] !== 'Administrador') {
            $sql .= ' WHERE s.usuario_id = :usuario_id';
            $params[':usuario_id'] = $user['id'];
        }
        $sql .= ' ORDER BY s.fecha_solicitud DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        respond(true, 'Solicitudes consultadas.', $stmt->fetchAll());
    }

    if ($method === 'POST') {
        $data = body();
        $pending = $pdo->prepare('SELECT id FROM solicitudes WHERE usuario_id = :usuario_id AND estado = "Pendiente"');
        $pending->execute([':usuario_id' => $user['id']]);
        if ($pending->fetch()) {
            respond(false, 'Ya tienes una solicitud pendiente.', null, 409);
        }

        $stmt = $pdo->prepare('INSERT INTO solicitudes (usuario_id, observaciones) VALUES (:usuario_id, :observaciones)');
        $stmt->execute([
            ':usuario_id' => $user['id'],
            ':observaciones' => text($data, 'observaciones'),
        ]);
        $requestId = (int) $pdo->lastInsertId();
        logAction($pdo, (int) $user['id'], 'Solicitud creada', 'El usuario envió una solicitud de certificado.');
        respond(true, 'Solicitud enviada para revisión.', ['id' => $requestId], 201);
    }

    if ($method === 'PATCH') {
        requireAdmin();
        $data = body();
        $id = (int) ($data['id'] ?? 0);
        $accion = text($data, 'accion');
        if ($id <= 0 || !in_array($accion, ['aprobar', 'rechazar'], true)) {
            respond(false, 'Indica una solicitud y una acción válida.', null, 422);
        }
        if ($accion === 'rechazar' && text($data, 'observaciones') === '') {
            respond(false, 'Indica el motivo del rechazo.', null, 422);
        }

        $pdo->beginTransaction();
        $request = $pdo->prepare('SELECT id, usuario_id FROM solicitudes WHERE id = :id AND estado = "Pendiente" FOR UPDATE');
        $request->execute([':id' => $id]);
        $solicitud = $request->fetch();
        if ($solicitud === false) {
            $pdo->rollBack();
            respond(false, 'La solicitud no existe o ya fue gestionada.', null, 404);
        }

        $estado = $accion === 'aprobar' ? 'Aprobada' : 'Rechazada';
        $observaciones = text($data, 'observaciones');
        $update = $pdo->prepare('UPDATE solicitudes SET estado = :estado, observaciones = :observaciones WHERE id = :id');
        $update->execute([':estado' => $estado, ':observaciones' => $observaciones, ':id' => $id]);

        if ($accion === 'aprobar') {
            $years = (int) ($data['vigencia'] ?? 1);
            if (!in_array($years, [1, 2, 3], true)) {
                $pdo->rollBack();
                respond(false, 'La vigencia debe ser de 1, 2 o 3 años.', null, 422);
            }
            $issued = new DateTimeImmutable('today');
            $expires = $issued->modify("+{$years} years");
            $certificate = $pdo->prepare(
                'INSERT INTO certificados (codigo, usuario_id, fecha_emision, fecha_vencimiento, estado)
                 VALUES (:codigo, :usuario_id, :fecha_emision, :fecha_vencimiento, "Activo")'
            );
            $certificate->execute([
                ':codigo' => 'CERT-' . date('Y') . '-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT),
                ':usuario_id' => $solicitud['usuario_id'],
                ':fecha_emision' => $issued->format('Y-m-d'),
                ':fecha_vencimiento' => $expires->format('Y-m-d'),
            ]);
        }

        logAction($pdo, (int) $user['id'], "Solicitud {$estado}", $observaciones ?: "Solicitud {$estado} por un administrador.");

        $pdo->commit();
        respond(true, "Solicitud {$estado} correctamente.");
    }

    requireMethod('GET', 'POST', 'PATCH');
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    respond(false, 'No fue posible completar la operación.', null, 500);
}
