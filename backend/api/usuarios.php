<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $user = requireUser();
    $pdo = db();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if ($user['rol'] === 'Administrador') {
            $users = $pdo->query(
                'SELECT u.id, u.nombres, u.apellidos, u.documento, u.correo, u.telefono, u.estado, r.nombre AS rol
                 FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id ORDER BY u.nombres, u.apellidos'
            )->fetchAll();
            respond(true, 'Usuarios consultados.', $users);
        }

        respond(true, 'Perfil consultado.', $user);
    }

    requireMethod('PUT');
    $data = body();
    foreach (['nombres', 'apellidos', 'correo'] as $field) {
        if (text($data, $field) === '') {
            respond(false, 'Completa nombres, apellidos y correo.', null, 422);
        }
    }
    if (!filter_var(text($data, 'correo'), FILTER_VALIDATE_EMAIL)) {
        respond(false, 'El correo no tiene un formato válido.', null, 422);
    }

    $stmt = $pdo->prepare(
        'UPDATE usuarios SET nombres = :nombres, apellidos = :apellidos, correo = :correo, telefono = :telefono
         WHERE id = :id'
    );
    $stmt->execute([
        ':nombres' => text($data, 'nombres'),
        ':apellidos' => text($data, 'apellidos'),
        ':correo' => text($data, 'correo'),
        ':telefono' => text($data, 'telefono'),
        ':id' => $user['id'],
    ]);

    $_SESSION['usuario'] = array_merge($user, [
        'nombres' => text($data, 'nombres'),
        'apellidos' => text($data, 'apellidos'),
        'correo' => text($data, 'correo'),
        'telefono' => text($data, 'telefono'),
    ]);
    respond(true, 'Perfil actualizado.', $_SESSION['usuario']);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        respond(false, 'El correo ya está registrado.', null, 409);
    }
    respond(false, 'No fue posible completar la operación.', null, 500);
}
