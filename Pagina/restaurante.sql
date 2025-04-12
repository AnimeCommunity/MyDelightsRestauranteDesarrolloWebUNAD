-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-04-2025 a las 23:44:49
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `restaurante`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `tipo` enum('a_la_carta','comida_corriente') NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `precio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menus`
--

INSERT INTO `menus` (`id`, `nombre`, `descripcion`, `imagen`, `tipo`, `activo`, `precio`) VALUES
(13, 'De dias', 'Platillo del dia,carne,patatas,arroz y puchero', '2.png', 'comida_corriente', 1, 100000.00),
(14, 'light', 'equilibrado para todos tus dias', '3.png', 'a_la_carta', 1, 130000.00),
(15, 'recomendacion del chef', 'sorpresa del chef', '4.png', 'a_la_carta', 1, 160000.00),
(16, 'hoy es viernes', 'comida especial para despedir la semana', '5.png', 'a_la_carta', 1, 130000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_productos`
--

CREATE TABLE `menu_productos` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `categoria` enum('sopa','principio','carne','entrada','bebida','postre') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menu_productos`
--

INSERT INTO `menu_productos` (`id`, `menu_id`, `producto_id`, `categoria`) VALUES
(18, 13, 8, 'principio'),
(19, 13, 14, 'carne'),
(20, 13, 16, 'entrada'),
(21, 13, 21, 'sopa'),
(22, 14, 9, 'entrada'),
(23, 14, 10, 'principio'),
(24, 14, 12, 'bebida'),
(25, 14, 20, 'sopa'),
(26, 13, 12, 'bebida'),
(27, 15, 10, 'postre'),
(28, 15, 13, 'bebida'),
(29, 15, 14, 'principio'),
(30, 15, 15, 'entrada'),
(31, 15, 16, 'entrada'),
(32, 16, 9, 'entrada'),
(33, 16, 15, 'carne'),
(34, 16, 18, 'principio'),
(35, 16, 23, 'postre'),
(36, 16, 13, 'bebida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `tipo_entrega` enum('presencial','domicilio') DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `detalle`, `total`, `tipo_entrega`, `fecha`) VALUES
(9, 6, 'menu: Fantasia x1', 200.00, 'presencial', '2025-04-12 15:18:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `tipo_item` enum('producto','menu') DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `sexo` enum('masculino','femenino','otro') DEFAULT 'otro',
  `fecha_nacimiento` date DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perfiles`
--

INSERT INTO `perfiles` (`id`, `usuario_id`, `sexo`, `fecha_nacimiento`, `direccion`) VALUES
(1, 6, 'masculino', '1999-01-01', 'ccacac');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `ingredientes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `descripcion`, `tipo`, `imagen`, `ingredientes`) VALUES
(8, 'Verduras salteadas', 20000.00, 'verduras salteadas al horno', 'principio', 'img/9.png', 'Zanahoria, cebollin, tomate,ajo'),
(9, 'Linguini', 15000.00, 'Verduras y puchero', 'principio', 'img/10.png', 'tomate, calabazin, pepino'),
(10, 'Melolonpack', 32000.00, 'Lluvia de frutas', 'entrada', 'img/11.png', 'mango, freaza, kiwi, lulo'),
(11, 'salpicon', 1000.00, 'fruta cortada en trozos', 'entrada', 'img/12.png', 'papaya, lulo, maracuya, mora'),
(12, 'Jugo de frutas ', 10000.00, 'Jugo de frutas con hielo', 'bebida', 'img/13.png', 'agua,frutas'),
(13, 'jugo de frutas en leche', 20000.00, 'juego de frutas ', 'bebida', 'img/14.png', 'fruta y leche'),
(14, 'carnes frias', 36000.00, 'jamon cerrano', 'carne', 'img/15.png', 'carne,jamon, salchicha'),
(15, 'cardero', 50000.00, 'crote de carne selecto de cordero', 'carne', 'img/16.png', 'carne de cordero, ajo, pimenton'),
(16, 'arroz blanco', 2000.00, 'arroz blanco', 'principio', 'img/17.png', 'arroz,sal, agua'),
(17, 'arroz parbolziado', 20000.00, 'arroz parbolziado', 'entrada', 'img/18.png', 'arroz,ajo,agua'),
(18, 'Patatas fritas', 15000.00, 'papas fritas', 'principio', 'img/19.png', 'papa, sal y agua'),
(19, 'patatas', 10000.00, 'patatas cocidas', 'entrada', 'img/20.png', 'papas, sala,agua'),
(20, 'sopa de verduras', 60000.00, 'puhcero de verduras', 'sopa', 'img/21.png', 'zanahoria, agua de llave,sal,ajo, cebolla'),
(21, 'puchero de pasta', 50000.00, 'sopa de pasta', 'sopa', 'img/22.png', 'pasta,agua,perejil, ajo,sal'),
(22, 'hotcakes con fresa', 90000.00, 'hotcakes de fresa y crema chantilly', 'postre', 'img/23.png', 'hotcakes, fresas,chantilly'),
(23, 'helado cookies and cream', 120000.00, 'helado con galleta de chocolate', 'postre', 'img/24.png', 'helado, leche,chocolate,galleta, salsa de chocolate');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `personas` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `precio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `rol` enum('admin','cliente') DEFAULT NULL,
  `telefono` int(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tipo_cliente` enum('nuevo','recurrente','permanente') DEFAULT 'nuevo',
  `total_compras` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `rol`, `telefono`, `email`, `tipo_cliente`, `total_compras`) VALUES
(5, 'Administrador', 'admin123', 'admin', 1234567890, 'admin@restaurante.com', 'nuevo', 0),
(6, 'prueba', '1', 'cliente', 1234567890, 'san1599@gmail.com', 'recurrente', 8);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_productos`
--
ALTER TABLE `menu_productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_id` (`menu_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `menu_productos`
--
ALTER TABLE `menu_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `menu_productos`
--
ALTER TABLE `menu_productos`
  ADD CONSTRAINT `menu_productos_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_productos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `pedido_detalles_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`);

--
-- Filtros para la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD CONSTRAINT `perfiles_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
