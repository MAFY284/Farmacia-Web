-- ============================================================
-- SCRIPT DEFINITIVO DE BASE DE DATOS: INVENTORY (FARMACIA)
-- ============================================================
-- Base de datos: `inventory`
-- Puerto recomendado: 3308

CREATE DATABASE IF NOT EXISTS `inventory` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `inventory`;

-- 1. TABLA DE USUARIOS (Incluye ROL para permisos)
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('paciente', 'admin') DEFAULT 'paciente',
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABLA DE DOCTORES
CREATE TABLE IF NOT EXISTS `doctores` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `especialidad` VARCHAR(100) NOT NULL,
  `descripcion` TEXT,
  `foto` VARCHAR(255) DEFAULT 'Imgs/doctor_default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABLA DE CITAS MÉDICAS
CREATE TABLE IF NOT EXISTS `citas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT DEFAULT NULL,
  `doctor_id` INT DEFAULT 1,
  `nombre_paciente` VARCHAR(100) NOT NULL,
  `servicio` VARCHAR(100) NOT NULL,
  `fecha` DATE NOT NULL,
  `hora` TIME NOT NULL,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`doctor_id`) REFERENCES `doctores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABLA DE HISTORIAL MÉDICO
CREATE TABLE IF NOT EXISTS `historial_medico` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `fecha_consulta` DATE NOT NULL,
  `diagnostico` TEXT NOT NULL,
  `receta` TEXT NOT NULL,
  `medico` VARCHAR(100) NOT NULL,
  `notas` TEXT DEFAULT NULL,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABLA DE SUGERENCIAS Y RECLAMOS
CREATE TABLE IF NOT EXISTS `sugerencias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) DEFAULT 'Anónimo',
  `email` VARCHAR(100) NOT NULL,
  `mensaje` TEXT NOT NULL,
  `imagen` VARCHAR(255) DEFAULT NULL,
  `fecha_envio` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. TABLA DE PRODUCTOS
CREATE TABLE IF NOT EXISTS `productos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `precio` DECIMAL(10,2) NOT NULL,
  `imagen` VARCHAR(255) NOT NULL,
  `categoria` VARCHAR(50) NOT NULL,
  `tag` VARCHAR(50),
  `tag_class` VARCHAR(50),
  `oferta_popular` VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INSERCIÓN DE DATOS REALES (NOMBRES PROPORCIONADOS)
-- ============================================================

INSERT INTO `doctores` (`id`, `nombre`, `especialidad`, `descripcion`) VALUES
(1, 'Yahir Chaco', 'Médico General y Familiar', 'Especialista en atención integral con amplia experiencia en salud comunitaria.'),
(2, 'Maya-Tec', 'Especialista en Diagnóstico Digital', 'Experto en nuevas tecnologías aplicadas a la medicina preventiva.');

-- Usuarios / Pacientes Reales (Contraseña para todos: 'root')
-- Yahir Chaco será el Administrador (admin)
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`) VALUES
(1, 'Yahir Chaco', 'yahir@gmail.com', '123456', 'admin'),
(2, 'Pichandido', 'pichandido@gmail.com', '123456', 'paciente'),
(3, 'Candidato', 'candidato@gmail.com', '123456', 'paciente'),
(4, 'Sapondido', 'sapondido@gmail.com', '123456', 'paciente'),
(5, 'Adany', 'adany@gmail.com', '123456', 'paciente'),
(6, 'Cachis Lopez Lopez', 'cachis@gmail.com', '123456', 'paciente');

-- Historial médico para Pichandido (Usuario 2)
INSERT INTO `historial_medico` (`usuario_id`, `fecha_consulta`, `diagnostico`, `receta`, `medico`, `notas`) VALUES
(2, '2026-05-15', 'Gripe estacional.', 'Paracetamol 500mg cada 8 horas.', 'Yahir Chaco', 'Reposo absoluto por 48 horas.'),
(2, '2026-05-28', 'Chequeo de rutina anual.', 'Vitamina C suplementaria.', 'Maya-Tec', 'Paciente en excelente estado físico.');

-- Citas de ejemplo para el día de hoy
INSERT INTO `citas` (`usuario_id`, `nombre_paciente`, `servicio`, `fecha`, `hora`) VALUES
(2, 'Pichandido', 'Consulta General', CURDATE(), '09:00:00'),
(3, 'Candidato', 'Revisión de Resultados', CURDATE(), '10:30:00');

-- Productos (Nombres Neutros con Ofertas Reales)
INSERT INTO `productos` (`nombre`, `precio`, `imagen`, `categoria`, `tag`, `tag_class`, `oferta_popular`) VALUES
('Producto A', 50.00, 'Imgs/Producto 1.png', 'medicamentos', 'Medicamento', 'tag-medicamento', '¡2x1 Oferta!'),
('Producto B', 30.00, 'Imgs/Producto 2.png', 'vitaminas', 'Vitamina', 'tag-vitamina', '⭐ Popular'),
('Producto C', 45.00, 'Imgs/Producto 3.png', 'cuidado-personal', 'Cuidado Personal', 'tag-cuidado', '¡15% OFF!'),
('Producto D', 10.00, 'Imgs/Producto 4.png', 'bebes', 'Bebés', 'tag-bebe', NULL);
