<?php 
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php'; 
require_once __DIR__ . '/../../controllers/qrVerificationController.php';
require_once __DIR__ . '/../../controllers/loginController.php';

// Verificar autenticación
$pdo = getConnection();
$loginController = new LoginController($pdo);

if (!$loginController->estaAutenticado()) {
    header('Location: page-login-register.php');
    exit;
}

$usuario = $loginController->obtenerUsuarioActual();
$mensaje = '';
$tipoMensaje = '';
$resultadoVerificacion = null;
?>

<?php require_once 'head.php'; ?>
<?php require_once 'header.php'; ?>
<?php require_once 'menu.php'; ?>

<main class="main">
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php" rel="nofollow">Inicio</a>
                <span></span> Verificar QR
            </div>
        </div>
    </div>
    
    <section class="mt-50 mb-50">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="qr-verification-container">
                        <div class="text-center mb-50">
                            <div class="qr-icon mb-4">
                                <i class="fi-rs-qr-code" style="font-size: 4rem; color: #007bff;"></i>
                            </div>
                            <h1 class="mb-3">Verificar Código QR</h1>
                            <p class="text-muted fs-5">Escanea o ingresa el código QR del producto para verificar la entrega</p>
                        </div>
                        
                        <!-- Formulario de verificación -->
                        <div class="verification-form">
                            <form id="qrVerificationForm" method="POST">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="codigo_qr">Código QR *</label>
                                            <input type="text" id="codigo_qr" name="codigo_qr" class="form-control form-control-lg" 
                                                   placeholder="Ingresa o escanea el código QR" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                                <i class="fi-rs-search mr-2"></i>Verificar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ubicacion">Ubicación de verificación</label>
                                            <input type="text" id="ubicacion" name="ubicacion" class="form-control" 
                                                   placeholder="Ej: Panamá, San Miguelito">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="dispositivo">Dispositivo</label>
                                            <input type="text" id="dispositivo" name="dispositivo" class="form-control" 
                                                   placeholder="Ej: Móvil, Tablet, PC">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label for="notas">Notas adicionales</label>
                                    <textarea id="notas" name="notas" class="form-control" rows="2" 
                                              placeholder="Información adicional sobre la verificación (opcional)"></textarea>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Resultado de la verificación -->
                        <?php if ($resultadoVerificacion): ?>
                        <div class="verification-result mt-4">
                            <?php if ($resultadoVerificacion['success']): ?>
                                <div class="alert alert-success">
                                    <div class="d-flex align-items-center">
                                        <i class="fi-rs-check-circle mr-3" style="font-size: 2rem;"></i>
                                        <div>
                                            <h5 class="mb-1">¡Verificación Exitosa!</h5>
                                            <p class="mb-0"><?= htmlspecialchars($resultadoVerificacion['message']) ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (isset($resultadoVerificacion['producto'])): ?>
                                <div class="product-info mt-4">
                                    <h5>Información del Producto</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <strong>Producto:</strong>
                                                <span><?= htmlspecialchars($resultadoVerificacion['producto']['nombre']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <strong>Comercio:</strong>
                                                <span><?= htmlspecialchars($resultadoVerificacion['producto']['comercio']) ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <strong>Socio:</strong>
                                                <span><?= htmlspecialchars($resultadoVerificacion['producto']['socio']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <strong>Orden:</strong>
                                                <span><?= htmlspecialchars($resultadoVerificacion['producto']['orden']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item mt-2">
                                        <strong>Fecha de orden:</strong>
                                        <span><?= date('d/m/Y H:i', strtotime($resultadoVerificacion['producto']['fecha_orden'])) ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <div class="d-flex align-items-center">
                                        <i class="fi-rs-cross-circle mr-3" style="font-size: 2rem;"></i>
                                        <div>
                                            <h5 class="mb-1">Error en la Verificación</h5>
                                            <p class="mb-0"><?= htmlspecialchars($resultadoVerificacion['message']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Información adicional -->
                        <div class="verification-info mt-5">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon">
                                            <i class="fi-rs-info"></i>
                                        </div>
                                        <h6>¿Cómo funciona?</h6>
                                        <p>Escanea o ingresa el código QR que recibiste con tu producto para confirmar la entrega exitosa.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon">
                                            <i class="fi-rs-shield-check"></i>
                                        </div>
                                        <h6>Seguridad</h6>
                                        <p>Cada código QR es único y solo puede ser utilizado una vez para garantizar la autenticidad.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Historial de verificaciones -->
                        <div class="verification-history mt-5">
                            <h4 class="mb-4">Mis Verificaciones Recientes</h4>
                            <div id="verificationHistory">
                                <div class="text-center py-4">
                                    <i class="fi-rs-clock" style="font-size: 3rem; color: #ccc;"></i>
                                    <h6 class="mt-3">No hay verificaciones recientes</h6>
                                    <p class="text-muted">Las verificaciones que realices aparecerán aquí</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>

<style>
.qr-verification-container {
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0,0,0,0.1);
}

.qr-icon {
    animation: pulse 2s infinite;
}

.verification-form {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    padding: 15px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.4);
}

.verification-result {
    border-radius: 10px;
    overflow: hidden;
}

.product-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #28a745;
}

.info-item {
    margin-bottom: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item strong {
    display: inline-block;
    width: 120px;
    color: #495057;
}

.info-card {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    height: 100%;
    border: 1px solid #e9ecef;
}

.info-icon {
    font-size: 2.5rem;
    color: #007bff;
    margin-bottom: 15px;
}

.info-card h6 {
    color: #333;
    margin-bottom: 15px;
}

.info-card p {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.verification-history {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.alert {
    border: none;
    border-radius: 10px;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
}
</style>

<script>
document.getElementById('qrVerificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const datosVerificacion = {
        codigo_qr: formData.get('codigo_qr'),
        ubicacion: formData.get('ubicacion'),
        dispositivo: formData.get('dispositivo'),
        notas: formData.get('notas')
    };
    
    // Validar código QR
    if (!datosVerificacion.codigo_qr.trim()) {
        mostrarNotificacion('Debes ingresar un código QR', 'error');
        return;
    }
    
    // Mostrar indicador de carga
    const btnVerificar = document.querySelector('button[type="submit"]');
    const textoOriginal = btnVerificar.innerHTML;
    btnVerificar.innerHTML = '<i class="fi-rs-spinner mr-2 fa-spin"></i>Verificando...';
    btnVerificar.disabled = true;
    
    // Realizar verificación
    fetch('ajax/verificar-qr.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datosVerificacion)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            // Recargar la página para mostrar el resultado
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            mostrarNotificacion(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al verificar el QR', 'error');
    })
    .finally(() => {
        // Restaurar botón
        btnVerificar.innerHTML = textoOriginal;
        btnVerificar.disabled = false;
    });
});

function mostrarNotificacion(mensaje, tipo) {
    // Crear notificación
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion notificacion-${tipo}`;
    notificacion.innerHTML = `
        <div class="notificacion-contenido">
            <span class="notificacion-mensaje">${mensaje}</span>
            <button class="notificacion-cerrar" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    // Agregar estilos
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
    `;
    
    if (tipo === 'success') {
        notificacion.style.backgroundColor = '#28a745';
    } else {
        notificacion.style.backgroundColor = '#dc3545';
    }
    
    // Agregar al DOM
    document.body.appendChild(notificacion);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentElement) {
            notificacion.remove();
        }
    }, 5000);
}

// Agregar estilos CSS para las notificaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .notificacion-contenido {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .notificacion-cerrar {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        margin-left: 15px;
    }
    
    .notificacion-cerrar:hover {
        opacity: 0.8;
    }
`;
document.head.appendChild(style);

// Cargar historial de verificaciones al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    cargarHistorialVerificaciones();
});

function cargarHistorialVerificaciones() {
    fetch('ajax/obtener-historial-verificaciones.php')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.verificaciones.length > 0) {
            mostrarHistorialVerificaciones(data.verificaciones);
        }
    })
    .catch(error => {
        console.error('Error cargando historial:', error);
    });
}

function mostrarHistorialVerificaciones(verificaciones) {
    const container = document.getElementById('verificationHistory');
    
    let html = '<div class="table-responsive"><table class="table table-hover">';
    html += '<thead><tr><th>Fecha</th><th>Producto</th><th>Comercio</th><th>Estado</th></tr></thead><tbody>';
    
    verificaciones.forEach(verificacion => {
        const fecha = new Date(verificacion.fecha_verificacion).toLocaleDateString('es-ES');
        html += `<tr>
            <td>${fecha}</td>
            <td>${verificacion.nombre_producto || 'N/A'}</td>
            <td>${verificacion.nombre_comercio || 'N/A'}</td>
            <td><span class="badge bg-success">Verificado</span></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}
</script>

<?php require_once 'foot.php'; ?>
