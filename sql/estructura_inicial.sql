-- Script para crear la base de datos OFM (Ofertas y Más)

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS dbuzwvgswctwj0 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE dbuzwvgswctwj0;

-- Tabla de usuarios con 3 tipos: admin, socio, cliente
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    apellido VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    clave VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'socio', 'cliente') DEFAULT 'cliente',
    activo TINYINT(1) DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    precio_original DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255),
    categoria VARCHAR(100),
    socio_id INT,
    activo TINYINT(1) DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_precio (precio),
    INDEX idx_categoria (categoria),
    INDEX idx_socio_id (socio_id),
    INDEX idx_activo (activo),
    INDEX idx_creado_en (creado_en),
    FOREIGN KEY (socio_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de ventas
CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    socio_id INT,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado ENUM('pendiente', 'pagado', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    metodo_pago VARCHAR(50),
    direccion_envio TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (socio_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_cliente_id (cliente_id),
    INDEX idx_socio_id (socio_id),
    INDEX idx_estado (estado),
    INDEX idx_creado_en (creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalles de venta
CREATE TABLE IF NOT EXISTS venta_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL,
    INDEX idx_venta_id (venta_id),
    INDEX idx_producto_id (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para cuentas sociales
CREATE TABLE IF NOT EXISTS usuarios_sociales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    social_id VARCHAR(255) NOT NULL,
    provider ENUM('facebook', 'google') NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_social_provider (social_id, provider),
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_social_id (social_id),
    INDEX idx_provider (provider)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de categorías de productos
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (nombre, apellido, email, clave, rol, activo) VALUES 
('Administrador', 'Sistema', 'admin@ofm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1)
ON DUPLICATE KEY UPDATE id=id;

-- Insertar categorías de ejemplo
INSERT INTO categorias (nombre, descripcion, activo) VALUES 
('Electrónicos', 'Productos electrónicos y tecnología', 1),
('Ropa', 'Vestimenta y accesorios', 1),
('Hogar', 'Artículos para el hogar', 1),
('Deportes', 'Equipos y ropa deportiva', 1),
('Belleza', 'Productos de belleza y cuidado personal', 1)
ON DUPLICATE KEY UPDATE id=id;

-- Insertar algunos productos de ejemplo
INSERT INTO productos (nombre, descripcion, precio, precio_original, stock, categoria, activo) VALUES 
('Laptop HP Pavilion', 'Laptop HP Pavilion 15.6" Intel Core i5 8GB RAM 256GB SSD', 899.99, 1099.99, 10, 'Electrónicos', 1),
('Smartphone Samsung Galaxy', 'Smartphone Samsung Galaxy A54 5G 128GB 6GB RAM', 449.99, 549.99, 15, 'Electrónicos', 1),
('Auriculares Bluetooth', 'Auriculares inalámbricos con cancelación de ruido', 89.99, 129.99, 25, 'Electrónicos', 1),
('Tablet iPad Air', 'Tablet Apple iPad Air 10.9" 64GB WiFi', 599.99, 699.99, 8, 'Electrónicos', 1),
('Smart TV 55"', 'Smart TV Samsung 55" 4K Ultra HD HDR', 699.99, 899.99, 5, 'Electrónicos', 1)
ON DUPLICATE KEY UPDATE id=id;
