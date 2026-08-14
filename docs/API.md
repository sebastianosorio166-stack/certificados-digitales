# API REST

Base: `/backend/api`.

Todas las respuestas son JSON:

```json
{ "status": true, "mensaje": "Descripción", "data": {} }
```

## Sesión

`POST /sesion.php`

Registro:

```json
{ "accion": "registro", "nombres": "Ana", "apellidos": "Pérez", "documento": "123", "correo": "ana@example.com", "telefono": "3000000000", "password": "mínimo8" }
```

Inicio de sesión:

```json
{ "accion": "login", "documento": "123", "password": "mínimo8" }
```

Cierre de sesión: `POST /logout.php`.

`GET /sesion.php` devuelve el usuario autenticado o `null`.

## Solicitudes

- `GET /solicitudes.php`: el usuario recibe las propias; el administrador recibe todas.
- `POST /solicitudes.php`: crea una solicitud para el usuario autenticado.
- `PATCH /solicitudes.php`: solo administrador. Envía `{ "id": 1, "accion": "aprobar", "vigencia": 1 }` o `{ "id": 1, "accion": "rechazar", "observaciones": "Motivo" }`.

La aprobación crea un certificado automáticamente. Los certificados se consultan con `GET /certificados.php`.

## Perfil

`GET /usuarios.php` consulta el perfil; `PUT /usuarios.php` actualiza nombres, apellidos, correo y teléfono del usuario autenticado.
