<?php
/**
 * Modelo de Usuario Simplificado para OFM
 * Versión que funciona sin campo de contraseña
 */

class UsuarioSimple {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener usuario por ID
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM usuarios WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener usuario por email
    public function obtenerPorEmail($email) {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Autenticar usuario con contraseña
    public function autenticar($email, $clave) {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($clave, $usuario['clave'])) {
                return $usuario;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener todos los usuarios
    public function obtenerTodos() {
        try {
            $sql = "SELECT * FROM usuarios ORDER BY creado_en DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Obtener usuarios por rol
    public function obtenerPorRol($rol) {
        try {
            $sql = "SELECT * FROM usuarios WHERE rol = ? AND activo = 1 ORDER BY creado_en DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$rol]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Obtener usuarios activos
    public function obtenerActivos() {
        try {
            $sql = "SELECT * FROM usuarios WHERE activo = 1 ORDER BY creado_en DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Crear nuevo usuario (con contraseña)
    public function crear($nombre, $apellido, $email, $clave, $telefono, $rol = 'cliente') {
        try {
            // Verificar si el email ya existe
            if ($this->obtenerPorEmail($email)) {
                return ['success' => false, 'message' => 'El email ya está registrado'];
            }

            // Hash de la contraseña
            $claveHash = password_hash($clave, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nombre, apellido, email, clave, telefono, rol, activo) 
                    VALUES (?, ?, ?, ?, ?, ?, 1)";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$nombre, $apellido, $email, $claveHash, $telefono, $rol])) {
                return ['success' => true, 'id' => $this->pdo->lastInsertId()];
            }
            return ['success' => false, 'message' => 'Error al crear el usuario'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    // Actualizar usuario
    public function actualizar($id, $datos) {
        try {
            $campos = [];
            $valores = [];
            
            foreach ($datos as $campo => $valor) {
                if ($campo !== 'id') {
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
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    // Cambiar estado de usuario
    public function cambiarEstado($id, $activo) {
        try {
            $sql = "UPDATE usuarios SET activo = ?, actualizado_en = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$activo, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Eliminar usuario (cambiar a inactivo)
    public function eliminar($id) {
        return $this->cambiarEstado($id, 0);
    }

    // Obtener estadísticas
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_usuarios,
                        COUNT(CASE WHEN activo = 1 THEN 1 END) as usuarios_activos,
                        COUNT(CASE WHEN rol = 'admin' THEN 1 END) as total_admin,
                        COUNT(CASE WHEN rol = 'socio' THEN 1 END) as total_socio,
                        COUNT(CASE WHEN rol = 'cliente' THEN 1 END) as total_cliente
                    FROM usuarios";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                'total_usuarios' => 0,
                'usuarios_activos' => 0,
                'total_admin' => 0,
                'total_socio' => 0,
                'total_cliente' => 0
            ];
        }
    }

    // Buscar usuarios
    public function buscar($termino) {
        try {
            $sql = "SELECT * FROM usuarios 
                    WHERE nombre LIKE ? OR apellido LIKE ? OR email LIKE ? 
                    ORDER BY nombre, apellido";
            $termino = "%$termino%";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$termino, $termino, $termino]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Verificar si usuario existe
    public function existe($email) {
        return $this->obtenerPorEmail($email) !== false;
    }

    // Obtener usuarios recientes
    public function obtenerRecientes($limite = 10) {
        try {
            $sql = "SELECT * FROM usuarios ORDER BY creado_en DESC LIMIT ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$limite]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
