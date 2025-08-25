<?php
/**
 * Controlador de login simplificado para el panel administrativo
 */

require_once __DIR__ . '/../includes/db.php';

class LoginControllerSimple {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Verificar acceso del usuario
     */
    public function verificarAcceso($rolRequerido = 'admin') {
        // Verificar si está autenticado
        if (!estaAutenticado()) {
            header('Location: /ofm/public/evara/page-login-register.php');
            exit;
        }
        
        // Verificar rol
        if (!tieneRol($rolRequerido)) {
            header('Location: /ofm/public/evara/page-login-register.php?error=unauthorized');
            exit;
        }
        
        // Verificar expiración
        verificarExpiracionSesion();
        
        return true;
    }
    
    /**
     * Autenticar usuario
     */
    public function autenticar($email, $password) {
        try {
            $sql = "SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ? AND rol = 'admin'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Establecer sesión
                establecerSesionUsuario($usuario);
                return ['success' => true, 'usuario' => $usuario];
            }
            
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        } catch (PDOException $e) {
            error_log("Error en autenticación: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error del servidor'];
        }
    }
    
    /**
     * Obtener usuario actual
     */
    public function obtenerUsuarioActual() {
        if (!estaAutenticado()) {
            return null;
        }
        
        try {
            $sql = "SELECT id, nombre, email, rol FROM usuarios WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([obtenerUsuarioId()]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error obteniendo usuario: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function cerrarSesion() {
        cerrarSesion();
        return true;
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function estaAutenticado() {
        return estaAutenticado();
    }
    
    /**
     * Verificar si el usuario tiene el rol especificado
     */
    public function tieneRol($rol) {
        return tieneRol($rol);
    }
}
?>
