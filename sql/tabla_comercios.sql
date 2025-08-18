-- Tabla para almacenar información de comercios
CREATE TABLE IF NOT EXISTS `comercios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_socio_id` int(11) NOT NULL,
  `nombre_comercio` varchar(255) NOT NULL,
  `descripcion` text,
  `direccion` text NOT NULL,
  `telefono_comercio` varchar(20),
  `email_comercio` varchar(255),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_socio_id` (`usuario_socio_id`),
  CONSTRAINT `fk_comercio_usuario_socio` FOREIGN KEY (`usuario_socio_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunos comercios de ejemplo (opcional)
-- INSERT INTO `comercios` (`usuario_socio_id`, `nombre_comercio`, `descripcion`, `direccion`, `telefono_comercio`, `email_comercio`, `activo`) VALUES
-- (2, 'Tienda de Ropa Fashion', 'Tienda especializada en ropa casual y formal para todas las edades', 'Calle Principal 123, Centro Comercial Plaza Mayor', '555-0101', 'fashion@tienda.com', 1),
-- (3, 'Electrónicos TechPro', 'Venta de dispositivos electrónicos, smartphones y accesorios', 'Avenida Tecnológica 456, Local 15', '555-0202', 'ventas@techpro.com', 1);
