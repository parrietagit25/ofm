<?php

class QRVerificationController {
    private $pdo;
    private $loginController;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loginController = new LoginController($pdo);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Verificar un código QR
     */
    public function verificarQR($codigoQR, $datosVerificacion = []) {
        try {
            // Buscar el detalle de orden por código QR
            $sql = "SELECT od.*, o.numero_orden, o.fecha_orden, o.estado as estado_orden,
                           p.nombre as nombre_producto, p.imagen_principal,
                           c.nombre as nombre_comercio, s.nombre as nombre_socio
                    FROM orden_detalles od 
                    JOIN ordenes o ON od.orden_id = o.id 
                    JOIN productos p ON od.producto_id = p.id 
                    JOIN comercios c ON od.comercio_id = c.id 
                    JOIN socios s ON od.socio_id = s.id 
                    WHERE od.codigo_qr = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$codigoQR]);
            $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$detalle) {
                return ['success' => false, 'message' => 'Código QR no encontrado'];
            }
            
            // Verificar estado del QR
            if ($detalle['estado_qr'] === 'utilizado') {
                return ['success' => false, 'message' => 'Este código QR ya ha sido utilizado'];
            }
            
            // Verificar estado de la orden
            if ($detalle['estado_orden'] !== 'enviada' && $detalle['estado_orden'] !== 'entregada') {
                return ['success' => false, 'message' => 'La orden no está lista para verificación'];
            }
            
            // Obtener información del usuario que verifica
            $verificador = $this->loginController->obtenerUsuarioActual();
            if (!$verificador) {
                return ['success' => false, 'message' => 'Usuario no autenticado'];
            }
            
            // Registrar la verificación
            $verificacion = $this->registrarVerificacion($detalle['id'], $codigoQR, $verificador['id'], $datosVerificacion);
            if (!$verificacion['success']) {
                return $verificacion;
            }
            
            // Actualizar estado del QR
            $this->actualizarEstadoQR($detalle['id'], 'utilizado');
            
            // Crear notificación para el socio
            $this->crearNotificacionSocio($detalle['socio_id'], $detalle['nombre_producto'], $detalle['numero_orden']);
            
            // Actualizar estadísticas del socio
            $this->actualizarEstadisticasSocio($detalle['socio_id']);
            
            return [
                'success' => true,
                'message' => 'QR verificado exitosamente',
                'producto' => [
                    'nombre' => $detalle['nombre_producto'],
                    'comercio' => $detalle['nombre_comercio'],
                    'socio' => $detalle['nombre_socio'],
                    'orden' => $detalle['numero_orden'],
                    'fecha_orden' => $detalle['fecha_orden']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Error verificando QR: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    /**
     * Registrar la verificación del QR
     */
    private function registrarVerificacion($ordenDetalleId, $codigoQR, $verificadorId, $datosVerificacion) {
        try {
            $sql = "INSERT INTO qr_verificaciones (
                orden_detalle_id, codigo_qr, verificado_por, ubicacion_verificacion,
                dispositivo_verificacion, ip_verificacion, resultado, notas
            ) VALUES (?, ?, ?, ?, ?, ?, 'exitoso', ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $ordenDetalleId,
                $codigoQR,
                $verificadorId,
                $datosVerificacion['ubicacion'] ?? 'No especificada',
                $datosVerificacion['dispositivo'] ?? 'No especificado',
                $datosVerificacion['ip'] ?? $_SERVER['REMOTE_ADDR'] ?? 'No disponible',
                $datosVerificacion['notas'] ?? ''
            ]);
            
            return ['success' => true, 'verificacion_id' => $this->pdo->lastInsertId()];
            
        } catch (Exception $e) {
            throw new Exception('Error registrando verificación: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualizar estado del QR
     */
    private function actualizarEstadoQR($ordenDetalleId, $estado) {
        $sql = "UPDATE orden_detalles SET 
                estado_qr = ?, 
                fecha_verificacion = NOW(), 
                verificado_por = ? 
                WHERE id = ?";
        
        $verificador = $this->loginController->obtenerUsuarioActual();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$estado, $verificador['id'], $ordenDetalleId]);
    }
    
    /**
     * Crear notificación para el socio
     */
    private function crearNotificacionSocio($socioId, $nombreProducto, $numeroOrden) {
        try {
            $sql = "INSERT INTO notificaciones_socios (
                socio_id, tipo, titulo, mensaje
            ) VALUES (?, 'qr_verificado', ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $socioId,
                'Producto entregado y verificado',
                "El producto '{$nombreProducto}' de la orden {$numeroOrden} ha sido entregado y verificado exitosamente"
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error creando notificación para socio: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar estadísticas del socio
     */
    private function actualizarEstadisticasSocio($socioId) {
        try {
            $fecha = date('Y-m-d');
            
            // Verificar si ya existe estadística para hoy
            $sqlCheck = "SELECT id FROM estadisticas_ventas_socios 
                        WHERE socio_id = ? AND fecha = ?";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([$socioId, $fecha]);
            
            if ($stmtCheck->fetch()) {
                // Actualizar estadística existente
                $sqlUpdate = "UPDATE estadisticas_ventas_socios 
                             SET qr_verificados = qr_verificados + 1 
                             WHERE socio_id = ? AND fecha = ?";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([$socioId, $fecha]);
            } else {
                // Crear nueva estadística
                $sqlInsert = "INSERT INTO estadisticas_ventas_socios (
                    socio_id, fecha, qr_verificados
                ) VALUES (?, ?, 1)";
                $stmtInsert = $this->pdo->prepare($sqlInsert);
                $stmtInsert->execute([$socioId, $fecha]);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error actualizando estadísticas del socio: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener historial de verificaciones de un QR
     */
    public function obtenerHistorialVerificaciones($codigoQR) {
        try {
            $sql = "SELECT qv.*, u.nombre as nombre_verificador, u.email as email_verificador
                    FROM qr_verificaciones qv 
                    JOIN usuarios u ON qv.verificado_por = u.id 
                    WHERE qv.codigo_qr = ? 
                    ORDER BY qv.fecha_verificacion DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$codigoQR]);
            $verificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'verificaciones' => $verificaciones];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error obteniendo historial: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener estadísticas de verificaciones por socio
     */
    public function obtenerEstadisticasVerificaciones($socioId, $fechaInicio = null, $fechaFin = null) {
        try {
            $where = "WHERE s.id = ?";
            $params = [$socioId];
            
            if ($fechaInicio && $fechaFin) {
                $where .= " AND evs.fecha BETWEEN ? AND ?";
                $params[] = $fechaInicio;
                $params[] = $fechaFin;
            }
            
            $sql = "SELECT evs.fecha, evs.total_ventas, evs.cantidad_productos, 
                           evs.total_comisiones, evs.qr_verificados
                    FROM estadisticas_ventas_socios evs 
                    JOIN socios s ON evs.socio_id = s.id 
                    $where 
                    ORDER BY evs.fecha DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $estadisticas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'estadisticas' => $estadisticas];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error obteniendo estadísticas: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generar reporte de verificaciones
     */
    public function generarReporteVerificaciones($fechaInicio, $fechaFin) {
        try {
            $sql = "SELECT 
                        s.nombre as nombre_socio,
                        c.nombre as nombre_comercio,
                        COUNT(od.id) as total_productos,
                        SUM(CASE WHEN od.estado_qr = 'utilizado' THEN 1 ELSE 0 END) as qr_verificados,
                        SUM(CASE WHEN od.estado_qr = 'pendiente' THEN 1 ELSE 0 END) as qr_pendientes
                    FROM socios s 
                    JOIN comercios c ON s.id = c.socio_id 
                    JOIN orden_detalles od ON c.id = od.comercio_id 
                    JOIN ordenes o ON od.orden_id = o.id 
                    WHERE o.fecha_orden BETWEEN ? AND ? 
                    GROUP BY s.id, c.id 
                    ORDER BY s.nombre, c.nombre";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin]);
            $reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'reporte' => $reporte];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error generando reporte: ' . $e->getMessage()];
        }
    }
}
?>
