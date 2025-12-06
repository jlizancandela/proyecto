USE sistema_reservas;

INSERT INTO USUARIO (nombre, apellidos, email, telefono, password_hash, rol, activo, fecha_registro) VALUES
('Carlos', 'García López', 'carlos.garcia@email.com', '612345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('Ana', 'Martínez Ruiz', 'ana.martinez@email.com', '623456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('Luis', 'Fernández Soto', 'luis.fernandez@email.com', '634567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Especialista', 1, NOW()),
('Elena', 'Rodríguez Pérez', 'elena.rodriguez@email.com', '645678901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('Miguel', 'Sánchez Torres', 'miguel.sanchez@email.com', '656789012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Especialista', 1, NOW()),
('Laura', 'González Díaz', 'laura.gonzalez@email.com', '667890123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('David', 'López Moreno', 'david.lopez@email.com', '678901234', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 0, NOW()),
('Carmen', 'Hernández Vega', 'carmen.hernandez@email.com', '689012345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Especialista', 1, NOW()),
('Javier', 'Jiménez Castro', 'javier.jimenez@email.com', '690123456', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('Isabel', 'Ruiz Ortiz', 'isabel.ruiz@email.com', '601234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('Antonio', 'Morales Gil', 'antonio.morales@email.com', '612345679', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Especialista', 1, NOW()),
('Marta', 'Navarro Ramos', 'marta.navarro@email.com', '623456780', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('Francisco', 'Romero Silva', 'francisco.romero@email.com', '634567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 0, NOW()),
('Beatriz', 'Alonso Campos', 'beatriz.alonso@email.com', '645678902', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Especialista', 1, NOW()),
('Roberto', 'Gutiérrez Molina', 'roberto.gutierrez@email.com', '656789013', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('Cristina', 'Vargas Prieto', 'cristina.vargas@email.com', '667890124', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('Pablo', 'Castro Núñez', 'pablo.castro@email.com', '678901235', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Especialista', 1, NOW()),
('Raquel', 'Ortega Medina', 'raquel.ortega@email.com', '689012346', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 1, NOW()),
('Sergio', 'Delgado Iglesias', 'sergio.delgado@email.com', '690123457', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 0, NOW()),
('Silvia', 'Cabrera Fuentes', 'silvia.cabrera@email.com', '601234568', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Especialista', 1, NOW());
