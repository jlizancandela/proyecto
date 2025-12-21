DROP DATABASE IF EXISTS sistema_reservas;

CREATE DATABASE sistema_reservas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'db'@'%' IDENTIFIED BY 'db';

GRANT ALL PRIVILEGES ON sistema_reservas.* TO 'db'@'%';

FLUSH PRIVILEGES;

USE sistema_reservas;

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET CHARACTER SET utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_results = utf8mb4;

CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    rol VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    fecha_registro DATE NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    reset_token VARCHAR(64),
    reset_expiration DATETIME,
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_reset_token (reset_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ESPECIALISTA (
    id_especialista INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    descripcion VARCHAR(255),
    foto_url VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE,
    INDEX idx_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE SERVICIO (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL,
    duracion_minutos INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    descripcion VARCHAR(255),
    INDEX idx_nombre (nombre_servicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ESPECIALISTA_SERVICIO (
    id_especialista INT NOT NULL,
    id_servicio INT NOT NULL,
    PRIMARY KEY (id_especialista, id_servicio),
    FOREIGN KEY (id_especialista) REFERENCES ESPECIALISTA(id_especialista) ON DELETE CASCADE,
    FOREIGN KEY (id_servicio) REFERENCES SERVICIO(id_servicio) ON DELETE CASCADE
);

CREATE TABLE HORARIO_ESPECIALISTA (
    id_horario INT AUTO_INCREMENT PRIMARY KEY,
    id_especialista INT NOT NULL,
    dia_semana INT NOT NULL CHECK (dia_semana BETWEEN 0 AND 6),
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    FOREIGN KEY (id_especialista) REFERENCES ESPECIALISTA(id_especialista) ON DELETE CASCADE,
    INDEX idx_especialista_dia (id_especialista, dia_semana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE RESERVA (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_especialista INT NOT NULL,
    id_servicio INT NOT NULL,
    fecha_reserva DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado VARCHAR(50) DEFAULT 'Pendiente',
    observaciones VARCHAR(500),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_especialista) REFERENCES ESPECIALISTA(id_especialista) ON DELETE CASCADE,
    FOREIGN KEY (id_servicio) REFERENCES SERVICIO(id_servicio) ON DELETE CASCADE,
    INDEX idx_fecha (fecha_reserva),
    INDEX idx_cliente (id_cliente),
    INDEX idx_especialista (id_especialista),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo) VALUES
('Admin', 'Carlos', 'González Pérez', 'admin@sistema.com', '912345678', '$2y$10$ZOEVKOGb44x2zjczuRq0YOiBoN7px1CnZmnESIpIJhgdzTe.5fiwa', '2024-01-15', TRUE),
('Cliente', 'María', 'López Martínez', 'maria.lopez@email.com', '678901234', '$2y$10$RMUPJU2gnGMg/0VYe3dId.cuDfaG31tgkhQMQ1d0R8XMBZLswgdBS', '2024-02-20', TRUE),
('Cliente', 'Juan', 'Rodríguez Sánchez', 'juan.rodriguez@email.com', '654321098', '$2y$10$usxwIQorDzJiu8RQy2s.bugHTecPwZYWtJJ2UQKtMIW35r9QMB.xe', '2024-03-10', TRUE),
('Especialista', 'Ana', 'Fernández García', 'ana.fernandez@email.com', '611223344', '$2y$10$Z.9x2sSUuw6aVyVMpqMyluf2c6RQwMbaqedd75E.13FdTpN/E3PsO', '2024-01-20', TRUE),
('Especialista', 'Pedro', 'Martín Díaz', 'pedro.martin@email.com', '622334455', '$2y$10$1oT942mC6nQFUT4ViOwuz.zFk9UnWzMBYnz6brSBVhohhqznj.eSq', '2024-01-25', TRUE),
('Especialista', 'Laura', 'Sánchez Ruiz', 'laura.sanchez@email.com', '633445566', '$2y$10$rdJa.m5r/48KF66Pmac4quCDZpNRgWwg0QxzNM3au/6q/IqbDEKMC', '2024-02-01', TRUE),
('Cliente', 'David', 'Torres Jiménez', 'david.torres@email.com', '644556677', '$2y$10$Htj8oo8pdMprUKUNjAj/Feyjrcha4x./a68XgKRokxFSGa7ndogba', '2024-03-15', TRUE),
('Cliente', 'Elena', 'Gómez Moreno', 'elena.gomez@email.com', '655667788', '$2y$10$8e9RV/BcB2olDnCOGbfkieO1oGhy9SMS8y63wdKZrBSKlclTh8LUS', '2024-04-01', TRUE);

INSERT INTO ESPECIALISTA (id_usuario, descripcion, foto_url) VALUES
(4, 'Especialista en cortes y peinados modernos con 10 años de experiencia', '/images/ana.jpg'),
(5, 'Experto en coloración y tratamientos capilares avanzados', '/images/pedro.jpg'),
(6, 'Especialista en manicura, pedicura y tratamientos de belleza', '/images/laura.jpg');

INSERT INTO SERVICIO (nombre_servicio, duracion_minutos, precio, descripcion) VALUES
('Corte de Cabello Mujer', 45, 35.00, 'Corte y acabado profesional para mujer'),
('Corte de Cabello Hombre', 30, 25.00, 'Corte moderno y clásico para hombre'),
('Tinte Completo', 120, 85.00, 'Coloración completa del cabello con productos premium'),
('Mechas/Reflejos', 90, 75.00, 'Mechas o reflejos con técnicas modernas'),
('Peinado Evento', 60, 50.00, 'Peinado profesional para eventos especiales'),
('Tratamiento Capilar', 45, 40.00, 'Tratamiento hidratante y reparador'),
('Manicura', 45, 30.00, 'Manicura completa con esmaltado'),
('Pedicura', 60, 40.00, 'Pedicura completa con cuidado de pies'),
('Uñas Gel/Acrílico', 90, 55.00, 'Aplicación de uñas de gel o acrílico'),
('Depilación Facial', 30, 20.00, 'Depilación de cejas y labio superior');

INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES
-- Ana ofrece servicios de cabello
(1, 1), (1, 2), (1, 5), (1, 6),
-- Pedro ofrece servicios de coloración y tratamientos
(2, 3), (2, 4), (2, 6),
-- Laura ofrece servicios de estética y uñas
(3, 7), (3, 8), (3, 9), (3, 10);

-- Horarios de Ana (Lunes a Viernes)
INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
(1, 1, '09:00:00', '14:00:00'),
(1, 1, '16:00:00', '20:00:00'),
(1, 2, '09:00:00', '14:00:00'),
(1, 2, '16:00:00', '20:00:00'),
(1, 3, '09:00:00', '14:00:00'),
(1, 3, '16:00:00', '20:00:00'),
(1, 4, '09:00:00', '14:00:00'),
(1, 4, '16:00:00', '20:00:00'),
(1, 5, '09:00:00', '14:00:00'),
(1, 5, '16:00:00', '20:00:00');

-- Horarios de Pedro (Martes a Sábado)
INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
(2, 2, '10:00:00', '14:00:00'),
(2, 2, '16:00:00', '21:00:00'),
(2, 3, '10:00:00', '14:00:00'),
(2, 3, '16:00:00', '21:00:00'),
(2, 4, '10:00:00', '14:00:00'),
(2, 4, '16:00:00', '21:00:00'),
(2, 5, '10:00:00', '14:00:00'),
(2, 5, '16:00:00', '21:00:00'),
(2, 6, '10:00:00', '15:00:00');

-- Horarios de Laura (Lunes a Sábado)
INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
(3, 1, '09:00:00', '14:00:00'),
(3, 1, '15:00:00', '19:00:00'),
(3, 2, '09:00:00', '14:00:00'),
(3, 2, '15:00:00', '19:00:00'),
(3, 3, '09:00:00', '14:00:00'),
(3, 3, '15:00:00', '19:00:00'),
(3, 4, '09:00:00', '14:00:00'),
(3, 4, '15:00:00', '19:00:00'),
(3, 5, '09:00:00', '14:00:00'),
(3, 5, '15:00:00', '19:00:00'),
(3, 6, '09:00:00', '14:00:00');

-- Insertar RESERVAS de ejemplo
INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado, observaciones) VALUES
(2, 1, 1, '2024-11-15', '10:00:00', '10:45:00', 'Confirmada', 'Primera visita, requiere asesoramiento'),
(3, 2, 3, '2024-11-16', '11:00:00', '13:00:00', 'Confirmada', 'Cliente habitual'),
(7, 3, 7, '2024-11-17', '16:00:00', '16:45:00', 'Pendiente', NULL),
(8, 1, 5, '2024-11-18', '18:00:00', '19:00:00', 'Confirmada', 'Peinado para boda'),
(2, 3, 8, '2024-11-19', '10:00:00', '11:00:00', 'Pendiente', NULL),
(3, 1, 2, '2024-11-20', '09:00:00', '09:30:00', 'Confirmada', NULL),
(7, 2, 4, '2024-11-22', '17:00:00', '18:30:00', 'Cancelada', 'Cliente canceló por motivos personales'),
(8, 3, 9, '2024-11-23', '11:00:00', '12:30:00', 'Confirmada', 'Diseño especial solicitado');


