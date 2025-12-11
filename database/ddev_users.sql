-- Script para crear usuarios de desarrollo con DDEV
-- Ejecutar después de init.sql

USE sistema_reservas;

-- Usuario Administrador para desarrollo
INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo) VALUES
('Admin', 'Admin', 'Desarrollo', 'admin@dev.com', '600000001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-12-11', TRUE);

-- Usuario Cliente normal para desarrollo  
INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo) VALUES
('Cliente', 'Usuario', 'Normal', 'usuario@dev.com', '600000002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-12-11', TRUE);

-- Mostrar los usuarios creados
SELECT id_usuario, rol, nombre, apellidos, email FROM USUARIO WHERE email IN ('admin@dev.com', 'usuario@dev.com');