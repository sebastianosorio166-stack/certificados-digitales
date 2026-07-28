# Documentación Técnica de la API REST

## Proyecto

Sistema de Gestión de Certificados Digitales

---

# Descripción

La API REST permite la comunicación entre el frontend y la base de datos del proyecto "Sistema de Gestión de Certificados Digitales". Está desarrollada en PHP utilizando el patrón Modelo-Vista-Controlador (MVC) y el acceso a datos mediante PDO.

La API recibe solicitudes HTTP, procesa la información mediante los controladores y devuelve respuestas en formato JSON.

---

# Arquitectura

```
Frontend (HTML + CSS + JavaScript)

↓

API REST

↓

Controladores

↓

Modelos

↓

Base de Datos MySQL
```

---

# Tecnologías utilizadas

- PHP 8
- MySQL
- PDO
- HTML5
- CSS3
- JavaScript
- JSON
- Git
- GitHub
- Postman

---

# Formato de respuesta

Todas las respuestas de la API se devuelven en formato JSON.

Ejemplo de respuesta correcta:

```json
{
    "status": true,
    "mensaje": "Proceso realizado correctamente.",
    "data": {}
}
```

Ejemplo de respuesta de error:

```json
{
    "status": false,
    "mensaje": "No fue posible completar la operación.",
    "data": null
}
```

---

# Métodos HTTP utilizados

| Método | Función |
|---------|----------|
| GET | Consultar información |
| POST | Registrar información |
| PUT | Actualizar información |
| DELETE | Eliminar información |

---

# Recursos disponibles

- Usuarios
- Certificados
- Solicitudes
- Bitácora
- Autenticación

-- ==========================================
-- USUARIOS
-- ==========================================
# API Usuarios

Endpoint

```
/backend/api/usuarios.php
```

---

## GET

Obtiene todos los usuarios registrados.

Respuesta:

```json
[
    {
        "id":1,
        "nombres":"Juan",
        "apellidos":"Pérez"
    }
]
```

---

## GET por ID

```
/backend/api/usuarios.php?id=1
```

Obtiene un usuario específico.

---

## POST

Registra un nuevo usuario.

Ejemplo:

```json
{
    "nombres":"Sebastián",
    "apellidos":"Osorio",
    "documento":"123456789",
    "correo":"usuario@correo.com",
    "password":"123456",
    "telefono":"3001234567",
    "rol_id":2
}
```

---

## PUT

Actualiza la información de un usuario.

```
/backend/api/usuarios.php?id=1
```

---

## DELETE

Elimina un usuario.

```
/backend/api/usuarios.php?id=1
```
-- ==========================================
-- CERTIFICADOS
-- ==========================================
# API Certificados

Endpoint

```
/backend/api/certificados.php
```

---

## GET

Obtiene todos los certificados.

---

## GET por ID

Obtiene un certificado específico.

---

## POST

Registra un certificado.

Ejemplo:

```json
{
    "codigo":"CERT-001",
    "usuario_id":1,
    "fecha_emision":"2026-06-15",
    "fecha_vencimiento":"2027-06-15",
    "estado":"Activo"
}
```

---

## PUT

Actualiza un certificado.

---

## DELETE

Elimina un certificado.

-- ==========================================
-- SOLICITUDES
-- ==========================================
# API Solicitudes

Endpoint

```
/backend/api/solicitudes.php
```

---

## GET

Consulta todas las solicitudes.

---

## GET por ID

Consulta una solicitud específica.

---

## POST

Registra una solicitud.

Ejemplo:

```json
{
    "usuario_id":1,
    "observaciones":"Solicitud inicial."
}
```

---

## PUT

Actualiza una solicitud.

---

## DELETE

Elimina una solicitud.

-- ==========================================
-- BITACORA
-- ==========================================
# API Bitácora

Endpoint

```
/backend/api/bitacora.php
```

---

## GET

Consulta los registros de la bitácora.

---

## POST

Registra una acción realizada por un usuario.

Ejemplo:

```json
{
    "usuario_id":1,
    "accion":"Inicio de sesión",
    "descripcion":"Ingreso al sistema."
}
```
-- ==========================================
-- AUTENTICACION
-- ==========================================
# API Autenticación

Endpoint

```
/backend/api/login.php
```

---

## POST

Permite autenticar a un usuario en el sistema.

El método recibirá las credenciales definidas para el proyecto:

```json
{
    "documento":"123456789",
    "password":"123456"
}
```

Respuesta esperada:

```json
{
    "status": true,
    "mensaje": "Inicio de sesión exitoso.",
    "data": {
        "id":1,
        "nombres":"Sebastián",
        "apellidos":"Osorio",
        "rol":"Administrador"
    }
}
```
