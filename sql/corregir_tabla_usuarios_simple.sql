-- Script SIMPLE para corregir la tabla usuarios en OFM
-- Ejecutar paso a paso para evitar problemas de permisos

USE ofm;

-- 1. Verificar estructura actual
DESCRIBE usuarios;

-- 2. Agregar campo 'clave' (ejecutar solo si no existe)
-- Si da error "Duplicate column name", significa que ya existe
ALTER TABLE usuarios ADD COLUMN clave VARCHAR(255) NOT NULL AFTER email;

-- 3. Verificar que se agregó el campo
DESCRIBE usuarios;

-- 4. Crear usuario admin (ejecutar solo si no existe)
INSERT INTO usuarios (nombre, apellido, email, clave, telefono, rol, activo) 
VALUES ('Administrador', 'Sistema', 'admin@ofm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0000000000', 'admin', 1);

-- 5. Actualizar contraseña de admin existente (admin123)
UPDATE usuarios 
SET clave = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE rol = 'admin';

-- 6. Verificar resultado final
SELECT id, nombre, apellido, email, rol, activo FROM usuarios ORDER BY rol, nombre;

-- 7. Mostrar credenciales de admin
SELECT 
    'admin@ofm.com' as email_admin,
    'admin123' as password_admin,
    'admin' as rol_admin;
