SET NAMES 'utf8mb4';
-- 1. BASE DE DATOS Y USUARIO
DROP DATABASE IF EXISTS sistema_reservas;
CREATE DATABASE sistema_reservas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'db'@'%' IDENTIFIED BY 'db';
GRANT ALL PRIVILEGES ON sistema_reservas.* TO 'db'@'%';
FLUSH PRIVILEGES;

USE sistema_reservas;

-- 2. TABLAS
CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    rol ENUM('Admin', 'Especialista', 'Cliente') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    fecha_registro DATE NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    reset_token VARCHAR(64),
    reset_expiration DATETIME
) ENGINE=InnoDB;

CREATE TABLE ESPECIALISTA (
    id_especialista INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    descripcion VARCHAR(255),
    foto_url VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE SERVICIO (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL,
    duracion_minutos INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    descripcion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

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
    dia_semana INT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    FOREIGN KEY (id_especialista) REFERENCES ESPECIALISTA(id_especialista) ON DELETE CASCADE
);

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
    FOREIGN KEY (id_servicio) REFERENCES SERVICIO(id_servicio) ON DELETE CASCADE
);

-- 3. SERVICIOS
INSERT INTO SERVICIO (nombre_servicio, duracion_minutos, precio, descripcion) VALUES
('Corte de Pelo', 30, 20.00, 'Lavado y corte profesional'),
('Tinte y Color', 90, 45.00, 'Coloración completa'),
('Peinado y Secado', 45, 25.00, 'Lavado y peinado'),
('Hidratación Pro', 40, 35.00, 'Tratamiento nutritivo'),
('Arreglo de Barba', 20, 15.00, 'Perfilado y cuidado');

-- 4. USUARIO ADMIN
-- Password: S3cur3P@ssw0rd!
INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro) 
VALUES ('Admin', 'Admin', 'Sistema', 'sr.tu.peluqueria+admin@gmail.com', '600000000', '$2y$12$QN97xPIT..vOECDhZYzaKOxzg1iZKa6DZEaW9j.CVjaCdMqMTlRcy', '2025-01-01');

-- 5. 20 ESPECIALISTAS (10 Mujeres, 10 Hombres)
-- Password: Sp3ci@l1st!
INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro) VALUES
('Especialista','Ana','G1','sr.tu.peluqueria+esp1@gmail.com','666000001','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Laura','G2','sr.tu.peluqueria+esp2@gmail.com','666000002','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Elena','G3','sr.tu.peluqueria+esp3@gmail.com','666000003','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Sofia','G4','sr.tu.peluqueria+esp4@gmail.com','666000004','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Lucia','G5','sr.tu.peluqueria+esp5@gmail.com','666000005','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Marta','G6','sr.tu.peluqueria+esp6@gmail.com','666000006','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Sara','G7','sr.tu.peluqueria+esp7@gmail.com','666000007','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Julia','G8','sr.tu.peluqueria+esp8@gmail.com','666000008','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Paula','G9','sr.tu.peluqueria+esp9@gmail.com','666000009','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Nerea','G10','sr.tu.peluqueria+esp10@gmail.com','666000010','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Pedro','H1','sr.tu.peluqueria+esp11@gmail.com','666000011','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Carlos','H2','sr.tu.peluqueria+esp12@gmail.com','666000012','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Marcos','H3','sr.tu.peluqueria+esp13@gmail.com','666000013','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Diego','H4','sr.tu.peluqueria+esp14@gmail.com','666000014','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Javi','H5','sr.tu.peluqueria+esp15@gmail.com','666000015','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Raul','H6','sr.tu.peluqueria+esp16@gmail.com','666000016','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Ivan','H7','sr.tu.peluqueria+esp17@gmail.com','666000017','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Hugo','H8','sr.tu.peluqueria+esp18@gmail.com','666000018','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Marc','H9','sr.tu.peluqueria+esp19@gmail.com','666000019','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01'),
('Especialista','Oscar','H10','sr.tu.peluqueria+esp20@gmail.com','666000020','$2y$12$bAWG1eK.g7K/oWWXyOQqF.K/0LvVFNjuFGcqe.7KFrPvVj129UORi','2025-01-01');

-- 6. PERFILES DE ESPECIALISTA (URLs Optimizadas)
INSERT INTO ESPECIALISTA (id_usuario, descripcion, foto_url) VALUES
(2, 'Experta en color', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2004.jpg?tr=w-300,h-300,q-80,f-auto'),
(3, 'Estilista senior', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2001.jpg?tr=w-300,h-300,q-80,f-auto'),
(4, 'Especialista en peinados', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2003.jpg?tr=w-300,h-300,q-80,f-auto'),
(5, 'Experta en color', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2004.jpg?tr=w-300,h-300,q-80,f-auto'),
(6, 'Estilista senior', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2001.jpg?tr=w-300,h-300,q-80,f-auto'),
(7, 'Especialista en peinados', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2003.jpg?tr=w-300,h-300,q-80,f-auto'),
(8, 'Experta en color', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2004.jpg?tr=w-300,h-300,q-80,f-auto'),
(9, 'Estilista senior', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2001.jpg?tr=w-300,h-300,q-80,f-auto'),
(10, 'Especialista en peinados', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2003.jpg?tr=w-300,h-300,q-80,f-auto'),
(11, 'Experta en color', 'https://ik.imagekit.io/jlc84/peluqueria/mujer%2004.jpg?tr=w-300,h-300,q-80,f-auto'),
(12, 'Barbero profesional', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2001.jpg?tr=w-300,h-300,q-80,f-auto'),
(13, 'Estilista masculino', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2003.jpg?tr=w-300,h-300,q-80,f-auto'),
(14, 'Experto en degradados', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2002.jpg?tr=w-300,h-300,q-80,f-auto'),
(15, 'Barbero profesional', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2001.jpg?tr=w-300,h-300,q-80,f-auto'),
(16, 'Estilista masculino', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2003.jpg?tr=w-300,h-300,q-80,f-auto'),
(17, 'Experto en degradados', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2002.jpg?tr=w-300,h-300,q-80,f-auto'),
(18, 'Barbero profesional', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2001.jpg?tr=w-300,h-300,q-80,f-auto'),
(19, 'Estilista masculino', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2003.jpg?tr=w-300,h-300,q-80,f-auto'),
(20, 'Experto en degradados', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2002.jpg?tr=w-300,h-300,q-80,f-auto'),
(21, 'Barbero profesional', 'https://ik.imagekit.io/jlc84/peluqueria/hombre%2001.jpg?tr=w-300,h-300,q-80,f-auto');

-- 7. 20 CLIENTES
-- Password: C1i3ntP@ssw0rd!
INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro) VALUES
('Cliente','Juan','C1','sr.tu.peluqueria+cli1@gmail.com','600123001','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Maria','C2','sr.tu.peluqueria+cli2@gmail.com','600123002','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Luis','C3','sr.tu.peluqueria+cli3@gmail.com','600123003','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Rosa','C4','sr.tu.peluqueria+cli4@gmail.com','600123004','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Jose','C5','sr.tu.peluqueria+cli5@gmail.com','600123005','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Carmen','C6','sr.tu.peluqueria+cli6@gmail.com','600123006','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Fran','C7','sr.tu.peluqueria+cli7@gmail.com','600123007','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Ines','C8','sr.tu.peluqueria+cli8@gmail.com','600123008','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Alex','C9','sr.tu.peluqueria+cli9@gmail.com','600123009','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Lola','C10','sr.tu.peluqueria+cli10@gmail.com','600123010','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Dani','C11','sr.tu.peluqueria+cli11@gmail.com','600123011','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Bea','C12','sr.tu.peluqueria+cli12@gmail.com','600123012','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Toni','C13','sr.tu.peluqueria+cli13@gmail.com','600123013','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Eva','C14','sr.tu.peluqueria+cli14@gmail.com','600123014','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Hugo','C15','sr.tu.peluqueria+cli15@gmail.com','600123015','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Sara','C16','sr.tu.peluqueria+cli16@gmail.com','600123016','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Rafa','C17','sr.tu.peluqueria+cli17@gmail.com','600123017','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Noa','C18','sr.tu.peluqueria+cli18@gmail.com','600123456','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Leo','C19','sr.tu.peluqueria+cli19@gmail.com','600123019','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01'),
('Cliente','Mia','C20','sr.tu.peluqueria+cli20@gmail.com','600123020','$2y$12$srpX683wul7Y4tYPdpk4rexELgnZGXKi5kit8YA7kUsM7aQcYgvsa','2025-01-01');

-- 8. ASIGNACIONES (Especialistas aleatorios a servicios aleatorios)
INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio) VALUES
(1, 1), (1, 2), (1, 3),
(2, 4), (2, 5), (2, 1),
(3, 2), (3, 3), (3, 4),
(4, 5), (4, 1), (4, 2),
(5, 3), (5, 4), (5, 5),
(6, 1), (6, 2), (6, 3),
(7, 4), (7, 5), (7, 1),
(8, 2), (8, 3), (8, 4),
(9, 5), (9, 1), (9, 2),
(10, 3), (10, 4), (10, 5),
(11, 1), (11, 2), (11, 3),
(12, 4), (12, 5), (12, 1),
(13, 2), (13, 3), (13, 4),
(14, 5), (14, 1), (14, 2),
(15, 3), (15, 4), (15, 5),
(16, 1), (16, 2), (16, 3),
(17, 4), (17, 5), (17, 1),
(18, 2), (18, 3), (18, 4),
(19, 5), (19, 1), (19, 2),
(20, 3), (20, 4), (20, 5);

-- 9. HORARIOS (Lun-Vie 9-14 para algunos, Mar-Sab 10-15 para otros)
INSERT INTO HORARIO_ESPECIALISTA (id_especialista, dia_semana, hora_inicio, hora_fin) VALUES
-- Grupo 1 (Lun-Vie)
(1, 1, '09:00', '14:00'), (1, 2, '09:00', '14:00'), (1, 3, '09:00', '14:00'), (1, 4, '09:00', '14:00'), (1, 5, '09:00', '14:00'),
(2, 1, '09:00', '14:00'), (2, 2, '09:00', '14:00'), (2, 3, '09:00', '14:00'), (2, 4, '09:00', '14:00'), (2, 5, '09:00', '14:00'),
(3, 1, '09:00', '14:00'), (3, 2, '09:00', '14:00'), (3, 3, '09:00', '14:00'), (3, 4, '09:00', '14:00'), (3, 5, '09:00', '14:00'),
(4, 1, '09:00', '14:00'), (4, 2, '09:00', '14:00'), (4, 3, '09:00', '14:00'), (4, 4, '09:00', '14:00'), (4, 5, '09:00', '14:00'),
(5, 1, '09:00', '14:00'), (5, 2, '09:00', '14:00'), (5, 3, '09:00', '14:00'), (5, 4, '09:00', '14:00'), (5, 5, '09:00', '14:00'),
-- Grupo 2 (Mar-Sab)
(11, 2, '10:00', '15:00'), (11, 3, '10:00', '15:00'), (11, 4, '10:00', '15:00'), (11, 5, '10:00', '15:00'), (11, 6, '10:00', '15:00'),
(12, 2, '10:00', '15:00'), (12, 3, '10:00', '15:00'), (12, 4, '10:00', '15:00'), (12, 5, '10:00', '15:00'), (12, 6, '10:00', '15:00'),
(13, 2, '10:00', '15:00'), (13, 3, '10:00', '15:00'), (13, 4, '10:00', '15:00'), (13, 5, '10:00', '15:00'), (13, 6, '10:00', '15:00'),
(14, 2, '10:00', '15:00'), (14, 3, '10:00', '15:00'), (14, 4, '10:00', '15:00'), (14, 5, '10:00', '15:00'), (14, 6, '10:00', '15:00'),
(15, 2, '10:00', '15:00'), (15, 3, '10:00', '15:00'), (15, 4, '10:00', '15:00'), (15, 5, '10:00', '15:00'), (15, 6, '10:00', '15:00');

-- 10. RESERVAS DE EJEMPLO
INSERT INTO RESERVA (id_cliente, id_especialista, id_servicio, fecha_reserva, hora_inicio, hora_fin, estado) VALUES
(22, 1, 1, '2026-02-01', '09:00:00', '09:30:00', 'Confirmada'),
(23, 1, 2, '2026-02-01', '10:00:00', '11:30:00', 'Pendiente'),
(24, 2, 4, '2026-02-01', '10:00:00', '10:40:00', 'Confirmada'),
(25, 3, 3, '2026-02-02', '11:00:00', '11:45:00', 'Confirmada'),
(26, 4, 1, '2026-02-03', '12:00:00', '12:30:00', 'Pendiente'),
(27, 5, 5, '2026-02-04', '13:00:00', '13:20:00', 'Completada'),
(28, 11, 2, '2026-02-05', '10:00:00', '11:30:00', 'Confirmada'),
(29, 12, 1, '2026-02-06', '10:30:00', '11:00:00', 'Pendiente'),
(30, 13, 3, '2026-02-07', '11:00:00', '11:45:00', 'Confirmada'),
(31, 14, 4, '2026-02-08', '09:00:00', '09:40:00', 'Cancelada');
