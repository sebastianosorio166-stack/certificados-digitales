# Sistema de Certificados Digitales

Aplicación sencilla para registrar usuarios, solicitar certificados y gestionarlos desde un panel administrativo.

## Flujo

1. Un usuario crea una cuenta e inicia sesión.
2. Envía una solicitud de certificado o renovación.
3. Un administrador aprueba o rechaza la solicitud.
4. Al aprobarla, el sistema genera un certificado activo con vigencia de 1, 2 o 3 años.

La base de datos es la única fuente de información. El navegador no guarda usuarios, roles ni contraseñas en `localStorage`.

## Estructura

```text
frontend/              Pantallas HTML y cliente JavaScript
backend/api/           Endpoints HTTP
backend/config/        Conexión PDO
backend/storage/       Archivos temporales de sesión PHP
database/certificados.sql  Esquema y roles iniciales
```

## Instalación local

1. Importa `database/certificados.sql` en MySQL.
2. Configura XAMPP o tu servidor PHP para servir la carpeta del proyecto.
3. Ajusta las variables de entorno `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` y `DB_PORT` si tus valores difieren de los predeterminados. En esta instalación de XAMPP, MySQL usa el puerto `3307`.
4. Abre `frontend/index.html` mediante el mismo servidor web que sirve `backend/`.

Para tener un administrador, crea un usuario normalmente y cambia su `rol_id` en la base de datos al ID del rol `Administrador`.

## API principal

| Endpoint | Método | Uso |
|---|---:|---|
| `api/sesion.php` | GET | Consultar sesión actual |
| `api/sesion.php` | POST | Registro, inicio y cierre de sesión |
| `api/usuarios.php` | GET / PUT | Consultar o editar perfil |
| `api/solicitudes.php` | GET / POST / PATCH | Consultar, crear o gestionar solicitudes |
| `api/certificados.php` | GET | Consultar certificados |
| `api/bitacora.php` | GET | Consultar bitácora (administrador) |

Todas las respuestas usan el formato `status`, `mensaje` y `data`.
