-- Ejecutar este archivo SQL para crear las tablas de productos
-- Copia y pega este contenido en tu gestor de base de datos (phpMyAdmin, MySQL Workbench, etc.)

-- Tabla para almacenar información de productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comercio_id` int(11) NULL DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_anterior` decimal(10,2) NULL DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `categoria` varchar(100) NULL DEFAULT NULL,
  `marca` varchar(100) NULL DEFAULT NULL,
  `codigo_producto` varchar(100) NULL DEFAULT NULL,
  `peso` decimal(8,2) NULL DEFAULT NULL,
  `dimensiones` varchar(100) NULL DEFAULT NULL,
  `status` enum('activo', 'inactivo', 'agotado', 'en_oferta') NOT NULL DEFAULT 'activo',
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `comercio_id` (`comercio_id`),
  KEY `status` (`status`),
  KEY `categoria` (`categoria`),
  KEY `destacado` (`destacado`),
  CONSTRAINT `fk_producto_comercio` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para almacenar las imágenes de los productos
CREATE TABLE IF NOT EXISTS `producto_imagenes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta` varchar(500) NOT NULL,
  `tipo` varchar(50) NOT NULL DEFAULT 'imagen',
  `orden` int(11) NOT NULL DEFAULT 0,
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `principal` (`principal`),
  KEY `orden` (`orden`),
  CONSTRAINT `fk_imagen_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunos productos de ejemplo (opcional)
INSERT INTO `productos` (`comercio_id`, `nombre`, `descripcion`, `precio`, `categoria`, `marca`, `status`, `destacado`) VALUES
(NULL, 'Producto OFM Premium', 'Producto propio de OFM sin comercio asociado', 99.99, 'General', 'OFM', 'activo', 1);

-- Verificar que las tablas se crearon correctamente
SHOW TABLES LIKE 'productos';
SHOW TABLES LIKE 'producto_imagenes';
