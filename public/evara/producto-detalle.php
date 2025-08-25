<?php 
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php'; 
require_once __DIR__ . '/../../controllers/productoController.php'; 
require_once __DIR__ . '/../../controllers/loginController.php';

// Configurar entorno
define('ENVIRONMENT', 'development');

try {
    // Obtener ID del producto de la URL
    $productoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$productoId) {
        header('Location: index.php');
        exit;
    }

    // Obtener información del producto
    $pdo = getConnection();
    $productoController = new ProductoController($pdo);
    $producto = $productoController->obtenerPorId($productoId);

    if (!$producto) {
        header('Location: index.php');
        exit;
    }

    // Obtener todas las imágenes del producto
    $imagenes = $productoController->obtenerTodasImagenes($productoId);

    // Verificar si el usuario está autenticado (USANDO EL MÉTODO CORRECTO)
    $pdo = getConnection();
    $loginController = new LoginController($pdo);
    $usuarioAutenticado = $loginController->estaAutenticado(); // ✅ MÉTODO CORRECTO
    $usuario = null;

    if ($usuarioAutenticado) {
        $usuario = $loginController->obtenerUsuarioActual(); // ✅ MÉTODO CORRECTO
    }

    // Obtener productos relacionados
    $productosRelacionados = $productoController->obtenerProductos([
        'status' => 'activo',
        'categoria' => $producto['categoria']
    ]);

    // Filtrar el producto actual de los relacionados
    $productosRelacionados = array_filter($productosRelacionados, function($p) use ($productoId) {
        return $p['id'] != $productoId;
    });

    // Limitar a 4 productos relacionados
    $productosRelacionados = array_slice($productosRelacionados, 0, 4);

    // Debug: Verificar que las funciones existen
    if (!function_exists('getProductImageUrl')) {
        throw new Exception('La función getProductImageUrl no está disponible');
    }

} catch (Exception $e) {
    // Log del error
    error_log("Error en producto-detalle.php: " . $e->getMessage());
    
    // Mostrar error amigable
    echo "<div style='text-align: center; padding: 50px; font-family: Arial, sans-serif;'>";
    echo "<h1>Error del Sistema</h1>";
    echo "<p>Lo sentimos, ha ocurrido un error al cargar el producto.</p>";
    echo "<p><a href='index.php'>← Volver al inicio</a></p>";
    echo "<details style='margin-top: 20px; text-align: left;'>";
    echo "<summary>Detalles del error (solo para desarrolladores)</summary>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</details>";
    echo "</div>";
    exit;
}
?>

<?php require_once 'head.php'; ?>
<?php require_once 'header.php'; ?>
<?php require_once 'menu.php'; ?>

<main class="main">
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php" rel="nofollow">Inicio</a>
                <span></span> <?= htmlspecialchars($producto['categoria'] ?? 'Productos') ?>
                <span></span> <?= htmlspecialchars($producto['nombre']) ?>
            </div>
        </div>
    </div>
    
    <section class="mt-50 mb-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="product-detail accordion-detail">
                        <div class="row mb-50">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="detail-gallery">
                                    <span class="zoom-icon"><i class="fi-rs-search"></i></span>
                                    <!-- MAIN SLIDES -->
                                    <div class="product-image-slider">
                                        <?php if (!empty($imagenes)): ?>
                                            <?php foreach ($imagenes as $imagen): ?>
                                            <figure class="border-radius-10">
                                                <img src="<?= getProductImageUrl($imagen['nombre_archivo'] ?? $imagen['ruta']) ?>" 
                                                     alt="<?= htmlspecialchars($producto['nombre']) ?>">
                                            </figure>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <figure class="border-radius-10">
                                                <img src="assets/imgs/shop/default.png" alt="Imagen no disponible">
                                            </figure>
                                        <?php endif; ?>
                                    </div>
                                    <!-- THUMBNAILS -->
                                    <?php if (!empty($imagenes)): ?>
                                    <div class="slider-nav-thumbnails pl-15 pr-15">
                                        <?php foreach ($imagenes as $imagen): ?>
                                        <div>
                                            <img src="<?= getProductImageUrl($imagen['nombre_archivo'] ?? $imagen['ruta']) ?>" 
                                                 alt="<?= htmlspecialchars($producto['nombre']) ?>">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <!-- End Gallery -->
                                
                                <div class="social-icons single-share">
                                    <ul class="text-grey-5 d-inline-block">
                                        <li><strong class="mr-10">Compartir:</strong></li>
                                        <li class="social-facebook"><a href="#"><img src="assets/imgs/theme/icons/icon-facebook.svg" alt=""></a></li>
                                        <li class="social-twitter"> <a href="#"><img src="assets/imgs/theme/icons/icon-twitter.svg" alt=""></a></li>
                                        <li class="social-instagram"><a href="#"><img src="assets/imgs/theme/icons/icon-instagram.svg" alt=""></a></li>
                                        <li class="social-linkedin"><a href="#"><img src="assets/imgs/theme/icons/icon-pinterest.svg" alt=""></a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="detail-info">
                                    <h2 class="title-detail"><?= htmlspecialchars($producto['nombre']) ?></h2>
                                    
                                    <div class="product-detail-rating">
                                        <?php if (!empty($producto['marca'])): ?>
                                        <div class="pro-details-brand">
                                            <span> Marca: <a href="#"><?= htmlspecialchars($producto['marca']) ?></a></span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="product-rate-cover text-end">
                                            <div class="product-rate d-inline-block">
                                                <div class="product-rating" style="width:90%"></div>
                                            </div>
                                            <span class="font-small ml-5 text-muted"> (Sin calificaciones aún)</span>
                                        </div>
                                    </div>
                                    
                                    <div class="clearfix product-price-cover">
                                        <div class="product-price primary-color float-left">
                                            <ins><span class="text-brand">$<?= number_format($producto['precio'], 2) ?></span></ins>
                                            <?php if (!empty($producto['precio_anterior']) && $producto['precio_anterior'] > $producto['precio']): ?>
                                                <ins><span class="old-price font-md ml-15">$<?= number_format($producto['precio_anterior'], 2) ?></span></ins>
                                                <?php 
                                                $descuento = round((($producto['precio_anterior'] - $producto['precio']) / $producto['precio_anterior']) * 100);
                                                ?>
                                                <span class="save-price font-md color3 ml-15"><?= $descuento ?>% Descuento</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="bt-1 border-color-1 mt-15 mb-15"></div>
                                    
                                    <div class="short-desc mb-30">
                                        <p><?= htmlspecialchars($producto['descripcion'] ?? 'Descripción no disponible') ?></p>
                                    </div>
                                    
                                    <div class="product_sort_info font-xs mb-30">
                                        <ul>
                                            <li class="mb-10"><i class="fi-rs-crown mr-5"></i> Garantía OFM</li>
                                            <li class="mb-10"><i class="fi-rs-refresh mr-5"></i> Política de devolución</li>
                                            <li><i class="fi-rs-credit-card mr-5"></i> Pago seguro disponible</li>
                                        </ul>
                                    </div>
                                    
                                    <?php if (!empty($producto['categoria'])): ?>
                                    <div class="attr-detail attr-category mb-15">
                                        <strong class="mr-10">Categoría:</strong>
                                        <span class="text-brand"><?= htmlspecialchars($producto['categoria']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="attr-detail attr-stock mb-15">
                                        <strong class="mr-10">Stock:</strong>
                                        <?php if ($producto['stock'] > 0): ?>
                                            <span class="in-stock text-success ml-5"><?= $producto['stock'] ?> unidades disponibles</span>
                                        <?php else: ?>
                                            <span class="out-of-stock text-danger ml-5">Agotado</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (!empty($producto['codigo_producto'])): ?>
                                    <div class="attr-detail attr-sku mb-15">
                                        <strong class="mr-10">SKU:</strong>
                                        <span><?= htmlspecialchars($producto['codigo_producto']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="bt-1 border-color-1 mt-30 mb-30"></div>
                                    
                                    <div class="detail-extralink">
                                        <?php if ($producto['stock'] > 0): ?>
                                            <div class="detail-qty border radius">
                                                <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                                                <span class="qty-val">1</span>
                                                <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                                            </div>
                                            
                                            <div class="product-extra-link2">
                                                <?php if ($usuarioAutenticado): ?>
                                                    <button type="submit" class="button button-add-to-cart" onclick="agregarAlCarrito(<?= $producto['id'] ?>)">
                                                        Agregar al carrito
                                                    </button>
                                                <?php else: ?>
                                                    <a href="page-login-register.php" style="width:100%" width="100%" class="button button-add-to-cart">
                                                        Inicia sesión
                                                    </a>
                                                <?php endif; ?>
                                                <!--
                                                <a aria-label="Agregar a favoritos" class="action-btn hover-up" href="#">
                                                    <i class="fi-rs-heart"></i>
                                                </a>
                                                
                                                <a aria-label="Comparar" class="action-btn hover-up" href="#">
                                                    <i class="fi-rs-shuffle"></i>
                                                </a>-->
                                            </div>
                                        <?php else: ?>
                                            <div class="product-extra-link2">
                                                <button type="button" class="button button-add-to-cart" disabled>
                                                    Producto agotado
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <ul class="product-meta font-xs color-grey mt-50">
                                        <?php if (!empty($producto['codigo_producto'])): ?>
                                            <li class="mb-5">SKU: <a href="#"><?= htmlspecialchars($producto['codigo_producto']) ?></a></li>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($producto['categoria'])): ?>
                                            <li class="mb-5">Categoría: <a href="#" rel="tag"><?= htmlspecialchars($producto['categoria']) ?></a></li>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($producto['marca'])): ?>
                                            <li class="mb-5">Marca: <a href="#" rel="tag"><?= htmlspecialchars($producto['marca']) ?></a></li>
                                        <?php endif; ?>
                                        
                                        <li>Disponibilidad: 
                                            <?php if ($producto['stock'] > 0): ?>
                                                <span class="in-stock text-success ml-5">En stock</span>
                                            <?php else: ?>
                                                <span class="out-of-stock text-danger ml-5">Agotado</span>
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </div>
                                <!-- Detail Info -->
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-10 m-auto entry-main-content">
                                <h2 class="section-title style-1 mb-30">Descripción</h2>
                                <div class="description mb-50">
                                    <p><?= htmlspecialchars($producto['descripcion'] ?? 'Descripción detallada no disponible para este producto.') ?></p>
                                    
                                    <?php if (!empty($producto['peso']) || !empty($producto['dimensiones'])): ?>
                                    <h4 class="mt-30">Especificaciones</h4>
                                    <hr class="wp-block-separator is-style-wide">
                                    <ul class="product-more-infor mt-30">
                                        <?php if (!empty($producto['peso'])): ?>
                                            <li><span>Peso</span> <?= htmlspecialchars($producto['peso']) ?></li>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($producto['dimensiones'])): ?>
                                            <li><span>Dimensiones</span> <?= htmlspecialchars($producto['dimensiones']) ?></li>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($producto['categoria'])): ?>
                                            <li><span>Categoría</span> <?= htmlspecialchars($producto['categoria']) ?></li>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($producto['marca'])): ?>
                                            <li><span>Marca</span> <?= htmlspecialchars($producto['marca']) ?></li>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($producto['codigo_producto'])): ?>
                                            <li><span>Código</span> <?= htmlspecialchars($producto['codigo_producto']) ?></li>
                                        <?php endif; ?>
                                    </ul>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="social-icons single-share">
                                    <ul class="text-grey-5 d-inline-block">
                                        <li><strong class="mr-10">Compartir este producto:</strong></li>
                                        <li class="social-facebook"><a href="#"><img src="assets/imgs/theme/icons/icon-facebook.svg" alt=""></a></li>
                                        <li class="social-twitter"> <a href="#"><img src="assets/imgs/theme/icons/icon-twitter.svg" alt=""></a></li>
                                        <li class="social-instagram"><a href="#"><img src="assets/imgs/theme/icons/icon-instagram.svg" alt=""></a></li>
                                        <li class="social-linkedin"><a href="#"><img src="assets/imgs/theme/icons/icon-pinterest.svg" alt=""></a></li>
                                    </ul>
                                </div>

                                <?php /*
                                <h3 class="section-title style-1 mb-30 mt-30">Comentarios (0)</h3>
                                <!--Comments-->
                                <div class="comments-area style-2">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <h4 class="mb-30">Preguntas y respuestas del cliente</h4>
                                            <div class="comment-list">
                                                <div class="text-center py-5">
                                                    <i class="fi-rs-comment" style="font-size: 3rem; color: #ccc;"></i>
                                                    <h5 class="mt-3">No hay comentarios aún</h5>
                                                    <p class="text-muted">Sé el primero en comentar sobre este producto</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <h4 class="mb-30">Calificaciones del cliente</h4>
                                            <div class="d-flex mb-30">
                                                <div class="product-rate d-inline-block mr-15">
                                                    <div class="product-rating" style="width:0%"></div>
                                                </div>
                                                <h6>0.0 de 5</h6>
                                            </div>
                                            <p class="text-muted">No hay calificaciones aún para este producto</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!--comment form-->
                                <?php if ($usuarioAutenticado): ?>
                                <div class="comment-form">
                                    <h4 class="mb-15">Agregar un comentario</h4>
                                    <div class="row">
                                        <div class="col-lg-8 col-md-12">
                                            <form class="form-contact comment_form" action="#" id="commentForm">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <textarea class="form-control w-100" name="comment" id="comment" cols="30" rows="9" placeholder="Escribe tu comentario"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="button button-contactForm">Enviar comentario</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="comment-form">
                                    <h4 class="mb-15">Agregar un comentario</h4>
                                    <div class="text-center py-4">
                                        <p class="mb-3">Debes <a href="page-login-register.php" class="text-brand">iniciar sesión</a> para poder comentar</p>
                                        <a href="page-login-register.php" class="button button-contactForm">Iniciar sesión</a>
                                    </div>
                                </div>
                                <?php endif; */ ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($productosRelacionados)): ?>
                        <div class="row mt-60">
                            <div class="col-12">
                                <h3 class="section-title style-1 mb-30">Productos relacionados</h3>
                            </div>
                            <div class="col-12">
                                <div class="row related-products">
                                    <?php foreach ($productosRelacionados as $p): ?>
                                    <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                                        <div class="product-cart-wrap small hover-up">
                                            <div class="product-img-action-wrap">
                                                <div class="product-img product-img-zoom">
                                                    <a href="producto-detalle.php?id=<?= $p['id'] ?>" tabindex="0">
                                                        <?php if (!empty($p['imagen_principal'])): ?>
                                                            <img class="default-img" src="<?= getProductImageUrl($p['imagen_principal']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                                                        <?php else: ?>
                                                            <img class="default-img" src="assets/imgs/shop/default.png" alt="Imagen no disponible">
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                                <div class="product-action-1">
                                                    <a aria-label="Vista rápida" class="action-btn small hover-up" href="producto-detalle.php?id=<?= $p['id'] ?>">
                                                        <i class="fi-rs-search"></i>
                                                    </a>
                                                    <a aria-label="Agregar a favoritos" class="action-btn small hover-up" href="#">
                                                        <i class="fi-rs-heart"></i>
                                                    </a>
                                                    <a aria-label="Comparar" class="action-btn small hover-up" href="#">
                                                        <i class="fi-rs-shuffle"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="product-content-wrap">
                                                <h2><a href="producto-detalle.php?id=<?= $p['id'] ?>" tabindex="0"><?= htmlspecialchars($p['nombre']) ?></a></h2>
                                                <div class="product-price">
                                                    <span>$<?= number_format($p['precio'], 2) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>

<!-- Preloader Start -->
<div id="preloader-active">
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="text-center">
            <h5 class="mb-5">Cargando...</h5>
            <div class="loader">
                <div class="bar bar1"></div>
                <div class="bar bar2"></div>
                <div class="bar bar3"></div>
            </div>
        </div>
    </div>
</div>

<script>
function agregarAlCarrito(productoId) {
    // Obtener cantidad seleccionada
    const cantidad = parseInt(document.querySelector('.qty-val').textContent);
    
    // Mostrar indicador de carga
    const boton = event.target;
    const textoOriginal = boton.textContent;
    boton.textContent = 'Agregando...';
    boton.disabled = true;
    
    // Realizar llamada AJAX
    fetch('ajax/agregar-al-carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            producto_id: productoId,
            cantidad: cantidad
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            mostrarNotificacion(data.message, 'success');
            
            // Actualizar contador del carrito en el header si existe
            const contadorCarrito = document.querySelector('.pro-count');
            if (contadorCarrito && data.carrito) {
                contadorCarrito.textContent = data.carrito.cantidad_productos;
            }
            
            // Actualizar el dropdown del carrito en el header
            if (typeof cargarCarritoDropdown === 'function') {
                cargarCarritoDropdown();
            }
            
            // Opcional: Redirigir al carrito o mostrar modal
            setTimeout(() => {
                if (confirm('¿Deseas ver tu carrito de compras?')) {
                    window.location.href = 'carrito.php';
                }
            }, 1000);
            
        } else {
            mostrarNotificacion(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al agregar al carrito', 'error');
    })
    .finally(() => {
        // Restaurar botón
        boton.textContent = textoOriginal;
        boton.disabled = false;
    });
}

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

// Actualizar cantidad
document.addEventListener('DOMContentLoaded', function() {
    const qtyDown = document.querySelector('.qty-down');
    const qtyUp = document.querySelector('.qty-up');
    const qtyVal = document.querySelector('.qty-val');
    
    if (qtyDown && qtyUp && qtyVal) {
        qtyDown.addEventListener('click', function(e) {
            e.preventDefault();
            let currentQty = parseInt(qtyVal.textContent);
            if (currentQty > 1) {
                qtyVal.textContent = currentQty - 1;
            }
        });
        
        qtyUp.addEventListener('click', function(e) {
            e.preventDefault();
            let currentQty = parseInt(qtyVal.textContent);
            qtyVal.textContent = currentQty + 1;
        });
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
});
</script>

<?php require_once 'foot.php'; ?>
