-- ==========================================
-- BASE DE DATOS
-- Proyecto: Certificados Digitales
-- ==========================================

CREATE DATABASE IF NOT EXISTS certificados_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE certificados_db;

-- ==========================================
-- TABLA ROLES
-- ==========================================

CREATE TABLE roles (

    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(150)

);

-- ==========================================
-- TABLA USUARIOS
-- ==========================================

CREATE TABLE usuarios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombres VARCHAR(100) NOT NULL,

    apellidos VARCHAR(100) NOT NULL,

    documento VARCHAR(20) NOT NULL UNIQUE,

    correo VARCHAR(120) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    telefono VARCHAR(20),

    estado ENUM('Activo','Inactivo') DEFAULT 'Activo',

    rol_id INT NOT NULL,

    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (rol_id)
        REFERENCES roles(id)

);

-- ==========================================
-- TABLA CERTIFICADOS
-- ==========================================

CREATE TABLE certificados (

    id INT AUTO_INCREMENT PRIMARY KEY,

    codigo VARCHAR(100) NOT NULL UNIQUE,

    usuario_id INT NOT NULL,

    fecha_emision DATE NOT NULL,

    fecha_vencimiento DATE NOT NULL,

    estado ENUM(
        'Activo',
        'Revocado',
        'Vencido'
    ) DEFAULT 'Activo',

    CONSTRAINT fk_certificado_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)

);

-- ==========================================
-- TABLA SOLICITUDES
-- ==========================================

CREATE TABLE solicitudes (

    id INT AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT NOT NULL,

    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    estado ENUM(
        'Pendiente',
        'Aprobada',
        'Rechazada'
    ) DEFAULT 'Pendiente',

    observaciones TEXT,

    CONSTRAINT fk_solicitud_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)

);

-- ==========================================
-- TABLA BITACORA
-- ==========================================

CREATE TABLE bitacora (

    id INT AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT,

    accion VARCHAR(150),

    descripcion TEXT,

    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bitacora_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)

);

-- ==========================================
-- DATOS INICIALES
-- ==========================================

INSERT INTO roles (nombre, descripcion)
VALUES
('Administrador','Control total del sistema'),
('Usuario','Solicita certificados'),
('Verificador','Valida certificados');