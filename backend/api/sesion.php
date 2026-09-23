<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    // CONSULTAR SESIÓN ACTUAL
    if ($method === 'GET') {
        respond(true, 'Sesión consultada.', currentUser());
    }

    requireMethod('POST');

    $data = body();

    $action = text($data, 'accion');

    // Si no viene accion pero vienen documento y contraseña,
    // asumimos que es un inicio de sesión.
    if (
        $action === '' &&
        text($data, 'documento') !== '' &&
        text($data, 'password') !== ''
    ) {
        $action = 'login';
    }

    // =========================================================
    // REGISTRO
    // =========================================================
    if ($action === 'registro') {

        $required = [
            'nombres',
            'apellidos',
            'documento',
            'correo',
            'password'
        ];

        foreach ($required as $field) {
            if (text($data, $field) === '') {
                respond(
                    false,
                    'Completa todos los campos obligatorios.',
                    null,
                    422
                );
            }
        }

        if (!filter_var(text($data, 'correo'), FILTER_VALIDATE_EMAIL)) {
            respond(
                false,
                'El correo no tiene un formato válido.',
                null,
                422
            );
        }

        $pdo = db();

        // Buscar el rol Usuario
        $stmtRole = $pdo->prepare(
            "SELECT id FROM roles WHERE nombre = 'Usuario' LIMIT 1"
        );

        $stmtRole->execute();

        $roleId = $stmtRole->fetchColumn();

        if ($roleId === false) {
            respond(
                false,
                'No existe el rol Usuario en la base de datos.',
                null,
                500
            );
        }

        // Insertar usuario
        // IMPORTANTE:
        // La contraseña se guarda directamente como texto,
        // según lo solicitado para este prototipo académico.
        $stmt = $pdo->prepare(
            'INSERT INTO usuarios
            (
                nombres,
                apellidos,
                documento,
                correo,
                password,
                telefono,
                rol_id
            )
            VALUES
            (
                :nombres,
                :apellidos,
                :documento,
                :correo,
                :password,
                :telefono,
                :rol_id
            )'
        );

        $stmt->execute([
            ':nombres' => text($data, 'nombres'),
            ':apellidos' => text($data, 'apellidos'),
            ':documento' => text($data, 'documento'),
            ':correo' => text($data, 'correo'),
            ':password' => text($data, 'password'),
            ':telefono' => text($data, 'telefono'),
            ':rol_id' => (int) $roleId
        ]);

        $nuevoUsuarioId = (int) $pdo->lastInsertId();

        // Registrar actividad
        logAction(
            $pdo,
            $nuevoUsuarioId,
            'Registro',
            'Cuenta creada.'
        );

        respond(
            true,
            'Usuario registrado. Ya puedes iniciar sesión.',
            null,
            201
        );
    }

    // =========================================================
    // LOGIN
    // =========================================================
    if ($action === 'login') {

        $documento = text($data, 'documento');
        $password = text($data, 'password');

        if ($documento === '' || $password === '') {
            respond(
                false,
                'Ingresa documento y contraseña.',
                null,
                422
            );
        }

        $pdo = db();

        $stmt = $pdo->prepare(
            'SELECT
                u.id,
                u.nombres,
                u.apellidos,
                u.documento,
                u.correo,
                u.telefono,
                u.password,
                u.estado,
                r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.documento = :documento
             AND u.estado = "Activo"
             LIMIT 1'
        );

        $stmt->execute([
            ':documento' => $documento
        ]);

        $user = $stmt->fetch();

        if ($user === false) {
            respond(
                false,
                'Documento o contraseña incorrectos.',
                null,
                401
            );
        }

        // Comparación sencilla de contraseña
        if ($password !== $user['password']) {
            respond(
                false,
                'Documento o contraseña incorrectos.',
                null,
                401
            );
        }

        unset($user['password']);

        session_regenerate_id(true);

        $_SESSION['usuario'] = $user;

        logAction(
            $pdo,
            (int) $user['id'],
            'Inicio de sesión',
            'El usuario inició sesión.'
        );

        respond(
            true,
            'Inicio de sesión exitoso.',
            $user
        );
    }

    // =========================================================
    // LOGOUT
    // =========================================================
    if ($action === 'logout') {

        $user = currentUser();

        if ($user !== null) {
            $pdo = db();

            logAction(
                $pdo,
                (int) $user['id'],
                'Cierre de sesión',
                'El usuario cerró sesión.'
            );
        }

        $_SESSION = [];

        session_destroy();

        respond(
            true,
            'Sesión finalizada correctamente.'
        );
    }

    respond(
        false,
        'Acción no válida.',
        null,
        400
    );

} catch (PDOException $exception) {

    // Mostrar temporalmente el error real para poder
    // identificar el problema durante las pruebas.
    respond(
        false,
        'Error de base de datos: ' . $exception->getMessage(),
        null,
        500
    );

} catch (Throwable $exception) {

    respond(
        false,
        'Error del servidor: ' . $exception->getMessage(),
        null,
        500
    );
}