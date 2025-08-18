<?php
/**
 * Helper para manejo de sesiones en OFM
 * Evita problemas de session_start() duplicado
 */

/**
 * Inicia la sesión de forma segura
 * Solo inicia si no hay una sesión activa
 */
function iniciarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Verifica si hay una sesión activa
 */
function sesionActiva() {
    return session_status() === PHP_SESSION_ACTIVE;
}

/**
 * Obtiene el ID de usuario de la sesión
 */
function obtenerUsuarioId() {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtiene el rol del usuario de la sesión
 */
function obtenerUsuarioRol() {
    return $_SESSION['usuario_rol'] ?? null;
}

/**
 * Verifica si el usuario está autenticado
 */
function usuarioAutenticado() {
    return isset($_SESSION['usuario_id']) && $_SESSION['usuario_activo'] == 1;
}

/**
 * Verifica si el usuario tiene un rol específico
 */
function usuarioTieneRol($rol) {
    return usuarioAutenticado() && $_SESSION['usuario_rol'] === $rol;
}

/**
 * Cierra la sesión de forma segura
 */
function cerrarSesionSegura() {
    if (sesionActiva()) {
        session_destroy();
    }
}

/**
 * Regenera el ID de sesión para seguridad
 */
function regenerarIdSesion() {
    if (sesionActiva()) {
        session_regenerate_id(true);
    }
}
?>
