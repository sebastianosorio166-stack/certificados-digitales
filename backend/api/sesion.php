<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($method === 'GET') {
        respond(true, 'Sesión consultada.', currentUser());
    }

    requireMethod('POST');
    $data = body();
    $action = text($data, 'accion');
    if ($action === '' && text($data, 'documento') !== '' && text($data, 'password') !== '') {
        $action = 'login';
    }

    if ($action === 'registro') {
        $required = ['nombres', 'apellidos', 'documento', 'correo', 'password'];
        foreach ($required as $field) {
            if (text($data, $field) === '') {
                respond(false, 'Completa todos los campos obligatorios.', null, 422);
            }
        }

        if (!filter_var(text($data, 'correo'), FILTER_VALIDATE_EMAIL)) {
            respond(false, 'El correo no tiene un formato válido.', null, 422);
        }

        $pdo = db();
        $roleId = (int) $pdo->query("SELECT id FROM roles WHERE nombre = 'Usuario' LIMIT 1")->fetchColumn();
        if ($roleId === 0) {
            respond(false, 'No existe el rol Usuario en la base de datos.', null, 500);
        }

        $stmt = $pdo->prepare(
            'INSERT INTO usuarios (nombres, apellidos, documento, correo, password, telefono, rol_id)
             VALUES (:nombres, :apellidos, :documento, :correo, :password, :telefono, :rol_id)'
        );
        $stmt->execute([
            ':nombres' => text($data, 'nombres'),
            ':apellidos' => text($data, 'apellidos'),
            ':documento' => text($data, 'documento'),
            ':correo' => text($data, 'correo'),
            ':password' => password_hash(text($data, 'password'), PASSWORD_DEFAULT),
            ':telefono' => text($data, 'telefono'),
            ':rol_id' => $roleId,
        ]);

        logAction($pdo, (int) $pdo->lastInsertId(), 'Registro', 'Cuenta creada.');

        respond(true, 'Usuario registrado. Ya puedes iniciar sesión.', null, 201);
    }

    if ($action === 'login') {
        $documento = text($data, 'documento');
        $password = text($data, 'password');
        if ($documento === '' || $password === '') {
            respond(false, 'Ingresa documento y contraseña.', null, 422);
        }

        $stmt = db()->prepare(
            'SELECT u.id, u.nombres, u.apellidos, u.documento, u.correo, u.telefono, u.password, r.nombre AS rol
             FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.documento = :documento AND u.estado = "Activo" LIMIT 1'
        );
        $stmt->execute([':documento' => $documento]);
        $user = $stmt->fetch();
        if ($user === false || !password_verify($password, $user['password'])) {
            respond(false, 'Documento o contraseña incorrectos.', null, 401);
        }

        unset($user['password']);
        session_regenerate_id(true);
        $_SESSION['usuario'] = $user;
        logAction(db(), (int) $user['id'], 'Inicio de sesión', 'El usuario inició sesión.');
        respond(true, 'Inicio de sesión exitoso.', $user);
    }

    if ($action === 'logout') {
        $user = currentUser();
        if ($user !== null) {
            logAction(db(), (int) $user['id'], 'Cierre de sesión', 'El usuario cerró sesión.');
        }
        $_SESSION = [];
        session_destroy();
        respond(true, 'Sesión finalizada correctamente.');
    }

    respond(false, 'Acción no válida.', null, 400);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        respond(false, 'El documento o correo ya está registrado.', null, 409);
    }
    respond(false, 'No fue posible completar la operación.', null, 500);
}
