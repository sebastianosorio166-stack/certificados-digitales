<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
requireMethod('POST');
$user = currentUser();
if ($user !== null) {
    logAction(db(), (int) $user['id'], 'Cierre de sesión', 'El usuario cerró sesión.');
}
$_SESSION = [];
session_destroy();
respond(true, 'Sesión finalizada correctamente.');
