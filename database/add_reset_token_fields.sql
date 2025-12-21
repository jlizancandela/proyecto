-- Migración para añadir campos de recuperación de contraseña a la tabla USUARIO
-- Fecha: 2025-12-20

USE sistema_reservas;

-- Añadir campos reset_token y reset_expiration
ALTER TABLE USUARIO 
ADD COLUMN reset_token VARCHAR(64) AFTER activo,
ADD COLUMN reset_expiration DATETIME AFTER reset_token,
ADD INDEX idx_reset_token (reset_token);
