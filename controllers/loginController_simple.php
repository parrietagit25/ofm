<?php
/**
 * Controlador de Login Simplificado para OFM
 * Versión que funciona sin campo de contraseña
 */

// Incluir helper de sesiones
require_once __DIR__ . '/../includes/session_helper.php';

// Iniciar sesión de forma segura
iniciarSesionSegura();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../models/Usuario_simple.php';

class LoginControllerSimple {
    private $pdo;
    private $usuario;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->usuario = new UsuarioSimple($pdo);
    }

    // Login con email y contraseña
    public function procesarLogin($email, $clave) {
        // Validaciones básicas
        if (empty($email)) {
            return ['success' => false, 'message' => 'El email es obligatorio'];
        }

        if (empty($clave)) {
            return ['success' => false, 'message' => 'La contraseña es obligatoria'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Formato de email inválido'];
        }

        // Autenticar usuario con contraseña
        $usuario = $this->usuario->autenticar($email, $clave);
        
        if ($usuario) {
            // Crear sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['usuario_activo'] = $usuario['activo'];
            $_SESSION['login_time'] = time();

            return [
                'success' => true, 
                'message' => 'Login exitoso',
                'rol' => $usuario['rol'],
                'redirect' => $this->obtenerUrlDashboard($usuario['rol'])
            ];
        } else {
            return ['success' => false, 'message' => 'Email o contraseña incorrectos'];
        }
    }

    // Procesar registro con contraseña
    public function procesarRegistro($nombre, $apellido, $email, $clave, $telefono) {
        // Validaciones
        if (empty($nombre) || empty($apellido) || empty($email) || empty($clave)) {
            return ['success' => false, 'message' => 'Nombre, apellido, email y contraseña son obligatorios'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Formato de email inválido'];
        }

        if (strlen($clave) < 6) {
            return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'];
        }

        // Verificar si el email ya existe
        if ($this->usuario->obtenerPorEmail($email)) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }

        // Crear usuario (por defecto como cliente, con contraseña)
        $resultado = $this->usuario->crear($nombre, $apellido, $email, $clave, $telefono, 'cliente');
        
        if ($resultado['success']) {
            // Auto-login después del registro
            $usuario = $this->usuario->obtenerPorId($resultado['id']);
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['usuario_activo'] = $usuario['activo'];
            $_SESSION['login_time'] = time();

            return [
                'success' => true, 
                'message' => 'Registro exitoso. Bienvenido a OFM!',
                'rol' => $usuario['rol'],
                'redirect' => $this->obtenerUrlDashboard($usuario['rol'])
            ];
        }

        return $resultado;
    }

    // Cerrar sesión
    public function cerrarSesion() {
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada correctamente'];
    }

    // Verificar si el usuario está autenticado
    public function estaAutenticado() {
        return isset($_SESSION['usuario_id']) && $_SESSION['usuario_activo'] == 1;
    }

    // Verificar si el usuario tiene un rol específico
    public function tieneRol($rol) {
        return $this->estaAutenticado() && $_SESSION['usuario_rol'] === $rol;
    }

    // Obtener información del usuario actual
    public function obtenerUsuarioActual() {
        if (!$this->estaAutenticado()) {
            return null;
        }

        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'email' => $_SESSION['usuario_email'],
            'rol' => $_SESSION['usuario_rol']
        ];
    }

    // Redirigir según el rol
    public function redirigirSegunRol() {
        if (!$this->estaAutenticado()) {
            header('Location: /ofm/public/evara/page-login-register.php');
            exit;
        }

        $rol = $_SESSION['usuario_rol'];
        $url = $this->obtenerUrlDashboard($rol);
        
        header("Location: $url");
        exit;
    }

    // Obtener URL del dashboard según el rol
    private function obtenerUrlDashboard($rol) {
        switch ($rol) {
            case 'admin':
                return '/ofm/admin/dashboard.php';
            case 'socio':
                return '/ofm/socio/dashboard.php';
            case 'cliente':
                return '/ofm/cliente/dashboard.php';
            default:
                return '/ofm/public/evara/page-login-register.php';
        }
    }

    // Verificar permisos de acceso
    public function verificarAcceso($rolRequerido) {
        if (!$this->estaAutenticado()) {
            header('Location: /ofm/public/evara/page-login-register.php');
            exit;
        }

        if ($_SESSION['usuario_rol'] !== $rolRequerido) {
            // Redirigir al dashboard correspondiente
            $this->redirigirSegunRol();
        }
    }

    // Verificar si la sesión ha expirado (8 horas)
    public function verificarExpiracionSesion() {
        if (isset($_SESSION['login_time'])) {
            $tiempoActual = time();
            $tiempoLogin = $_SESSION['login_time'];
            $tiempoExpiracion = 8 * 60 * 60; // 8 horas en segundos

            if (($tiempoActual - $tiempoLogin) > $tiempoExpiracion) {
                $this->cerrarSesion();
                return false;
            }
        }
        return true;
    }
}

// Instanciar controlador
$loginController = new LoginControllerSimple($pdo);

// Procesar acciones si se reciben por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $clave = $_POST['clave'] ?? '';
            $resultado = $loginController->procesarLogin($email, $clave);
            
            if ($resultado['success']) {
                header("Location: " . $resultado['redirect']);
                exit;
            } else {
                $error = $resultado['message'];
            }
            break;
            
        case 'registro':
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $email = $_POST['email'] ?? '';
            $clave = $_POST['clave'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            
            $resultado = $loginController->procesarRegistro($nombre, $apellido, $email, $clave, $telefono);
            
            if ($resultado['success']) {
                header("Location: " . $resultado['redirect']);
                exit;
            } else {
                $error = $resultado['message'];
            }
            break;
            
        case 'logout':
            $loginController->cerrarSesion();
            header('Location: /ofm/public/evara/page-login-register.php');
            exit;
            break;
    }
}
?>
