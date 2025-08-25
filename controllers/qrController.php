<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class QRController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Generar código QR como imagen PNG
     */
    public function generarQRImagen($codigoQR, $tamaño = 300) {
        try {
            // Crear código QR
            $qrCode = QrCode::create($codigoQR)
                ->setSize($tamaño)
                ->setMargin(10)
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));
            
            // Crear writer PNG
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            // Obtener imagen como string
            $imageData = $result->getString();
            
            return [
                'success' => true,
                'image_data' => $imageData,
                'mime_type' => 'image/png'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error generando QR: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Generar código QR y guardarlo como archivo
     */
    public function generarQRArchivo($codigoQR, $rutaDestino, $tamaño = 300) {
        try {
            $resultado = $this->generarQRImagen($codigoQR, $tamaño);
            
            if (!$resultado['success']) {
                return $resultado;
            }
            
            // Guardar archivo
            if (file_put_contents($rutaDestino, $resultado['image_data']) === false) {
                return [
                    'success' => false,
                    'message' => 'Error guardando archivo QR'
                ];
            }
            
            return [
                'success' => true,
                'ruta_archivo' => $rutaDestino
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error generando archivo QR: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener todos los códigos QR de una orden
     */
    public function obtenerQRsOrden($ordenId) {
        try {
            $sql = "SELECT od.*, p.nombre as nombre_producto 
                    FROM orden_detalles od 
                    JOIN productos p ON od.producto_id = p.id 
                    WHERE od.orden_id = ? 
                    ORDER BY od.producto_id, od.unidad_numero";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$ordenId]);
            $qrs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'qrs' => $qrs
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error obteniendo QRs: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Verificar si un código QR existe y obtener su información
     */
    public function verificarQR($codigoQR) {
        try {
            $sql = "SELECT od.*, o.numero_orden, o.fecha_orden, p.nombre as nombre_producto,
                           u.nombre as nombre_usuario, u.email
                    FROM orden_detalles od 
                    JOIN ordenes o ON od.orden_id = o.id 
                    JOIN productos p ON od.producto_id = p.id 
                    JOIN usuarios u ON o.usuario_id = u.id 
                    WHERE od.codigo_qr = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$codigoQR]);
            $qr = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$qr) {
                return [
                    'success' => false,
                    'message' => 'Código QR no encontrado'
                ];
            }
            
            return [
                'success' => true,
                'qr' => $qr
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error verificando QR: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Marcar un QR como verificado
     */
    public function marcarQRVerificado($codigoQR, $verificadoPor, $resultado = 'verificado') {
        try {
            // Primero verificar que el QR existe
            $verificacion = $this->verificarQR($codigoQR);
            if (!$verificacion['success']) {
                return $verificacion;
            }
            
            $qr = $verificacion['qr'];
            
            // Insertar en qr_verificaciones
            $sql = "INSERT INTO qr_verificaciones (
                orden_detalle_id, codigo_qr, verificado_por, resultado, fecha_verificacion
            ) VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $qr['id'],
                $codigoQR,
                $verificadoPor,
                $resultado
            ]);
            
            return [
                'success' => true,
                'message' => 'QR marcado como verificado',
                'verificacion_id' => $this->pdo->lastInsertId()
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error marcando QR como verificado: ' . $e->getMessage()
            ];
        }
    }
}
?>
