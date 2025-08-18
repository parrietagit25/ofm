<?php
class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener usuario por email y contraseña
    public function autenticar($email, $clave) {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($clave, $usuario['clave'])) {
            // Actualizar último acceso
            $this->actualizarUltimoAcceso($usuario['id']);
            return $usuario;
        }
        return false;
    }

    // Obtener usuario por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por email
    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo usuario
    public function crear($nombre, $apellido, $email, $clave, $telefono, $rol = 'cliente') {
        // Verificar si el email ya existe
        if ($this->obtenerPorEmail($email)) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }

        $claveHash = password_hash($clave, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nombre, apellido, email, clave, telefono, rol, activo) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $this->pdo->prepare($sql);
        
        if ($stmt->execute([$nombre, $apellido, $email, $claveHash, $telefono, $rol])) {
            return ['success' => true, 'id' => $this->pdo->lastInsertId()];
        }
        return ['success' => false, 'message' => 'Error al crear el usuario'];
    }

    // Actualizar usuario
    public function actualizar($id, $datos) {
        $campos = [];
        $valores = [];
        
        foreach ($datos as $campo => $valor) {
            if ($campo === 'clave' && !empty($valor)) {
                $campos[] = "$campo = ?";
                $valores[] = password_hash($valor, PASSWORD_DEFAULT);
            } elseif ($campo !== 'clave') {
                $campos[] = "$campo = ?";
                $valores[] = $valor;
            }
        }
        
        $campos[] = "actualizado_en = NOW()";
        $valores[] = $id;
        
        $sql = "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        if ($stmt->execute($valores)) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Error al actualizar el usuario'];
    }

    // Cambiar contraseña
    public function cambiarClave($id, $nuevaClave) {
        $claveHash = password_hash($nuevaClave, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET clave = ?, actualizado_en = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$claveHash, $id]);
    }

    // Actualizar último acceso
    public function actualizarUltimoAcceso($id) {
        $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Obtener todos los usuarios por rol
    public function obtenerPorRol($rol) {
        $sql = "SELECT * FROM usuarios WHERE rol = ? AND activo = 1 ORDER BY creado_en DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$rol]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los usuarios
    public function obtenerTodos() {
        $sql = "SELECT * FROM usuarios ORDER BY creado_en DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los usuarios activos
    public function obtenerActivos() {
        $sql = "SELECT * FROM usuarios WHERE activo = 1 ORDER BY creado_en DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener estadísticas de usuarios
    public function obtenerEstadisticas() {
        $sql = "SELECT 
                    rol,
                    COUNT(*) as total,
                    COUNT(CASE WHEN ultimo_acceso >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as activos_30dias
                FROM usuarios 
                WHERE activo = 1 
                GROUP BY rol";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Activar/Desactivar usuario
    public function cambiarEstado($id, $activo) {
        $sql = "UPDATE usuarios SET activo = ?, actualizado_en = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$activo, $id]);
    }

    // Eliminar usuario (soft delete)
    public function eliminar($id) {
        return $this->cambiarEstado($id, 0);
    }

    // Verificar si el usuario tiene permisos de administrador
    public function esAdmin($id) {
        $usuario = $this->obtenerPorId($id);
        return $usuario && $usuario['rol'] === 'admin';
    }

    // Verificar si el usuario es socio
    public function esSocio($id) {
        $usuario = $this->obtenerPorId($id);
        return $usuario && $usuario['rol'] === 'socio';
    }

    // Verificar si el usuario es cliente
    public function esCliente($id) {
        $usuario = $this->obtenerPorId($id);
        return $usuario && $usuario['rol'] === 'cliente';
    }
}
