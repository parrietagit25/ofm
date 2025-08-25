<?php
/**
 * Archivo de configuración central para OFM
 * Contiene todas las configuraciones del sistema
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'ofm');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de rutas
define('BASE_URL', '/ofm');
define('UPLOADS_PATH', __DIR__ . '/../uploads');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Rutas específicas de imágenes
define('PRODUCTOS_IMAGENES_PATH', __DIR__ . '/../public/uploads/productos');
define('PRODUCTOS_IMAGENES_URL', BASE_URL . '/public/uploads/productos');
define('USUARIOS_IMAGENES_PATH', UPLOADS_PATH . '/usuarios');
define('USUARIOS_IMAGENES_URL', UPLOADS_URL . '/usuarios');

// Configuración de sesión
define('SESSION_LIFETIME', 8 * 60 * 60); // 8 horas en segundos
define('SESSION_NAME', 'OFM_SESSION');

// Configuración de paginación
define('PRODUCTOS_POR_PAGINA', 12);
define('ADMIN_PRODUCTOS_POR_PAGINA', 20);

// Configuración de archivos
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configuración de seguridad
define('PASSWORD_MIN_LENGTH', 6);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 15 * 60); // 15 minutos

// Configuración de la aplicación
define('APP_NAME', 'OFM - Tu Marketplace de Confianza');
define('APP_VERSION', '1.0.0');
define('APP_EMAIL', 'info@ofm.com');
define('APP_PHONE', '(+01) - 2345 - 6789');

// Configuración de timezone
date_default_timezone_set('America/Mexico_City');

// Configuración de errores (solo para desarrollo)
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Función para obtener la URL base
function getBaseUrl() {
    return BASE_URL;
}

// Función para obtener la URL de imágenes de productos
function getProductImageUrl($nombreArchivo) {
    if (empty($nombreArchivo)) {
        return BASE_URL . '/public/evara/assets/imgs/shop/default.png';
    }
    return PRODUCTOS_IMAGENES_URL . '/' . $nombreArchivo;
}

// Función para obtener la ruta física de imágenes de productos
function getProductImagePath($nombreArchivo) {
    return PRODUCTOS_IMAGENES_PATH . '/' . $nombreArchivo;
}

// Función para verificar si una imagen existe
function imageExists($nombreArchivo, $tipo = 'producto') {
    if ($tipo === 'producto') {
        return file_exists(getProductImagePath($nombreArchivo));
    }
    return false;
}

// Función para limpiar nombres de archivo
function sanitizeFileName($fileName) {
    // Remover caracteres especiales y espacios
    $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
    // Remover múltiples guiones bajos
    $fileName = preg_replace('/_+/', '_', $fileName);
    return $fileName;
}

// Función para generar nombre único de archivo
function generateUniqueFileName($originalName, $prefix = '') {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $name = pathinfo($originalName, PATHINFO_FILENAME);
    $timestamp = time();
    $random = uniqid();
    
    $fileName = $prefix . $name . '_' . $timestamp . '_' . $random . '.' . $extension;
    return sanitizeFileName($fileName);
}
?>
