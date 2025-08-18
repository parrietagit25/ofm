-- Script para insertar productos de ejemplo en OFM
-- Ejecutar después de crear las tablas de productos

USE ofm;

-- Insertar productos de ejemplo
INSERT INTO `productos` (`comercio_id`, `nombre`, `descripcion`, `precio`, `precio_anterior`, `stock`, `categoria`, `marca`, `codigo_producto`, `status`, `destacado`) VALUES
-- Productos OFM (sin comercio)
(NULL, 'Laptop HP Pavilion Gaming', 'Laptop gaming con procesador Intel Core i7, 16GB RAM, 512GB SSD, NVIDIA GTX 1650', 1299.99, 1499.99, 15, 'Electrónicos', 'HP', 'HP-LAP-001', 'activo', 1),
(NULL, 'Smartphone Samsung Galaxy S23', 'Smartphone flagship con cámara de 108MP, 8GB RAM, 256GB almacenamiento', 899.99, 1099.99, 25, 'Electrónicos', 'Samsung', 'SAMS-S23-001', 'activo', 1),
(NULL, 'Auriculares Sony WH-1000XM4', 'Auriculares inalámbricos con cancelación de ruido activa', 349.99, 399.99, 30, 'Electrónicos', 'Sony', 'SONY-WH-001', 'activo', 0),
(NULL, 'Tablet Apple iPad Air', 'Tablet con chip M1, 10.9 pulgadas, 64GB, WiFi', 599.99, 699.99, 20, 'Electrónicos', 'Apple', 'APPLE-IPAD-001', 'activo', 1),
(NULL, 'Smart TV LG 55" 4K', 'Smart TV 55 pulgadas 4K Ultra HD con webOS', 799.99, 899.99, 12, 'Electrónicos', 'LG', 'LG-TV-001', 'activo', 0),

-- Productos de ropa
(NULL, 'Camisa Oxford de Algodón', 'Camisa casual de algodón 100%, disponible en varios colores', 49.99, 59.99, 50, 'Ropa', 'Fashion Brand', 'FASH-CAM-001', 'activo', 0),
(NULL, 'Jeans Slim Fit', 'Jeans de denim premium con stretch, corte slim fit', 79.99, 89.99, 40, 'Ropa', 'Denim Co', 'DENIM-JEAN-001', 'activo', 0),
(NULL, 'Vestido de Verano', 'Vestido ligero perfecto para el verano, estampado floral', 69.99, 79.99, 35, 'Ropa', 'Summer Style', 'SUMMER-VEST-001', 'activo', 1),

-- Productos del hogar
(NULL, 'Set de Sartenes Antiadherentes', 'Set de 5 sartenes con revestimiento antiadherente de cerámica', 129.99, 149.99, 25, 'Hogar', 'Kitchen Pro', 'KITCH-SART-001', 'activo', 0),
(NULL, 'Lámpara de Mesa LED', 'Lámpara de escritorio con luz ajustable y USB integrado', 89.99, 99.99, 30, 'Hogar', 'Light Home', 'LIGHT-LAMP-001', 'activo', 0),
(NULL, 'Juego de Toallas de Baño', 'Set de 4 toallas de algodón egipcio, 100% algodón', 59.99, 69.99, 45, 'Hogar', 'Bath Luxury', 'BATH-TOW-001', 'activo', 0),

-- Productos deportivos
(NULL, 'Zapatillas Running Nike Air Max', 'Zapatillas para correr con tecnología Air Max, suela de goma', 129.99, 149.99, 28, 'Deportes', 'Nike', 'NIKE-SHOE-001', 'activo', 1),
(NULL, 'Pelota de Fútbol Adidas', 'Pelota oficial de fútbol, tamaño 5, material sintético premium', 79.99, 89.99, 35, 'Deportes', 'Adidas', 'ADIDAS-BALL-001', 'activo', 0),
(NULL, 'Raqueta de Tenis Wilson', 'Raqueta de tenis profesional, encordada, peso balanceado', 199.99, 229.99, 15, 'Deportes', 'Wilson', 'WILSON-RACK-001', 'activo', 0),

-- Productos de belleza
(NULL, 'Set de Maquillaje Profesional', 'Set completo de maquillaje con 24 sombras y accesorios', 89.99, 109.99, 40, 'Belleza', 'Beauty Pro', 'BEAUTY-MAKE-001', 'activo', 0),
(NULL, 'Crema Hidratante Facial', 'Crema hidratante con ácido hialurónico, 50ml', 39.99, 49.99, 60, 'Belleza', 'Skin Care', 'SKIN-CREM-001', 'activo', 0),
(NULL, 'Perfume Unisex', 'Fragrancia elegante y duradera, notas de vainilla y sándalo', 69.99, 79.99, 25, 'Belleza', 'Luxury Scents', 'LUXURY-PERF-001', 'activo', 1);

-- Verificar que los productos se insertaron correctamente
SELECT 
    id, 
    nombre, 
    precio, 
    categoria, 
    status, 
    destacado,
    CASE 
        WHEN comercio_id IS NULL THEN 'OFM'
        ELSE 'Comercio'
    END as tipo
FROM productos 
ORDER BY id;

-- Mostrar estadísticas
SELECT 
    COUNT(*) as total_productos,
    COUNT(CASE WHEN status = 'activo' THEN 1 END) as productos_activos,
    COUNT(CASE WHEN destacado = 1 THEN 1 END) as productos_destacados,
    COUNT(CASE WHEN comercio_id IS NULL THEN 1 END) as productos_ofm,
    COUNT(CASE WHEN comercio_id IS NOT NULL THEN 1 END) as productos_comercio
FROM productos;
