-- Archivo de pruebas: test_especialistas.sql
-- Añade varios especialistas de prueba junto con sus servicios y horarios.
-- Ejecutar contra la base de datos `sistema_reservas` (ajusta usuario/host si es necesario).

USE sistema_reservas;

-- Especialista 1: Carlos Ruiz (Corte de Cabello Hombre)
INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo) VALUES
('Especialista', 'Carlos', 'Ruiz', 'carlos.ruiz@test.local', '600111001', '$2y$10$placeholderhash1', CURDATE(), TRUE);
SET @u1 = LAST_INSERT_ID();
INSERT INTO ESPECIALISTA (id_usuario, descripcion, foto_url) VALUES
(@u1, 'Cortes modernos y clásicos para hombre', '/images/carlos.jpg');
SET @e1 = LAST_INSERT_ID();
INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES
(@e1, 2);
INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
(@e1, 1, '09:00:00', '13:00:00'),
(@e1, 3, '14:00:00', '18:00:00');

-- Especialista 2: Miguel Ortega (Corte de Cabello Hombre)
INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo) VALUES
('Especialista', 'Miguel', 'Ortega', 'miguel.ortega@test.local', '600111002', '$2y$10$placeholderhash2', CURDATE(), TRUE);
SET @u2 = LAST_INSERT_ID();
INSERT INTO ESPECIALISTA (id_usuario, descripcion, foto_url) VALUES
(@u2, 'Especialista en cortes rápidos y degradados', '/images/miguel.jpg');
SET @e2 = LAST_INSERT_ID();
INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES
(@e2, 2);
INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
(@e2, 2, '10:00:00', '14:00:00'),
(@e2, 4, '15:00:00', '19:00:00');

-- Especialista 3: Roberto Silva (Multi-servicio: Corte hombre + Peinado evento)
INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo) VALUES
('Especialista', 'Roberto', 'Silva', 'roberto.silva@test.local', '600111003', '$2y$10$placeholderhash3', CURDATE(), TRUE);
SET @u3 = LAST_INSERT_ID();
INSERT INTO ESPECIALISTA (id_usuario, descripcion, foto_url) VALUES
(@u3, 'Cortes y peinados para eventos', '/images/roberto.jpg');
SET @e3 = LAST_INSERT_ID();
INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES
(@e3, 2),
(@e3, 5);
INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
(@e3, 5, '09:00:00', '13:00:00'),
(@e3, 6, '12:00:00', '16:00:00');

-- Nota: los hashes de contraseña son marcadores de posición; no los uses en producción.
-- Para probar: ejecutar este archivo SQL en tu entorno de desarrollo y recargar /user/reservas/nueva.
