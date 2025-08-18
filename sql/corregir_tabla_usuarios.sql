-- Script para corregir la estructura de la tabla usuarios en OFM
-- Ejecutar si hay problemas con el campo 'clave'
-- Versión compatible con MySQL 5.7+

USE ofm;

-- Verificar estructura actual de la tabla
DESCRIBE usuarios;

-- Verificar si existe el campo 'clave' de forma segura
SELECT COUNT(*) as existe_campo_clave
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'ofm' 
  AND TABLE_NAME = 'usuarios' 
  AND COLUMN_NAME = 'clave';

-- Agregar campo 'clave' solo si no existe (método compatible)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'ofm' 
       AND TABLE_NAME = 'usuarios' 
       AND COLUMN_NAME = 'clave') = 0,
    'ALTER TABLE usuarios ADD COLUMN clave VARCHAR(255) NOT NULL AFTER email',
    'SELECT "Campo clave ya existe" as mensaje'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar si existe usuario admin
SELECT COUNT(*) as total_admin FROM usuarios WHERE rol = 'admin';

-- Crear usuario admin si no existe
INSERT INTO usuarios (nombre, apellido, email, clave, telefono, rol, activo) 
SELECT 'Administrador', 'Sistema', 'admin@ofm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0000000000', 'admin', 1
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE rol = 'admin');

-- Actualizar contraseña del usuario admin existente (admin123)
UPDATE usuarios 
SET clave = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE rol = 'admin' AND (clave IS NULL OR clave = '');

-- Verificar estructura final
DESCRIBE usuarios;

-- Mostrar usuarios admin
SELECT id, nombre, apellido, email, rol, activo, 
       CASE WHEN clave IS NOT NULL AND clave != '' THEN 'Sí' ELSE 'No' END as tiene_clave
FROM usuarios WHERE rol = 'admin';

-- Mostrar todos los usuarios
SELECT id, nombre, apellido, email, rol, activo, 
       CASE WHEN clave IS NOT NULL AND clave != '' THEN 'Sí' ELSE 'No' END as tiene_clave
FROM usuarios ORDER BY rol, nombre;

-- Mostrar resumen final
SELECT 
    COUNT(*) as total_usuarios,
    COUNT(CASE WHEN rol = 'admin' THEN 1 END) as total_admin,
    COUNT(CASE WHEN rol = 'socio' THEN 1 END) as total_socio,
    COUNT(CASE WHEN rol = 'cliente' THEN 1 END) as total_cliente,
    COUNT(CASE WHEN clave IS NOT NULL AND clave != '' THEN 1 END) as usuarios_con_clave
FROM usuarios;
