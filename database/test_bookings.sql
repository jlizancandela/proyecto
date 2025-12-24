-- Insertar 30 reservas de ejemplo
-- Usando clientes: IDs 7, 17, 19, 20, 22, 24, 25, 27, 28, 30 (David, jhon, Carlos, Ana, Elena, Laura, David, Javier, Isabel, Marta)
-- Usando especialistas: IDs 1, 2, 3 (Ana, Pedro, Laura - de la tabla ESPECIALISTA)
-- Usando servicios: IDs 1-10

USE sistema_reservas;

INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado, observaciones) VALUES
-- Semana actual y pasada
(7, 1, 1, '2025-12-20', '09:00:00', '09:45:00', 'Completada', 'Cliente satisfecho con el resultado'),
(17, 1, 2, '2025-12-20', '10:00:00', '10:30:00', 'Completada', NULL),
(19, 2, 3, '2025-12-20', '09:00:00', '11:00:00', 'Completada', 'Cambio de color exitoso'),
(20, 3, 7, '2025-12-20', '16:00:00', '16:45:00', 'Completada', NULL),

(22, 2, 4, '2025-12-21', '10:00:00', '11:30:00', 'Completada', 'Mechas californianas'),
(24, 3, 8, '2025-12-21', '11:00:00', '12:00:00', 'Completada', NULL),
(25, 1, 5, '2025-12-21', '16:00:00', '17:00:00', 'Completada', 'Peinado para boda'),
(27, 1, 1, '2025-12-21', '17:30:00', '18:15:00', 'Completada', NULL),

(28, 3, 9, '2025-12-22', '09:00:00', '10:30:00', 'Completada', 'Uñas acrílicas francesas'),
(30, 2, 6, '2025-12-22', '10:00:00', '10:45:00', 'Completada', 'Tratamiento keratina'),
(31, 1, 2, '2025-12-22', '11:00:00', '11:30:00', 'Completada', NULL),
(34, 2, 3, '2025-12-22', '16:00:00', '18:00:00', 'Cancelada', 'Cliente canceló por enfermedad'),

-- Semana próxima (Pendientes y Confirmadas)
(7, 1, 1, '2025-12-26', '09:00:00', '09:45:00', 'Confirmada', NULL),
(17, 1, 2, '2025-12-26', '10:00:00', '10:30:00', 'Confirmada', NULL),
(19, 2, 3, '2025-12-26', '09:00:00', '11:00:00', 'Pendiente', NULL),
(20, 3, 7, '2025-12-26', '16:00:00', '16:45:00', 'Confirmada', NULL),

(22, 2, 4, '2025-12-27', '10:00:00', '11:30:00', 'Pendiente', NULL),
(24, 3, 8, '2025-12-27', '11:00:00', '12:00:00', 'Confirmada', NULL),
(25, 1, 5, '2025-12-27', '16:00:00', '17:00:00', 'Confirmada', 'Peinado para evento corporativo'),
(27, 1, 1, '2025-12-27', '17:30:00', '18:15:00', 'Pendiente', NULL),

(28, 3, 9, '2025-12-28', '09:00:00', '10:30:00', 'Confirmada', NULL),
(30, 2, 6, '2025-12-28', '10:00:00', '10:45:00', 'Pendiente', NULL),
(31, 1, 2, '2025-12-28', '11:00:00', '11:30:00', 'Confirmada', NULL),
(34, 2, 3, '2025-12-28', '16:00:00', '18:00:00', 'Pendiente', NULL),

-- Más reservas para enero 2026
(37, 1, 1, '2026-01-02', '09:00:00', '09:45:00', 'Pendiente', NULL),
(44, 1, 2, '2026-01-02', '10:00:00', '10:30:00', 'Pendiente', NULL),
(45, 2, 3, '2026-01-02', '09:00:00', '11:00:00', 'Pendiente', NULL),
(7, 3, 7, '2026-01-02', '16:00:00', '16:45:00', 'Pendiente', NULL),

(17, 2, 4, '2026-01-03', '10:00:00', '11:30:00', 'Pendiente', NULL),
(19, 3, 8, '2026-01-03', '11:00:00', '12:00:00', 'Pendiente', NULL);
