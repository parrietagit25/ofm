<?php

class Comercio {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Crear un nuevo comercio
     */
    public function crear($usuario_socio_id, $nombre_comercio, $descripcion, $direccion, $telefono_comercio, $email_comercio, $activo = 1) {
        try {
            // Verificar que el usuario socio existe
            $stmt = $this->pdo->prepare("SELECT id, rol FROM usuarios WHERE id = ? AND rol = 'socio'");
            $stmt->execute([$usuario_socio_id]);
            $usuario = $stmt->fetch();
            
            if (!$usuario) {
                return ['success' => false, 'message' => 'El usuario socio no existe o no es válido'];
            }
            
            // Insertar el comercio
            $stmt = $this->pdo->prepare("
                INSERT INTO comercios (usuario_socio_id, nombre_comercio, descripcion, direccion, telefono_comercio, email_comercio, activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $resultado = $stmt->execute([
                $usuario_socio_id,
                $nombre_comercio,
                $descripcion,
                $direccion,
                $telefono_comercio,
                $email_comercio,
                $activo
            ]);
            
            if ($resultado) {
                return ['success' => true, 'message' => 'Comercio creado exitosamente', 'id' => $this->pdo->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Error al crear el comercio'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener todos los comercios con información del usuario socio
     */
    public function obtenerTodos() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.nombre, u.apellido, u.email as email_usuario, u.telefono as telefono_usuario
                FROM comercios c
                INNER JOIN usuarios u ON c.usuario_socio_id = u.id
                ORDER BY c.creado_en DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener comercio por ID
     */
    public function obtenerPorId($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.nombre, u.apellido, u.email as email_usuario, u.telefono as telefono_usuario
                FROM comercios c
                INNER JOIN usuarios u ON c.usuario_socio_id = u.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener comercios por usuario socio
     */
    public function obtenerPorUsuarioSocio($usuario_socio_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM comercios 
                WHERE usuario_socio_id = ? 
                ORDER BY creado_en DESC
            ");
            $stmt->execute([$usuario_socio_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Actualizar comercio
     */
    public function actualizar($id, $datos) {
        try {
            $campos = [];
            $valores = [];
            
            foreach ($datos as $campo => $valor) {
                if ($campo !== 'id') {
                    $campos[] = "`$campo` = ?";
                    $valores[] = $valor;
                }
            }
            
            $valores[] = $id;
            
            $sql = "UPDATE comercios SET " . implode(', ', $campos) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute($valores)) {
                return ['success' => true, 'message' => 'Comercio actualizado exitosamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar el comercio'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cambiar estado del comercio
     */
    public function cambiarEstado($id, $activo) {
        try {
            $stmt = $this->pdo->prepare("UPDATE comercios SET activo = ? WHERE id = ?");
            if ($stmt->execute([$activo, $id])) {
                return ['success' => true, 'message' => 'Estado del comercio actualizado'];
            } else {
                return ['success' => false, 'message' => 'Error al cambiar el estado'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar comercio
     */
    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM comercios WHERE id = ?");
            if ($stmt->execute([$id])) {
                return ['success' => true, 'message' => 'Comercio eliminado exitosamente'];
            } else {
                return ['success' => false, 'message' => 'Error al eliminar el comercio'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener estadísticas de comercios
     */
    public function obtenerEstadisticas() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_comercios,
                    SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as comercios_activos,
                    SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) as comercios_inactivos
                FROM comercios
            ");
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            return ['total_comercios' => 0, 'comercios_activos' => 0, 'comercios_inactivos' => 0];
        }
    }
    
    /**
     * Verificar si un usuario socio ya tiene un comercio con el mismo nombre
     */
    public function verificarNombreDuplicado($usuario_socio_id, $nombre_comercio, $excluir_id = null) {
        try {
            $sql = "SELECT id FROM comercios WHERE usuario_socio_id = ? AND nombre_comercio = ?";
            $params = [$usuario_socio_id, $nombre_comercio];
            
            if ($excluir_id) {
                $sql .= " AND id != ?";
                $params[] = $excluir_id;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
}
