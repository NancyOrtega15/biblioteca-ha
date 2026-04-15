-- 1. Crear la base de datos y seleccionarla
CREATE DATABASE IF NOT EXISTS `biblioteca`;
USE `biblioteca`;

-- 2. Ajustes iniciales de compatibilidad
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `libros`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `libros` (
  `ID` int(100) NOT NULL AUTO_INCREMENT,
  `Titulo` varchar(100) NOT NULL,
  `Autor` varchar(10) NOT NULL,
  `ISBN` varchar(30) NOT NULL,
  `Paginas` int(50) NOT NULL,
  `Editorial` varchar(50) NOT NULL,
  `Categoria` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `libros`
INSERT INTO `libros` (`ID`, `Titulo`, `Autor`, `ISBN`, `Paginas`, `Editorial`, `Categoria`) VALUES
(1, 'don quijote de la mancha', 'prueba', 'prueba', 100, 'prueba', 'Ficción'),
(2, 'Romeo y julieta ', 'hshshs', '276378', 40, 'jxjs', 'Tecnología');

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `usuarios`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID` int(100) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Telefono` varchar(10) NOT NULL,
  `Correo` varchar(30) NOT NULL,
  `Rol` varchar(50) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `contraseña` varchar(32) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `usuarios`
INSERT INTO `usuarios` (`ID`, `Nombre`, `Telefono`, `Correo`, `Rol`, `usuario`, `contraseña`) VALUES
(1, 'Sandra', '7758695981', 'Sandra@gmail.com', 'Administrador', 'Sandy21', '379fffda3358541bb6049ca30bcaf239'),
(2, 'Antonio', '7754121421', 'Antonio@gmail.com', 'Estudiante', '', ''),
(3, 'Carlos', '7759898526', 'Carlos@gmail.com', 'Profesor', 'Carlos95', 'fc534c43dc4d23714c1b7a72a5f484c4'),
(4, 'Nancy ', '7751280240', '2330576@upt.edu.mx', 'Estudiante', '', '');

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `prestamos`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestamos` (
  `ID` int(100) NOT NULL AUTO_INCREMENT,
  `Fecha de préstamo` date NOT NULL,
  `Fecha de devolución` date NOT NULL,
  `ID libro` int(30) NOT NULL,
  `ID usuario` int(50) NOT NULL,
  `Estado` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `prestamos`
INSERT INTO `prestamos` (`ID`, `Fecha de préstamo`, `Fecha de devolución`, `ID libro`, `ID usuario`, `Estado`) VALUES
(1, '2026-04-15', '2026-04-21', 1, 1, 'Activo'),
(2, '2026-04-14', '2026-04-15', 2, 4, 'Activo');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



