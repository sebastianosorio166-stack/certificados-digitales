<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

header('Content-Type: application/json; charset=UTF-8');
$sessionPath = __DIR__ . '/../storage/sessions';
if (!is_dir($sessionPath) && !mkdir($sessionPath, 0775, true) && !is_dir($sessionPath)) {
    throw new RuntimeException('No fue posible preparar el almacenamiento de sesiones.');
}
session_save_path($sessionPath);
session_start();

function respond(bool $status, string $mensaje, mixed $data = null, int $code = 200): never
{
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'mensaje' => $mensaje,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    $data = json_decode($raw, true);
    if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
        respond(false, 'El cuerpo debe ser un JSON válido.', null, 400);
    }

    return $data;
}

function db(): PDO
{
    return (new Database())->conectar();
}

function currentUser(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function requireUser(): array
{
    $user = currentUser();
    if ($user === null) {
        respond(false, 'Debes iniciar sesión.', null, 401);
    }

    return $user;
}

function requireAdmin(): array
{
    $user = requireUser();
    if ($user['rol'] !== 'Administrador') {
        respond(false, 'No tienes permisos para esta acción.', null, 403);
    }

    return $user;
}

function requireMethod(string ...$allowed): void
{
    if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', $allowed, true)) {
        header('Allow: ' . implode(', ', $allowed));
        respond(false, 'Método HTTP no permitido.', null, 405);
    }
}

function text(array $data, string $key): string
{
    return trim((string) ($data[$key] ?? ''));
}

function logAction(PDO $pdo, ?int $userId, string $action, string $description): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO bitacora (usuario_id, accion, descripcion) VALUES (:usuario_id, :accion, :descripcion)'
    );
    $stmt->execute([
        ':usuario_id' => $userId,
        ':accion' => $action,
        ':descripcion' => $description,
    ]);
}
