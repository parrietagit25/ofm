<?php
class Venta {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener todas las ventas
    public function obtenerTodas() {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, c.email as cliente_email 
                FROM ventas v 
                LEFT JOIN usuarios c ON v.cliente_id = c.id 
                ORDER BY v.creado_en DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener ventas por socio (a través de su comercio)
    public function obtenerPorSocio($socioId) {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, c.email as cliente_email, 
                       co.nombre_comercio
                FROM ventas v 
                LEFT JOIN usuarios c ON v.cliente_id = c.id 
                LEFT JOIN comercios co ON v.comercio_id = co.id
                WHERE co.usuario_socio_id = ? 
                ORDER BY v.creado_en DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$socioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener ventas por cliente
    public function obtenerPorCliente($clienteId) {
        $sql = "SELECT v.*, co.nombre_comercio, u.nombre as socio_nombre, u.email as socio_email 
                FROM ventas v 
                LEFT JOIN comercios co ON v.comercio_id = co.id
                LEFT JOIN usuarios u ON co.usuario_socio_id = u.id 
                WHERE v.cliente_id = ? 
                ORDER BY v.creado_en DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener venta por ID
    public function obtenerPorId($id) {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, c.email as cliente_email,
                       co.nombre_comercio, u.nombre as socio_nombre, u.email as socio_email
                FROM ventas v 
                LEFT JOIN usuarios c ON v.cliente_id = c.id 
                LEFT JOIN comercios co ON v.comercio_id = co.id
                LEFT JOIN usuarios u ON co.usuario_socio_id = u.id 
                WHERE v.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nueva venta
    public function crear($cliente_id, $comercio_id, $total, $metodo_pago = null, $direccion_envio = null) {
        $sql = "INSERT INTO ventas (cliente_id, comercio_id, total, metodo_pago, status, creado_en) 
                VALUES (?, ?, ?, ?, 'pendiente', NOW())";
        $stmt = $this->pdo->prepare($sql);
        
        if ($stmt->execute([$cliente_id, $comercio_id, $total, $metodo_pago])) {
            return ['success' => true, 'id' => $this->pdo->lastInsertId()];
        }
        return ['success' => false, 'message' => 'Error al crear la venta'];
    }

    // Actualizar estado de la venta
    public function actualizarEstado($id, $status) {
        $sql = "UPDATE ventas SET status = ?, actualizado_en = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    // Actualizar venta
    public function actualizar($id, $total, $metodo_pago, $direccion_envio) {
        $sql = "UPDATE ventas SET total = ?, metodo_pago = ?, direccion_envio = ?, actualizado_en = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$total, $metodo_pago, $direccion_envio, $id]);
    }

    // Eliminar venta (soft delete)
    public function eliminar($id) {
        $sql = "UPDATE ventas SET status = 'cancelada', actualizado_en = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Obtener estadísticas de ventas por socio
    public function obtenerEstadisticasSocio($socioId, $periodo = 'mes') {
        $fechaInicio = '';
        switch ($periodo) {
            case 'semana':
                $fechaInicio = 'DATE_SUB(NOW(), INTERVAL 7 DAY)';
                break;
            case 'mes':
                $fechaInicio = 'DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                break;
            case 'año':
                $fechaInicio = 'DATE_SUB(NOW(), INTERVAL 1 YEAR)';
                break;
            default:
                $fechaInicio = 'DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        }

        $sql = "SELECT 
                    COUNT(*) as total_ventas,
                    SUM(total) as ingresos_totales,
                    AVG(total) as promedio_venta,
                    COUNT(CASE WHEN status = 'entregada' THEN 1 END) as ventas_completadas,
                    COUNT(CASE WHEN status = 'pendiente' THEN 1 END) as ventas_pendientes
                FROM ventas v
                INNER JOIN comercios c ON v.comercio_id = c.id
                WHERE c.usuario_socio_id = ? AND v.creado_en >= $fechaInicio";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$socioId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener ventas por período
    public function obtenerPorPeriodo($socioId, $fechaInicio, $fechaFin) {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, c.email as cliente_email 
                FROM ventas v 
                LEFT JOIN usuarios c ON v.cliente_id = c.id 
                WHERE v.socio_id = ? AND v.creado_en BETWEEN ? AND ? 
                ORDER BY v.creado_en DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$socioId, $fechaInicio, $fechaFin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener ventas por estado
    public function obtenerPorEstado($socioId, $estado) {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, c.email as cliente_email 
                FROM ventas v 
                LEFT JOIN usuarios c ON v.cliente_id = c.id 
                WHERE v.socio_id = ? AND v.estado = ? 
                ORDER BY v.creado_en DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$socioId, $estado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener total de ventas por día (últimos 30 días)
    public function obtenerVentasPorDia($socioId) {
        $sql = "SELECT 
                    DATE(creado_en) as fecha,
                    COUNT(*) as total_ventas,
                    SUM(total) as ingresos
                FROM ventas 
                WHERE socio_id = ? AND creado_en >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(creado_en)
                ORDER BY fecha DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$socioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
