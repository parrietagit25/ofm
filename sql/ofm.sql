-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-08-2025 a las 03:50:55
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
-- Base de datos: `ofm`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `imagen`, `activo`, `orden`, `creado_en`, `actualizado_en`) VALUES
(1, 'Electrónicos', 'Productos electrónicos y tecnología', NULL, 1, 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(2, 'Ropa', 'Vestimenta y accesorios', NULL, 1, 2, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(3, 'Hogar', 'Artículos para el hogar', NULL, 1, 3, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(4, 'Deportes', 'Artículos deportivos', NULL, 1, 4, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(5, 'Libros', 'Libros y material educativo', NULL, 1, 5, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(6, 'Juguetes', 'Juguetes y entretenimiento', NULL, 1, 6, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(7, 'Salud', 'Productos de salud y belleza', NULL, 1, 7, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(8, 'Automotriz', 'Productos automotrices', NULL, 1, 8, '2025-08-18 03:55:38', '2025-08-18 03:55:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comercios`
--

CREATE TABLE `comercios` (
  `id` int(11) NOT NULL,
  `usuario_socio_id` int(11) NOT NULL,
  `nombre_comercio` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `direccion` text NOT NULL,
  `telefono_comercio` varchar(20) DEFAULT NULL,
  `email_comercio` varchar(255) DEFAULT NULL,
  `sitio_web` varchar(255) DEFAULT NULL,
  `horario_apertura` time DEFAULT NULL,
  `horario_cierre` time DEFAULT NULL,
  `dias_operacion` varchar(100) DEFAULT NULL,
  `categoria_comercio` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de comercios/tiendas de los socios';

--
-- Volcado de datos para la tabla `comercios`
--

INSERT INTO `comercios` (`id`, `usuario_socio_id`, `nombre_comercio`, `descripcion`, `direccion`, `telefono_comercio`, `email_comercio`, `sitio_web`, `horario_apertura`, `horario_cierre`, `dias_operacion`, `categoria_comercio`, `activo`, `destacado`, `creado_en`, `actualizado_en`) VALUES
(1, 2, 'Pc gaming', 'venta de pc gamin', '7470 W Irlo Bronson Memorial Hwy, Kissim', '14073964400', 'schiavonea@ostarhotels.com', NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-22 02:08:32', '2025-08-22 02:08:32'),
(2, 4, 'ganga3000', 'ganga3000', 'ganga3000', '65656565', 'ganga3000@ganga3000.com', NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-24 03:43:07', '2025-08-24 03:43:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comisiones_socios`
--

CREATE TABLE `comisiones_socios` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `orden_detalle_id` int(11) NOT NULL,
  `monto_comision` decimal(10,2) NOT NULL,
  `porcentaje_comision` decimal(5,2) NOT NULL,
  `estado` enum('pendiente','calculada','pagada') DEFAULT 'pendiente',
  `fecha_calculo` datetime DEFAULT current_timestamp(),
  `fecha_pago` datetime DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='C├ílculo y seguimiento de comisiones para socios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('string','number','boolean','json') NOT NULL DEFAULT 'string',
  `categoria` varchar(50) NOT NULL DEFAULT 'general',
  `editable` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `categoria`, `editable`, `creado_en`, `actualizado_en`) VALUES
(1, 'nombre_sitio', 'OFM - Ofertas y Más', 'Nombre del sitio web', 'string', 'general', 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(2, 'descripcion_sitio', 'Tu plataforma de ofertas y descuentos', 'Descripción del sitio web', 'string', 'general', 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(3, 'email_contacto', 'contacto@ofm.com', 'Email de contacto del sitio', 'string', 'general', 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(4, 'telefono_contacto', '+1234567890', 'Teléfono de contacto', 'string', 'general', 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(5, 'moneda', 'USD', 'Moneda principal del sitio', 'string', 'general', 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(6, 'impuestos', '0.16', 'Porcentaje de impuestos', 'number', 'ventas', 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(7, 'comision_comercio', '0.05', 'Comisión por venta a comercios', 'number', 'ventas', 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(8, 'max_productos_carrito', '50', 'Máximo de productos en carrito', 'number', 'ventas', 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38'),
(9, 'dias_entrega_estimada', '3-5', 'Días estimados de entrega', 'string', 'ventas', 1, '2025-08-18 03:55:38', '2025-08-18 03:55:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_comisiones`
--

CREATE TABLE `configuracion_comisiones` (
  `id` int(11) NOT NULL,
  `tipo_producto` varchar(50) DEFAULT 'general',
  `porcentaje_comision` decimal(5,2) NOT NULL DEFAULT 10.00,
  `monto_minimo` decimal(10,2) DEFAULT 0.00,
  `monto_maximo` decimal(10,2) DEFAULT 999999.99,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuraci├│n de porcentajes de comisi├│n por tipo de producto';

--
-- Volcado de datos para la tabla `configuracion_comisiones`
--

INSERT INTO `configuracion_comisiones` (`id`, `tipo_producto`, `porcentaje_comision`, `monto_minimo`, `monto_maximo`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'general', 2.00, 0.00, 999999.99, 1, '2025-08-24 18:44:30', '2025-08-24 18:44:30'),
(2, 'general', 10.00, 0.00, 999999.99, 1, '2025-08-24 19:04:32', '2025-08-24 19:04:32'),
(3, 'electronica', 8.00, 0.00, 999999.99, 1, '2025-08-24 19:04:32', '2025-08-24 19:04:32'),
(4, 'ropa', 12.00, 0.00, 999999.99, 1, '2025-08-24 19:04:32', '2025-08-24 19:04:32'),
(5, 'hogar', 15.00, 0.00, 999999.99, 1, '2025-08-24 19:04:32', '2025-08-24 19:04:32'),
(6, 'general', 10.00, 0.00, 999999.99, 1, '2025-08-24 19:04:47', '2025-08-24 19:04:47'),
(7, 'electronica', 8.00, 0.00, 999999.99, 1, '2025-08-24 19:04:47', '2025-08-24 19:04:47'),
(8, 'ropa', 12.00, 0.00, 999999.99, 1, '2025-08-24 19:04:47', '2025-08-24 19:04:47'),
(9, 'hogar', 15.00, 0.00, 999999.99, 1, '2025-08-24 19:04:47', '2025-08-24 19:04:47'),
(10, 'general', 10.00, 0.00, 999999.99, 1, '2025-08-24 19:05:07', '2025-08-24 19:05:07'),
(11, 'electronica', 8.00, 0.00, 999999.99, 1, '2025-08-24 19:05:07', '2025-08-24 19:05:07'),
(12, 'ropa', 12.00, 0.00, 999999.99, 1, '2025-08-24 19:05:07', '2025-08-24 19:05:07'),
(13, 'hogar', 15.00, 0.00, 999999.99, 1, '2025-08-24 19:05:07', '2025-08-24 19:05:07'),
(14, 'general', 10.00, 0.00, 999999.99, 1, '2025-08-24 19:09:37', '2025-08-24 19:09:37'),
(15, 'electronica', 8.00, 0.00, 999999.99, 1, '2025-08-24 19:09:37', '2025-08-24 19:09:37'),
(16, 'ropa', 12.00, 0.00, 999999.99, 1, '2025-08-24 19:09:37', '2025-08-24 19:09:37'),
(17, 'hogar', 15.00, 0.00, 999999.99, 1, '2025-08-24 19:09:37', '2025-08-24 19:09:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cupones`
--

CREATE TABLE `cupones` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('porcentaje','monto_fijo') NOT NULL DEFAULT 'porcentaje',
  `valor` decimal(10,2) NOT NULL,
  `minimo_compra` decimal(10,2) DEFAULT NULL,
  `maximo_descuento` decimal(10,2) DEFAULT NULL,
  `usos_maximos` int(11) DEFAULT NULL,
  `usos_actuales` int(11) NOT NULL DEFAULT 0,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `aplicable_a` enum('todos','categoria','producto','comercio') NOT NULL DEFAULT 'todos',
  `aplicable_id` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas_ventas_socios`
--

CREATE TABLE `estadisticas_ventas_socios` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total_ventas` decimal(10,2) DEFAULT 0.00,
  `cantidad_productos` int(11) DEFAULT 0,
  `total_comisiones` decimal(10,2) DEFAULT 0.00,
  `qr_verificados` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estad├¡sticas diarias de ventas por socio';

--
-- Volcado de datos para la tabla `estadisticas_ventas_socios`
--

INSERT INTO `estadisticas_ventas_socios` (`id`, `socio_id`, `fecha`, `total_ventas`, `cantidad_productos`, `total_comisiones`, `qr_verificados`, `created_at`, `updated_at`) VALUES
(3, 1, '2025-08-24', 200.00, 2, 0.00, 0, '2025-08-25 00:01:00', '2025-08-25 00:01:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla` varchar(100) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_anteriores`)),
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_nuevos`)),
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `tipo` enum('info','success','warning','error') NOT NULL DEFAULT 'info',
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `enlace` varchar(500) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_socios`
--

CREATE TABLE `notificaciones_socios` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `tipo` enum('nueva_venta','qr_verificado','comision_pagada','stock_bajo') NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_lectura` datetime DEFAULT NULL,
  `datos_adicionales` longtext DEFAULT NULL CHECK (json_valid(`datos_adicionales`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sistema de notificaciones para socios';

--
-- Volcado de datos para la tabla `notificaciones_socios`
--

INSERT INTO `notificaciones_socios` (`id`, `socio_id`, `tipo`, `titulo`, `mensaje`, `leida`, `fecha_creacion`, `fecha_lectura`, `datos_adicionales`) VALUES
(8, 1, 'nueva_venta', 'Nueva venta realizada', 'Se ha vendido prueba en la orden OFM2025082417560800603576', 0, '2025-08-24 19:01:00', NULL, NULL),
(9, 1, 'nueva_venta', 'Nueva venta realizada', 'Se ha vendido prueba en la orden OFM2025082417560800603576', 0, '2025-08-24 19:01:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `numero_orden` varchar(50) NOT NULL,
  `fecha_orden` datetime DEFAULT current_timestamp(),
  `estado` enum('pendiente','confirmada','en_proceso','enviada','entregada','cancelada') DEFAULT 'pendiente',
  `total` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `impuestos` decimal(10,2) DEFAULT 0.00,
  `envio` decimal(10,2) DEFAULT 0.00,
  `metodo_pago` varchar(50) DEFAULT 'efectivo',
  `notas` text DEFAULT NULL,
  `direccion_envio` text DEFAULT NULL,
  `telefono_envio` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla principal de ├│rdenes/pedidos de clientes';

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id`, `usuario_id`, `numero_orden`, `fecha_orden`, `estado`, `total`, `subtotal`, `impuestos`, `envio`, `metodo_pago`, `notas`, `direccion_envio`, `telefono_envio`, `created_at`, `updated_at`) VALUES
(11, 3, 'OFM2025082417560800603576', '2025-08-24 19:01:00', 'pendiente', 200.00, 200.00, 0.00, 0.00, 'efectivo', 'asdasdasd', NULL, NULL, '2025-08-25 00:01:00', '2025-08-25 00:01:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_detalles`
--

CREATE TABLE `orden_detalles` (
  `id` int(11) NOT NULL,
  `orden_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `comercio_id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `precio_total` decimal(10,2) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `codigo_qr` varchar(255) NOT NULL,
  `estado_qr` enum('pendiente','generado','verificado','utilizado') DEFAULT 'pendiente',
  `fecha_verificacion` datetime DEFAULT NULL,
  `verificado_por` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unidad_numero` int(11) NOT NULL DEFAULT 1 COMMENT 'N├║mero de unidad del producto (1, 2, 3, etc.)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalles de productos en cada orden con QR ├║nico';

--
-- Volcado de datos para la tabla `orden_detalles`
--

INSERT INTO `orden_detalles` (`id`, `orden_id`, `producto_id`, `comercio_id`, `socio_id`, `cantidad`, `precio_unitario`, `precio_total`, `nombre_producto`, `codigo_qr`, `estado_qr`, `fecha_verificacion`, `verificado_por`, `created_at`, `unidad_numero`) VALUES
(11, 11, 46, 1, 1, 1, 100.00, 100.00, 'prueba', 'QR_ada4ca63c819cddc_1756080060_U1', 'pendiente', NULL, NULL, '2025-08-25 00:01:00', 1),
(12, 11, 46, 1, 1, 1, 100.00, 100.00, 'prueba', 'QR_9e81dc34decc66c2_1756080060_U2', 'pendiente', NULL, NULL, '2025-08-25 00:01:00', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `comercio_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_anterior` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `categoria` varchar(100) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `codigo_producto` varchar(100) DEFAULT NULL,
  `peso` decimal(8,2) DEFAULT NULL,
  `dimensiones` varchar(100) DEFAULT NULL,
  `status` enum('activo','inactivo','agotado','en_oferta') NOT NULL DEFAULT 'activo',
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `precio_oferta` decimal(11,0) NOT NULL,
  `socio_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `comercio_id`, `nombre`, `descripcion`, `precio`, `precio_anterior`, `stock`, `categoria`, `marca`, `codigo_producto`, `peso`, `dimensiones`, `status`, `destacado`, `creado_en`, `actualizado_en`, `precio_oferta`, `socio_id`) VALUES
(43, 1, 'Test Corrección', 'Producto para probar la corrección del array de imágenes', 149.99, NULL, 12, 'Test', 'Corrección', NULL, NULL, NULL, 'activo', 0, '2025-08-24 05:58:19', '2025-08-24 21:39:21', 0, NULL),
(44, NULL, 'qweqwe', 'qweqwe qweqw qweqwe', 12.00, NULL, 4, NULL, NULL, NULL, NULL, NULL, 'activo', 0, '2025-08-24 06:17:08', '2025-08-24 06:17:08', 0, NULL),
(45, 2, 'multimax', 'multimax multimax multimax', 200.00, 100.00, 196, 'Belleza', NULL, NULL, NULL, NULL, 'activo', 0, '2025-08-24 06:25:49', '2025-08-24 20:25:15', 0, NULL),
(46, NULL, 'prueba', 'prueba de producto ', 100.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'activo', 1, '2025-08-24 17:09:39', '2025-08-25 00:01:00', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_imagenes`
--

CREATE TABLE `producto_imagenes` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta` varchar(500) NOT NULL,
  `tipo` varchar(50) NOT NULL DEFAULT 'imagen',
  `orden` int(11) NOT NULL DEFAULT 0,
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto_imagenes`
--

INSERT INTO `producto_imagenes` (`id`, `producto_id`, `nombre_archivo`, `ruta`, `tipo`, `orden`, `principal`, `creado_en`) VALUES
(21, 43, '1_1756015099_68aaa9fbba922.jpg', '1_1756015099_68aaa9fbba922.jpg', 'imagen', 1, 1, '2025-08-24 05:58:19'),
(22, 43, '2_1756015099_68aaa9fbbae6c.jpg', '2_1756015099_68aaa9fbbae6c.jpg', 'imagen', 2, 0, '2025-08-24 05:58:19'),
(23, 43, '3_1756015099_68aaa9fbbb512.jpg', '3_1756015099_68aaa9fbbb512.jpg', 'imagen', 3, 0, '2025-08-24 05:58:19'),
(24, 43, '4_1756015099_68aaa9fbbb971.jpg', '4_1756015099_68aaa9fbbb971.jpg', 'imagen', 4, 0, '2025-08-24 05:58:19'),
(26, 44, '68aaae64b89fb_1756016228.jpg', '68aaae64b89fb_1756016228.jpg', 'image/jpeg', 2, 0, '2025-08-24 06:17:08'),
(27, 44, '68aaae64b8f12_1756016228.jpg', '68aaae64b8f12_1756016228.jpg', 'image/jpeg', 3, 0, '2025-08-24 06:17:08'),
(28, 44, '68aaae64b9414_1756016228.jpg', '68aaae64b9414_1756016228.jpg', 'image/jpeg', 4, 0, '2025-08-24 06:17:08'),
(29, 45, '68aab06d270b8_1756016749.png', '68aab06d270b8_1756016749.png', 'image/png', 1, 1, '2025-08-24 06:25:49'),
(30, 45, '68aab06d2765e_1756016749.png', '68aab06d2765e_1756016749.png', 'image/png', 2, 0, '2025-08-24 06:25:49'),
(31, 45, '68aab06d27d30_1756016749.png', '68aab06d27d30_1756016749.png', 'image/png', 3, 0, '2025-08-24 06:25:49'),
(32, 45, '68aab06d282de_1756016749.png', '68aab06d282de_1756016749.png', 'image/png', 4, 0, '2025-08-24 06:25:49'),
(33, 46, '68ab4753e7e7f_1756055379.png', '68ab4753e7e7f_1756055379.png', 'image/png', 1, 1, '2025-08-24 17:09:39'),
(34, 46, '68ab4753e85d8_1756055379.png', '68ab4753e85d8_1756055379.png', 'image/png', 2, 0, '2025-08-24 17:09:39'),
(35, 46, '68ab4753e8dc3_1756055379.png', '68ab4753e8dc3_1756055379.png', 'image/png', 3, 0, '2025-08-24 17:09:39'),
(36, 46, '68ab4753e93bc_1756055379.png', '68ab4753e93bc_1756055379.png', 'image/png', 4, 0, '2025-08-24 17:09:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `qr_verificaciones`
--

CREATE TABLE `qr_verificaciones` (
  `id` int(11) NOT NULL,
  `orden_detalle_id` int(11) NOT NULL,
  `codigo_qr` varchar(255) NOT NULL,
  `verificado_por` int(11) NOT NULL,
  `fecha_verificacion` datetime DEFAULT current_timestamp(),
  `ubicacion_verificacion` varchar(255) DEFAULT NULL,
  `dispositivo_verificacion` varchar(255) DEFAULT NULL,
  `ip_verificacion` varchar(45) DEFAULT NULL,
  `resultado` enum('exitoso','fallido','ya_utilizado','expirado') NOT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de verificaciones de c├│digos QR';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reseñas`
--

CREATE TABLE `reseñas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `comercio_id` int(11) DEFAULT NULL,
  `puntuacion` tinyint(1) NOT NULL CHECK (`puntuacion` >= 1 and `puntuacion` <= 5),
  `titulo` varchar(255) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `aprobado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socios`
--

CREATE TABLE `socios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `pais` varchar(100) DEFAULT 'Panam├í',
  `documento_identidad` varchar(50) DEFAULT NULL,
  `tipo_documento` enum('cedula','pasaporte','ruc','otro') DEFAULT 'cedula',
  `estado` enum('activo','inactivo','pendiente','suspendido') DEFAULT 'activo',
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de socios/afiliados del sistema';

--
-- Volcado de datos para la tabla `socios`
--

INSERT INTO `socios` (`id`, `nombre`, `email`, `telefono`, `direccion`, `ciudad`, `codigo_postal`, `pais`, `documento_identidad`, `tipo_documento`, `estado`, `fecha_registro`, `fecha_actualizacion`) VALUES
(1, 'Socio Demo 1', 'socio1@demo.com', '123456789', NULL, 'Panam├í', NULL, 'Panam├í', NULL, 'cedula', 'activo', '2025-08-24 14:14:46', '2025-08-24 14:14:46'),
(2, 'Socio Demo 2', 'socio2@demo.com', '987654321', NULL, 'Panam├í', NULL, 'Panam├í', NULL, 'cedula', 'activo', '2025-08-24 14:14:46', '2025-08-24 14:14:46'),
(3, 'Socio Demo 3', 'socio3@demo.com', '555666777', NULL, 'Panam├í', NULL, 'Panam├í', NULL, 'cedula', 'activo', '2025-08-24 14:14:46', '2025-08-24 14:14:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones`
--

CREATE TABLE `transacciones` (
  `id` int(11) NOT NULL,
  `orden_id` int(11) NOT NULL,
  `numero_transaccion` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `estado` enum('pendiente','procesando','completada','fallida','reembolsada') DEFAULT 'pendiente',
  `referencia_pago` varchar(255) DEFAULT NULL,
  `fecha_transaccion` datetime DEFAULT current_timestamp(),
  `fecha_procesamiento` datetime DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de transacciones de pago';

--
-- Volcado de datos para la tabla `transacciones`
--

INSERT INTO `transacciones` (`id`, `orden_id`, `numero_transaccion`, `monto`, `metodo_pago`, `estado`, `referencia_pago`, `fecha_transaccion`, `fecha_procesamiento`, `detalles`, `created_at`) VALUES
(9, 11, 'TXN20250824180100647478', 200.00, 'efectivo', 'completada', NULL, '2025-08-24 19:01:00', NULL, NULL, '2025-08-25 00:01:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('admin','socio','cliente') NOT NULL DEFAULT 'cliente',
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `verificado` tinyint(1) NOT NULL DEFAULT 0,
  `token_verificacion` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('masculino','femenino','otro') DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `clave`, `rol`, `telefono`, `direccion`, `activo`, `verificado`, `token_verificacion`, `fecha_nacimiento`, `genero`, `creado_en`, `actualizado_en`, `ultimo_acceso`) VALUES
(1, 'Administrador', 'Sistema', 'admin@ofm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, 1, 1, NULL, NULL, NULL, '2025-08-18 03:55:38', '2025-08-18 03:55:38', NULL),
(2, 'socio', 'socio', 'socio@socio.com', '$2y$10$glFPxcu7WlGmC8vLGpsXaetOq8RrwiaDLHbgjrRR82MGR7KnYU0Zu', 'socio', '61045697', NULL, 1, 0, NULL, NULL, NULL, '2025-08-22 01:03:10', '2025-08-22 01:03:10', NULL),
(3, 'cliente', 'cliente', 'cliente@cliente.com', '$2y$10$GcGK0pa5cgWVEuG6kcGqlOebkTZo6NR3jtP.gZQjzyRkrLZ5.f7gy', 'cliente', '61045697', NULL, 1, 0, NULL, NULL, NULL, '2025-08-24 03:08:37', '2025-08-24 03:08:37', NULL),
(4, 'gruporama', 'gruporama', 'gruporama@gruporama.com', '$2y$10$boRfJ8vN0zFuqIDZx4jFq.TPDR6pc4HL0eolrkXRx.6TU.MQcAhPe', 'socio', '62626262', NULL, 1, 0, NULL, NULL, NULL, '2025-08-24 03:40:30', '2025-08-24 03:40:30', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `comercio_id` int(11) DEFAULT NULL,
  `numero_venta` varchar(50) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `impuestos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` enum('efectivo','tarjeta','transferencia','paypal') NOT NULL DEFAULT 'efectivo',
  `status` enum('pendiente','confirmada','enviada','entregada','cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_venta` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_entrega` timestamp NULL DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `precio_total` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nombre` (`nombre`),
  ADD KEY `activo` (`activo`),
  ADD KEY `orden` (`orden`);

--
-- Indices de la tabla `comercios`
--
ALTER TABLE `comercios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_socio_id` (`usuario_socio_id`),
  ADD KEY `activo` (`activo`),
  ADD KEY `destacado` (`destacado`),
  ADD KEY `categoria_comercio` (`categoria_comercio`);

--
-- Indices de la tabla `comisiones_socios`
--
ALTER TABLE `comisiones_socios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clave` (`clave`),
  ADD KEY `categoria` (`categoria`);

--
-- Indices de la tabla `configuracion_comisiones`
--
ALTER TABLE `configuracion_comisiones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cupones`
--
ALTER TABLE `cupones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `codigo` (`codigo`),
  ADD KEY `activo` (`activo`),
  ADD KEY `fecha_inicio` (`fecha_inicio`),
  ADD KEY `fecha_fin` (`fecha_fin`);

--
-- Indices de la tabla `estadisticas_ventas_socios`
--
ALTER TABLE `estadisticas_ventas_socios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `accion` (`accion`),
  ADD KEY `tabla` (`tabla`),
  ADD KEY `creado_en` (`creado_en`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `leida` (`leida`),
  ADD KEY `tipo` (`tipo`),
  ADD KEY `creado_en` (`creado_en`);

--
-- Indices de la tabla `notificaciones_socios`
--
ALTER TABLE `notificaciones_socios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_orden` (`numero_orden`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha` (`fecha_orden`),
  ADD KEY `idx_ordenes_usuario_fecha` (`usuario_id`,`fecha_orden`);

--
-- Indices de la tabla `orden_detalles`
--
ALTER TABLE `orden_detalles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_qr` (`codigo_qr`),
  ADD KEY `idx_orden_detalles_qr_estado` (`codigo_qr`,`estado_qr`),
  ADD KEY `idx_orden_detalles_unidad` (`orden_id`,`producto_id`,`unidad_numero`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comercio_id` (`comercio_id`),
  ADD KEY `status` (`status`),
  ADD KEY `categoria` (`categoria`),
  ADD KEY `destacado` (`destacado`),
  ADD KEY `precio` (`precio`),
  ADD KEY `idx_productos_comercio` (`comercio_id`),
  ADD KEY `idx_productos_socio` (`socio_id`);

--
-- Indices de la tabla `producto_imagenes`
--
ALTER TABLE `producto_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `principal` (`principal`),
  ADD KEY `orden` (`orden`);

--
-- Indices de la tabla `qr_verificaciones`
--
ALTER TABLE `qr_verificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_qr_verificaciones_qr_fecha` (`codigo_qr`,`fecha_verificacion`);

--
-- Indices de la tabla `reseñas`
--
ALTER TABLE `reseñas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `comercio_id` (`comercio_id`),
  ADD KEY `puntuacion` (`puntuacion`),
  ADD KEY `aprobado` (`aprobado`);

--
-- Indices de la tabla `socios`
--
ALTER TABLE `socios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_registro` (`fecha_registro`);

--
-- Indices de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_transaccion` (`numero_transaccion`),
  ADD KEY `idx_transacciones_estado_fecha` (`estado`,`fecha_transaccion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `rol` (`rol`),
  ADD KEY `activo` (`activo`),
  ADD KEY `verificado` (`verificado`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `comercio_id` (`comercio_id`),
  ADD KEY `numero_venta` (`numero_venta`),
  ADD KEY `status` (`status`),
  ADD KEY `fecha_venta` (`fecha_venta`);

--
-- Indices de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `comercios`
--
ALTER TABLE `comercios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `comisiones_socios`
--
ALTER TABLE `comisiones_socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `configuracion_comisiones`
--
ALTER TABLE `configuracion_comisiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `cupones`
--
ALTER TABLE `cupones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estadisticas_ventas_socios`
--
ALTER TABLE `estadisticas_ventas_socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones_socios`
--
ALTER TABLE `notificaciones_socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `orden_detalles`
--
ALTER TABLE `orden_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `producto_imagenes`
--
ALTER TABLE `producto_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `qr_verificaciones`
--
ALTER TABLE `qr_verificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reseñas`
--
ALTER TABLE `reseñas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `socios`
--
ALTER TABLE `socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comercios`
--
ALTER TABLE `comercios`
  ADD CONSTRAINT `fk_comercio_usuario_socio` FOREIGN KEY (`usuario_socio_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `fk_notificacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_comercio` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_productos_comercio` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`),
  ADD CONSTRAINT `fk_productos_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`);

--
-- Filtros para la tabla `producto_imagenes`
--
ALTER TABLE `producto_imagenes`
  ADD CONSTRAINT `fk_imagen_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reseñas`
--
ALTER TABLE `reseñas`
  ADD CONSTRAINT `fk_reseña_comercio` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reseña_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reseña_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venta_comercio` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `fk_venta_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venta_detalle_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
