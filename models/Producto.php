<?php

class Producto {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Crear un nuevo producto
     */
    public function crear($datos) {
        try {
            // Insertar el producto
            $stmt = $this->pdo->prepare("
                INSERT INTO productos (
                    comercio_id, nombre, descripcion, precio, precio_anterior, 
                    stock, categoria, marca, codigo_producto, peso, 
                    dimensiones, status, destacado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $resultado = $stmt->execute([
                $datos['comercio_id'] ?: null,
                $datos['nombre'],
                $datos['descripcion'] ?: null,
                $datos['precio'],
                $datos['precio_anterior'] ?: null,
                $datos['stock'] ?: 0,
                $datos['categoria'] ?: null,
                $datos['marca'] ?: null,
                $datos['codigo_producto'] ?: null,
                $datos['peso'] ?: null,
                $datos['dimensiones'] ?: null,
                $datos['status'] ?: 'activo',
                $datos['destacado'] ?: 0
            ]);
            
            if ($resultado) {
                return ['success' => true, 'message' => 'Producto creado exitosamente', 'id' => $this->pdo->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Error al crear el producto'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener todos los productos con información del comercio
     */
    public function obtenerTodos($offset = 0, $limit = 10, $filtros = []) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filtros['status'])) {
                $where[] = "p.status = ?";
                $params[] = $filtros['status'];
            }
            
            if (!empty($filtros['categoria'])) {
                $where[] = "p.categoria = ?";
                $params[] = $filtros['categoria'];
            }
            
            if (!empty($filtros['comercio_id'])) {
                if ($filtros['comercio_id'] === 'null') {
                    $where[] = "p.comercio_id IS NULL";
                } else {
                    $where[] = "p.comercio_id = ?";
                    $params[] = $filtros['comercio_id'];
                }
            }
            
            if (isset($filtros['destacado']) && $filtros['destacado'] !== '') {
                $where[] = "p.destacado = ?";
                $params[] = $filtros['destacado'];
            }
            
            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            
            $sql = "
                SELECT p.*, c.nombre_comercio, c.direccion as direccion_comercio
                FROM productos p
                LEFT JOIN comercios c ON p.comercio_id = c.id
                $whereClause
                ORDER BY p.creado_en DESC
                LIMIT $limit OFFSET $offset
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Agregar imagen principal a cada producto
            foreach ($productos as &$producto) {
                $producto['imagen_principal'] = $this->obtenerImagenPrincipal($producto['id']);
            }
            
            return $productos;
        } catch (PDOException $e) {
            error_log("Error en obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener total de productos con filtros
     */
    public function obtenerTotal($filtros = []) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filtros['status'])) {
                $where[] = "status = ?";
                $params[] = $filtros['status'];
            }
            
            if (!empty($filtros['categoria'])) {
                $where[] = "categoria = ?";
                $params[] = $filtros['categoria'];
            }
            
            if (!empty($filtros['comercio_id'])) {
                $where[] = "comercio_id = ?";
                $params[] = $filtros['comercio_id'];
            }
            
            if (!empty($filtros['destacado'])) {
                $where[] = "destacado = ?";
                $params[] = $filtros['destacado'];
            }
            
            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            
            $sql = "SELECT COUNT(*) as total FROM productos $whereClause";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Obtener solo productos activos con información del comercio
     */
    public function obtenerActivos() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.nombre_comercio, c.direccion as direccion_comercio
                FROM productos p
                LEFT JOIN comercios c ON p.comercio_id = c.id
                WHERE p.status = 'activo'
                ORDER BY p.creado_en DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener productos destacados
     */
    public function obtenerDestacados() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.nombre_comercio, c.direccion as direccion_comercio
                FROM productos p
                LEFT JOIN comercios c ON p.comercio_id = c.id
                WHERE p.status = 'activo' AND p.destacado = 1
                ORDER BY p.creado_en DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener productos por comercio
     */
    public function obtenerPorComercio($comercio_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM productos 
                WHERE comercio_id = ? 
                ORDER BY creado_en DESC
            ");
            $stmt->execute([$comercio_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener productos propios de OFM (sin comercio)
     */
    public function obtenerPropiosOFM() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM productos 
                WHERE comercio_id IS NULL 
                ORDER BY creado_en DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener producto por ID
     */
    public function obtenerPorId($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.nombre_comercio, c.direccion as direccion_comercio
                FROM productos p
                LEFT JOIN comercios c ON p.comercio_id = c.id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Actualizar producto
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
            
            $sql = "UPDATE productos SET " . implode(', ', $campos) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute($valores)) {
                return ['success' => true, 'message' => 'Producto actualizado exitosamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar el producto'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cambiar estado del producto
     */
    public function cambiarEstado($id, $status) {
        try {
            $stmt = $this->pdo->prepare("UPDATE productos SET status = ? WHERE id = ?");
            if ($stmt->execute([$status, $id])) {
                return ['success' => true, 'message' => 'Estado del producto actualizado'];
            } else {
                return ['success' => false, 'message' => 'Error al cambiar el estado'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cambiar estado destacado
     */
    public function cambiarDestacado($id, $destacado) {
        try {
            $stmt = $this->pdo->prepare("UPDATE productos SET destacado = ? WHERE id = ?");
            if ($stmt->execute([$destacado, $id])) {
                return ['success' => true, 'message' => 'Estado destacado actualizado'];
            } else {
                return ['success' => false, 'message' => 'Error al cambiar el estado destacado'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar producto (cambiar a inactivo)
     */
    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE productos SET status = 'inactivo' WHERE id = ?");
            if ($stmt->execute([$id])) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener estadísticas de productos
     */
    public function obtenerEstadisticas() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_productos,
                    SUM(CASE WHEN status = 'activo' THEN 1 ELSE 0 END) as productos_activos,
                    SUM(CASE WHEN status = 'inactivo' THEN 1 ELSE 0 END) as productos_inactivos,
                    SUM(CASE WHEN status = 'agotado' THEN 1 ELSE 0 END) as productos_agotados,
                    SUM(CASE WHEN status = 'en_oferta' THEN 1 ELSE 0 END) as productos_oferta,
                    SUM(CASE WHEN destacado = 1 THEN 1 ELSE 0 END) as productos_destacados,
                    SUM(CASE WHEN comercio_id IS NULL THEN 1 ELSE 0 END) as productos_ofm,
                    SUM(CASE WHEN comercio_id IS NOT NULL THEN 1 ELSE 0 END) as productos_comercio
                FROM productos
            ");
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            return [
                'total_productos' => 0, 'productos_activos' => 0, 'productos_inactivos' => 0,
                'productos_agotados' => 0, 'productos_oferta' => 0, 'productos_destacados' => 0,
                'productos_ofm' => 0, 'productos_comercio' => 0
            ];
        }
    }
    
    /**
     * Obtener categorías disponibles
     */
    public function obtenerCategorias() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT categoria 
                FROM productos 
                WHERE categoria IS NOT NULL AND categoria != '' 
                ORDER BY categoria
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener marcas disponibles
     */
    public function obtenerMarcas() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT marca 
                FROM productos 
                WHERE marca IS NOT NULL AND marca != '' 
                ORDER BY marca
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Buscar productos
     */
    public function buscar($termino, $categoria = null, $marca = null, $status = null) {
        try {
            $sql = "
                SELECT p.*, c.nombre_comercio, c.direccion as direccion_comercio
                FROM productos p
                LEFT JOIN comercios c ON p.comercio_id = c.id
                WHERE 1=1
            ";
            $params = [];
            
            if ($termino) {
                $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ? OR p.codigo_producto LIKE ?)";
                $params[] = "%$termino%";
                $params[] = "%$termino%";
                $params[] = "%$termino%";
            }
            
            if ($categoria) {
                $sql .= " AND p.categoria = ?";
                $params[] = $categoria;
            }
            
            if ($marca) {
                $sql .= " AND p.marca = ?";
                $params[] = $marca;
            }
            
            if ($status) {
                $sql .= " AND p.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY p.creado_en DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtener imagen principal de un producto
     */
    private function obtenerImagenPrincipal($productoId) {
        try {
            $sql = "SELECT nombre_archivo FROM producto_imagenes WHERE producto_id = ? ORDER BY creado_en ASC LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$productoId]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado ? $resultado['nombre_archivo'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
