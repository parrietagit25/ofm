<?php

require_once __DIR__ . '/carritoController.php';
require_once __DIR__ . '/loginController.php';

class CheckoutController {
    private $pdo;
    private $carritoController;
    private $loginController;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->carritoController = new CarritoController($pdo);
        $this->loginController = new LoginController($pdo);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Procesar el checkout completo
     */
    public function procesarCheckout($datosCheckout) {
        try {
            error_log("DEBUG: Iniciando procesarCheckout con datos: " . json_encode($datosCheckout));
            
            // Verificar que el usuario esté autenticado
            if (!$this->loginController->estaAutenticado()) {
                error_log("DEBUG: Usuario no autenticado");
                return ['success' => false, 'message' => 'Debes iniciar sesión para continuar'];
            }
            error_log("DEBUG: Usuario autenticado correctamente");
            
            // Verificar que el carrito no esté vacío
            if ($this->carritoController->carritoVacio()) {
                error_log("DEBUG: Carrito vacío");
                return ['success' => false, 'message' => 'El carrito está vacío'];
            }
            error_log("DEBUG: Carrito no está vacío");
            
            // Validar datos del checkout
            $validacion = $this->validarDatosCheckout($datosCheckout);
            if (!$validacion['success']) {
                error_log("DEBUG: Validación falló: " . $validacion['message']);
                return $validacion;
            }
            error_log("DEBUG: Validación exitosa");
            
            // Iniciar transacción
            error_log("DEBUG: Iniciando transacción de BD");
            $this->pdo->beginTransaction();
            
            try {
                // 1. Crear la orden principal
                error_log("DEBUG: Creando orden principal");
                $orden = $this->crearOrden($datosCheckout);
                if (!$orden['success']) {
                    throw new Exception($orden['message']);
                }
                
                $ordenId = $orden['orden_id'];
                $numeroOrden = $orden['numero_orden'];
                error_log("DEBUG: Orden creada - ID: $ordenId, Número: $numeroOrden");
                
                // 2. Crear detalles de orden y generar QR para cada producto
                error_log("DEBUG: Creando detalles de orden");
                $detalles = $this->crearDetallesOrden($ordenId);
                if (!$detalles['success']) {
                    throw new Exception($detalles['message']);
                }
                error_log("DEBUG: Detalles de orden creados - Cantidad QR: " . $detalles['cantidad_qr']);
                
                // 3. Crear transacción de pago
                error_log("DEBUG: Creando transacción de pago");
                $transaccion = $this->crearTransaccion($ordenId, $datosCheckout);
                if (!$transaccion['success']) {
                    throw new Exception($transaccion['message']);
                }
                error_log("DEBUG: Transacción de pago creada");
                
                // 4. Calcular y registrar comisiones para socios
                error_log("DEBUG: Calculando comisiones");
                $comisiones = $this->calcularComisiones($detalles['detalles_ids']);
                if (!$comisiones['success']) {
                    throw new Exception($comisiones['message']);
                }
                error_log("DEBUG: Comisiones calculadas");
                
                // 5. Limpiar carrito
                error_log("DEBUG: Limpiando carrito");
                $this->carritoController->limpiarCarrito();
                
                // 6. Crear notificaciones para socios
                error_log("DEBUG: Creando notificaciones");
                $this->crearNotificacionesSocios($detalles['detalles_ids']);
                
                // 7. Actualizar estadísticas
                error_log("DEBUG: Actualizando estadísticas");
                $this->actualizarEstadisticasSocios($detalles['detalles_ids']);
                
                // Confirmar transacción
                error_log("DEBUG: Confirmando transacción");
                $this->pdo->commit();
                
                error_log("DEBUG: Checkout completado exitosamente");
                return [
                    'success' => true,
                    'message' => 'Orden procesada exitosamente',
                    'orden_id' => $ordenId,
                    'numero_orden' => $numeroOrden,
                    'total' => $datosCheckout['total'],
                    'qr_generados' => $detalles['cantidad_qr']
                ];
                
            } catch (Exception $e) {
                error_log("DEBUG: Error en transacción - Rollback: " . $e->getMessage());
                $this->pdo->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("DEBUG: Error general en checkout: " . $e->getMessage());
            error_log("DEBUG: Stack trace: " . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Error al procesar el checkout: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validar datos del checkout
     */
    private function validarDatosCheckout($datos) {
        $errores = [];
        
        if (empty($datos['metodo_pago'])) {
            $errores[] = 'El método de pago es requerido';
        }
        
        if (empty($datos['total']) || $datos['total'] <= 0) {
            $errores[] = 'El total de la orden es inválido';
        }
        
        if (!empty($errores)) {
            return ['success' => false, 'message' => implode(', ', $errores)];
        }
        
        return ['success' => true];
    }
    
    /**
     * Crear la orden principal
     */
    private function crearOrden($datos) {
        try {
            $usuario = $this->loginController->obtenerUsuarioActual();
            $numeroOrden = $this->generarNumeroOrden();
            
            $sql = "INSERT INTO ordenes (
                usuario_id, numero_orden, total, subtotal, impuestos, envio, 
                metodo_pago, notas
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $usuario['id'],
                $numeroOrden,
                $datos['total'],
                $datos['subtotal'] ?? $datos['total'],
                $datos['impuestos'] ?? 0.00,
                $datos['envio'] ?? 0.00,
                $datos['metodo_pago'],
                $datos['notas'] ?? ''
            ]);
            
            $ordenId = $this->pdo->lastInsertId();
            
            return [
                'success' => true,
                'orden_id' => $ordenId,
                'numero_orden' => $numeroOrden
            ];
            
        } catch (Exception $e) {
            throw new Exception('Error creando orden: ' . $e->getMessage());
        }
    }
    
    /**
     * Crear detalles de orden y generar QR
     * NOTA: Los códigos QR se almacenan en orden_detalles.codigo_qr
     * La tabla qr_verificaciones se llenará cuando se verifiquen los QR
     * IMPORTANTE: Se genera 1 QR por cada unidad del producto
     */
    private function crearDetallesOrden($ordenId) {
        try {
            $carrito = $this->carritoController->obtenerCarritoDetallado();
            $detallesIds = [];
            $cantidadQr = 0;
            
            foreach ($carrito as $item) {
                // Obtener información del comercio y socio
                $infoComercio = $this->obtenerInfoComercioProducto($item['producto_id']);
                
                // Generar 1 QR por cada unidad del producto
                for ($i = 1; $i <= $item['cantidad']; $i++) {
                    // Generar QR único para esta unidad específica
                    $codigoQr = $this->generarCodigoQR(
                        $infoComercio['comercio_id'], 
                        $infoComercio['socio_id'], 
                        $item['producto_id'], 
                        $ordenId,
                        $i // Número de unidad
                    );
                    
                    $sql = "INSERT INTO orden_detalles (
                        orden_id, producto_id, comercio_id, socio_id, cantidad,
                        precio_unitario, precio_total, nombre_producto, codigo_qr, unidad_numero
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        $ordenId,
                        $item['producto_id'],
                        $infoComercio['comercio_id'],
                        $infoComercio['socio_id'],
                        1, // Cantidad = 1 por cada unidad
                        $item['precio'],
                        $item['precio'], // Precio unitario
                        $item['nombre'],
                        $codigoQr,
                        $i // Número de unidad (1, 2, 3, etc.)
                    ]);
                    
                    $detalleId = $this->pdo->lastInsertId();
                    $detallesIds[] = $detalleId;
                    $cantidadQr++;
                }
                
                // Actualizar stock del producto
                $this->actualizarStockProducto($item['producto_id'], $item['cantidad']);
            }
            
            return [
                'success' => true,
                'detalles_ids' => $detallesIds,
                'cantidad_qr' => $cantidadQr
            ];
            
        } catch (Exception $e) {
            throw new Exception('Error creando detalles de orden: ' . $e->getMessage());
        }
    }
    
    /**
     * Crear transacción de pago
     */
    private function crearTransaccion($ordenId, $datos) {
        try {
            $numeroTransaccion = $this->generarNumeroTransaccion();
            
            $sql = "INSERT INTO transacciones (
                orden_id, numero_transaccion, monto, metodo_pago, estado
            ) VALUES (?, ?, ?, ?, 'completada')";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $ordenId,
                $numeroTransaccion,
                $datos['total'],
                $datos['metodo_pago']
            ]);
            
            return ['success' => true, 'transaccion_id' => $this->pdo->lastInsertId()];
            
        } catch (Exception $e) {
            throw new Exception('Error creando transacción: ' . $e->getMessage());
        }
    }
    
    /**
     * Calcular y registrar comisiones para socios
     */
    private function calcularComisiones($detallesIds) {
        try {
            foreach ($detallesIds as $detalleId) {
                // Obtener información del detalle
                $sql = "SELECT od.*, p.categoria, c.porcentaje_comision 
                        FROM orden_detalles od 
                        JOIN productos p ON od.producto_id = p.id 
                        JOIN configuracion_comisiones c ON p.categoria = c.tipo_producto 
                        WHERE od.id = ? AND c.activo = 1";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$detalleId]);
                $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($detalle) {
                    $montoComision = ($detalle['precio_total'] * $detalle['porcentaje_comision']) / 100;
                    
                    $sqlComision = "INSERT INTO comisiones_socios (
                        socio_id, orden_detalle_id, monto_comision, porcentaje_comision
                    ) VALUES (?, ?, ?, ?)";
                    
                    $stmtComision = $this->pdo->prepare($sqlComision);
                    $stmtComision->execute([
                        $detalle['socio_id'],
                        $detalleId,
                        $montoComision,
                        $detalle['porcentaje_comision']
                    ]);
                }
            }
            
            return ['success' => true];
            
        } catch (Exception $e) {
            throw new Exception('Error calculando comisiones: ' . $e->getMessage());
        }
    }
    
    /**
     * Crear notificaciones para socios
     */
    private function crearNotificacionesSocios($detallesIds) {
        try {
            foreach ($detallesIds as $detalleId) {
                $sql = "SELECT od.socio_id, od.nombre_producto, o.numero_orden 
                        FROM orden_detalles od 
                        JOIN ordenes o ON od.orden_id = o.id 
                        WHERE od.id = ?";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$detalleId]);
                $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($detalle) {
                    $sqlNotif = "INSERT INTO notificaciones_socios (
                        socio_id, tipo, titulo, mensaje
                    ) VALUES (?, 'nueva_venta', ?, ?)";
                    
                    $stmtNotif = $this->pdo->prepare($sqlNotif);
                    $stmtNotif->execute([
                        $detalle['socio_id'],
                        'Nueva venta realizada',
                        "Se ha vendido {$detalle['nombre_producto']} en la orden {$detalle['numero_orden']}"
                    ]);
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error creando notificaciones: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar estadísticas de socios
     */
    private function actualizarEstadisticasSocios($detallesIds) {
        try {
            foreach ($detallesIds as $detalleId) {
                $sql = "SELECT od.socio_id, od.precio_total, od.cantidad 
                        FROM orden_detalles od 
                        WHERE od.id = ?";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$detalleId]);
                $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($detalle) {
                    $fecha = date('Y-m-d');
                    
                    // Verificar si ya existe estadística para hoy
                    $sqlCheck = "SELECT id FROM estadisticas_ventas_socios 
                                WHERE socio_id = ? AND fecha = ?";
                    $stmtCheck = $this->pdo->prepare($sqlCheck);
                    $stmtCheck->execute([$detalle['socio_id'], $fecha]);
                    
                    if ($stmtCheck->fetch()) {
                        // Actualizar estadística existente
                        $sqlUpdate = "UPDATE estadisticas_ventas_socios 
                                     SET total_ventas = total_ventas + ?, 
                                         cantidad_productos = cantidad_productos + ? 
                                     WHERE socio_id = ? AND fecha = ?";
                        $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                        $stmtUpdate->execute([
                            $detalle['precio_total'],
                            $detalle['cantidad'],
                            $detalle['socio_id'],
                            $fecha
                        ]);
                    } else {
                        // Crear nueva estadística
                        $sqlInsert = "INSERT INTO estadisticas_ventas_socios (
                            socio_id, fecha, total_ventas, cantidad_productos
                        ) VALUES (?, ?, ?, ?)";
                        $stmtInsert = $this->pdo->prepare($sqlInsert);
                        $stmtInsert->execute([
                            $detalle['socio_id'],
                            $fecha,
                            $detalle['precio_total'],
                            $detalle['cantidad']
                        ]);
                    }
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error actualizando estadísticas: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generar número de orden único
     */
    private function generarNumeroOrden() {
        $prefijo = 'OFM';
        $fecha = date('Ymd');
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        
        return $prefijo . $fecha . $timestamp . $random;
    }
    
    /**
     * Generar número de transacción único
     */
    private function generarNumeroTransaccion() {
        $prefijo = 'TXN';
        $fecha = date('YmdHis');
        $random = mt_rand(100000, 999999);
        
        return $prefijo . $fecha . $random;
    }
    
    /**
     * Generar código QR único
     * Ahora incluye el número de unidad para hacer cada QR único
     */
    private function generarCodigoQR($comercioId, $socioId, $productoId, $ordenId, $unidadNumero = 1) {
        $datos = [
            'comercio_id' => $comercioId,
            'socio_id' => $socioId,
            'producto_id' => $productoId,
            'orden_id' => $ordenId,
            'unidad_numero' => $unidadNumero,
            'timestamp' => time(),
            'random' => mt_rand(100000, 999999)
        ];
        
        $json = json_encode($datos);
        $hash = hash('sha256', $json);
        
        return 'QR_' . substr($hash, 0, 16) . '_' . time() . '_U' . $unidadNumero;
    }
    
    /**
     * Obtener información del comercio y socio de un producto
     */
    private function obtenerInfoComercioProducto($productoId) {
        // Para simplificar, usamos IDs fijos ya que la tabla productos no tiene comercio_id ni socio_id
        // En un sistema real, estos IDs vendrían de la tabla productos
        return [
            'producto_id' => $productoId,
            'comercio_id' => 1, // Usar el primer comercio disponible
            'socio_id' => 1     // Usar el primer socio disponible
        ];
    }
    
    /**
     * Actualizar stock del producto
     */
    private function actualizarStockProducto($productoId, $cantidad) {
        $sql = "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cantidad, $productoId, $cantidad]);
        
        if ($stmt->rowCount() == 0) {
            throw new Exception("Stock insuficiente para el producto $productoId");
        }
    }
    
    /**
     * Obtener orden por ID
     */
    public function obtenerOrden($ordenId) {
        try {
            $sql = "SELECT o.*, u.nombre as nombre_usuario, u.email 
                    FROM ordenes o 
                    JOIN usuarios u ON o.usuario_id = u.id 
                    WHERE o.id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$ordenId]);
            $orden = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$orden) {
                return ['success' => false, 'message' => 'Orden no encontrada'];
            }
            
            // Obtener detalles de la orden
            $sqlDetalles = "SELECT od.*, p.nombre as nombre_producto 
                           FROM orden_detalles od 
                           JOIN productos p ON od.producto_id = p.id 
                           WHERE od.orden_id = ?";
            
            $stmtDetalles = $this->pdo->prepare($sqlDetalles);
            $stmtDetalles->execute([$ordenId]);
            $detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);
            
            $orden['detalles'] = $detalles;
            
            return ['success' => true, 'orden' => $orden];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error obteniendo orden: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener todas las órdenes de un usuario
     */
    public function obtenerOrdenesUsuario($usuarioId) {
        try {
            $sql = "SELECT o.*, COUNT(od.id) as cantidad_productos 
                    FROM ordenes o 
                    LEFT JOIN orden_detalles od ON o.id = od.orden_id 
                    WHERE o.usuario_id = ? 
                    GROUP BY o.id 
                    ORDER BY o.fecha_orden DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuarioId]);
            $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'ordenes' => $ordenes];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error obteniendo órdenes: ' . $e->getMessage()];
        }
    }
}
?>
