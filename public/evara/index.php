<?php require_once __DIR__ . '/../../includes/db.php'; ?>
<?php require_once __DIR__ . '/../../controllers/productoController.php'; ?>

<?php
// Obtener productos para mostrar en la página pública
$productoController = new ProductoController($pdo);
$resultado = $productoController->obtenerTodos(1, 50, ['status' => 'activo']); // Solo productos activos
$productos = $resultado['productos'] ?? [];
?>

<?php require_once 'head.php'; ?>
<?php require_once 'header.php'; ?>
<?php require_once 'menu.php'; ?>

<main class="main">
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.html" rel="nofollow">Home</a>
                <span></span> Shop
            </div>
        </div>
    </div>
    <section class="mt-50 mb-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shop-product-fillter">
                        <div class="totall-product">
                            <p> We found <strong class="text-brand"><?= count($productos) ?></strong> items for you!</p>
                        </div>
                        <div class="sort-by-product-area">
                            <div class="sort-by-cover mr-10">
                                <div class="sort-by-product-wrap">
                                    <div class="sort-by">
                                        <span><i class="fi-rs-apps"></i>Show:</span>
                                    </div>
                                    <div class="sort-by-dropdown-wrap">
                                        <span> 50 <i class="fi-rs-angle-small-down"></i></span>
                                    </div>
                                </div>
                                <div class="sort-by-dropdown">
                                    <ul>
                                        <li><a class="active" href="#">50</a></li>
                                        <li><a href="#">100</a></li>
                                        <li><a href="#">150</a></li>
                                        <li><a href="#">200</a></li>
                                        <li><a href="#">All</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="sort-by-cover">
                                <div class="sort-by-product-wrap">
                                    <div class="sort-by">
                                        <span><i class="fi-rs-apps-sort"></i>Sort by:</span>
                                    </div>
                                    <div class="sort-by-dropdown-wrap">
                                        <span> Featured <i class="fi-rs-angle-small-down"></i></span>
                                    </div>
                                </div>
                                <div class="sort-by-dropdown">
                                    <ul>
                                        <li><a class="active" href="#">Featured</a></li>
                                        <li><a href="#">Price: Low to High</a></li>
                                        <li><a href="#">Price: High to Low</a></li>
                                        <li><a href="#">Release Date</a></li>
                                        <li><a href="#">Avg. Rating</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (empty($productos)): ?>
                        <!-- Mensaje cuando no hay productos -->
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fi-rs-box" style="font-size: 4rem; color: #ccc;"></i>
                                    <h3 class="mt-3">No hay productos disponibles</h3>
                                    <p class="text-muted">Por el momento no tenemos productos para mostrar. ¡Vuelve pronto!</p>
                                    <a href="#" class="btn btn-primary">Ver más tarde</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Grid de productos -->
                        <div class="row product-grid-4">
                            <?php foreach ($productos as $p): ?>
                            <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                                <div class="product-cart-wrap mb-30">
                                <div class="product-img-action-wrap">
                                    <div class="product-img product-img-zoom">
                                    <a href="#">
                                        <?php if (!empty($p['imagen'])): ?>
                                            <img class="default-img" src="/ofm/uploads/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                                        <?php else: ?>
                                            <img class="default-img" src="assets/imgs/shop/default.png" alt="Imagen no disponible">
                                        <?php endif; ?>
                                    </a>
                                    </div>
                                </div>
                                <div class="product-content-wrap">
                                    <h2><a href="#"><?= htmlspecialchars($p['nombre']) ?></a></h2>
                                    <div class="product-price">
                                    <span>$<?= number_format($p['precio'], 2) ?></span>
                                    </div>
                                    <div class="product-action-1 show">
                                    <a aria-label="Comprar ahora" class="action-btn hover-up" href="checkout.php?id=<?= $p['id'] ?>"><i class="fi-rs-shopping-bag-add"></i></a>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>

    <!-- Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="text-center">
                    <h5 class="mb-5">Now Loading</h5>
                    <div class="loader">
                        <div class="bar bar1"></div>
                        <div class="bar bar2"></div>
                        <div class="bar bar3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'foot.php'; ?>