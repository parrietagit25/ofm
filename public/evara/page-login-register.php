<?php
// Incluir helper de sesiones
require_once __DIR__ . '/../../includes/session_helper.php';

// Iniciar sesión de forma segura
iniciarSesionSegura();

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
$lang = $_SESSION['lang'] ?? 'es';
$texts = require __DIR__ . '/lang/' . $lang . '.php';

// Inicializar controlador de autenticación simplificado
require_once __DIR__ . '/../../controllers/loginController_simple.php';
$pdo = getConnection();
$loginController = new LoginControllerSimple($pdo);

// Procesar login si se envía por POST
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $clave = $_POST['clave'] ?? '';
        
        // DEBUG: Mostrar información del login
        error_log("DEBUG: Intentando login para email: " . $email);
        
        $resultado = $loginController->procesarLogin($email, $clave);
        
        // DEBUG: Mostrar resultado del login
        error_log("DEBUG: Resultado del login: " . json_encode($resultado));
        
        if ($resultado['success']) {
            // DEBUG: Mostrar redirección
            error_log("DEBUG: Login exitoso, redirigiendo a: " . $resultado['redirect']);
            
            // Login exitoso, redirigir
            header("Location: " . $resultado['redirect']);
            exit;
        } else {
            $error = $resultado['message'];
            error_log("DEBUG: Error en login: " . $error);
        }
    } elseif ($action === 'registro') {
        $nombre = $_POST['nombre'] ?? '';
        $apellido = $_POST['apellido'] ?? '';
        $email = $_POST['email'] ?? '';
        $clave = $_POST['clave'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        
        $resultado = $loginController->procesarRegistro($nombre, $apellido, $email, $clave, $telefono);
        
        if ($resultado['success']) {
            // Registro exitoso, redirigir
            header("Location: " . $resultado['redirect']);
            exit;
        } else {
            $error = $resultado['message'];
        }
    }
}

// Si ya está autenticado, redirigir al dashboard correspondiente
if ($loginController->estaAutenticado()) {
    $loginController->redirigirSegunRol();
}

// Obtener mensajes de error si existen
$error = $error ?: ($_GET['error'] ?? '');
$success = $_GET['success'] ?? '';

// Obtener mensaje de logout exitoso
$logoutSuccess = $_GET['logout'] ?? '';
?>

<!DOCTYPE html>
<html class="no-js" lang="<?= $lang ?>">

<head>
    <meta charset="utf-8">
    <title>OFM - Iniciar Sesión / Registrarse</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="OFM - Ofertas y Más. Inicia sesión o regístrate para acceder a las mejores ofertas">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="OFM - Iniciar Sesión / Registrarse">
    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:image" content="">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/theme/favicon.svg">
    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/main.css?v=3.4">
</head>

<body>
    <?php include 'header.php'; ?>
    
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="index.php" rel="nofollow">Inicio</a>
                    <span></span> Páginas
                    <span></span> Iniciar Sesión / Registrarse
                </div>
            </div>
        </div>
        
        <section class="pt-150 pb-150">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 m-auto">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="login_wrap widget-taber-content p-30 background-white border-radius-10 mb-md-5 mb-lg-0 mb-sm-5">
                                    <div class="padding_eight_all bg-white">
                                        <div class="heading_s1">
                                            <h3 class="mb-30">
                                                <?php if (isset($_GET['redirect']) && $_GET['redirect'] === 'admin'): ?>
                                                    <i class="fas fa-shield-alt text-primary me-2"></i>
                                                    Acceso Administrativo
                                                <?php else: ?>
                                                    <?= $texts['login'] ?>
                                                <?php endif; ?>
                                            </h3>
                                        </div>
                                        
                                        <?php if (isset($_GET['redirect']) && $_GET['redirect'] === 'admin'): ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Panel Administrativo:</strong> Ingresa con tus credenciales de administrador para acceder al panel de control.
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($error): ?>
                                            <div class="alert alert-danger">
                                                <?= htmlspecialchars($error) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($logoutSuccess === 'success'): ?>
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Sesión cerrada exitosamente. ¡Hasta pronto!
                                            </div>
                                        <?php endif; ?>
                                        
                                        <form method="post" action="">
                                            <input type="hidden" name="action" value="login">
                                            <div class="form-group">
                                                <input type="email" required="" name="email" placeholder="<?= $texts['email'] ?>">
                                            </div>
                                            <div class="form-group">
                                                <input required="" type="password" name="clave" placeholder="<?= $texts['password'] ?>">
                                            </div>
                                            <div class="login_footer form-group">
                                                <div class="chek-form">
                                                    <div class="custome-checkbox">
                                                        <input class="form-check-input" type="checkbox" name="remember" id="exampleCheckbox1" value="1">
                                                        <label class="form-check-label" for="exampleCheckbox1"><span><?= $texts['remember_me'] ?></span></label>
                                                    </div>
                                                </div>
                                                <a class="text-muted" href="#"><?= $texts['forgot_password'] ?></a>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-fill-out btn-block hover-up" name="login"><?= $texts['login'] ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1"></div>
                            <div class="col-lg-6">
                                <div class="login_wrap widget-taber-content p-30 background-white border-radius-5">
                                    <div class="padding_eight_all bg-white">
                                        <div class="heading_s1">
                                            <h3 class="mb-30"><?= $texts['create_account'] ?></h3>
                                        </div>
                                        <p class="mb-50 font-sm">
                                            <?= $texts['personal_data_description'] ?>
                                        </p>
                                        
                                        <?php if ($error): ?>
                                            <div class="alert alert-danger">
                                                <?= htmlspecialchars($error) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($logoutSuccess === 'success'): ?>
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Sesión cerrada exitosamente. ¡Hasta pronto!
                                            </div>
                                        <?php endif; ?>
                                        
                                        <form method="post" action="">
                                            <input type="hidden" name="action" value="registro">
                                            <div class="form-group">
                                                <input type="text" required="" name="nombre" placeholder="Nombre">
                                            </div>
                                            <div class="form-group">
                                                <input type="text" required="" name="apellido" placeholder="Apellido">
                                            </div>
                                            <div class="form-group">
                                                <input type="email" required="" name="email" placeholder="<?= $texts['email'] ?>">
                                            </div>
                                            <div class="form-group">
                                                <input type="tel" name="telefono" placeholder="Teléfono (opcional)">
                                            </div>
                                            <div class="form-group">
                                                <input required="" type="password" name="clave" placeholder="<?= $texts['password'] ?>">
                                            </div>
                                            <div class="form-group">
                                                <input required="" type="password" name="confirmar_clave" placeholder="<?= $texts['confirm_password'] ?>">
                                            </div>
                                            <div class="login_footer form-group">
                                                <div class="chek-form">
                                                    <div class="custome-checkbox">
                                                        <input class="form-check-input" type="checkbox" name="agree_terms" id="exampleCheckbox12" value="1" required>
                                                        <label class="form-check-label" for="exampleCheckbox12"><span><?= $texts['agree_terms'] ?></span></label>
                                                    </div>
                                                </div>
                                                <a href="page-privacy-policy.html"><i class="fi-rs-book-alt mr-5 text-muted"></i><?= $texts['learn_more'] ?></a>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-fill-out btn-block hover-up" name="register"><?= $texts['submit_register'] ?></button>
                                            </div>
                                        </form>
                                        
                                        <div class="divider-text-center mt-15 mb-15">
                                            <span> <?= $texts['or'] ?></span>
                                        </div>
                                        
                                        <ul class="btn-login list_none text-center mb-15">
                                            <li><a href="#" class="btn btn-facebook hover-up mb-lg-0 mb-sm-4"><?= $texts['login_with_facebook'] ?></a></li>
                                            <li><a href="#" class="btn btn-google hover-up"><?= $texts['login_with_google'] ?></a></li>
                                        </ul>
                                        
                                        <div class="text-muted text-center"><?= $texts['already_have_account'] ?> <a href="#"><?= $texts['sign_in'] ?></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <!-- Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="text-center">
                    <h5 class="mb-5"><?= $texts['now_loading'] ?></h5>
                    <div class="loader">
                        <div class="bar bar1"></div>
                        <div class="bar bar2"></div>
                        <div class="bar bar3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Vendor JS-->
    <script src="assets/js/vendor/modernizr-3.6.0.min.js"></script>
    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/vendor/jquery-migrate-3.3.0.min.js"></script>
    <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>
    <script src="assets/js/plugins/slick.js"></script>
    <script src="assets/js/plugins/jquery.syotimer.min.js"></script>
    <script src="assets/js/plugins/wow.js"></script>
    <script src="assets/js/plugins/jquery-ui.js"></script>
    <script src="assets/js/plugins/perfect-scrollbar.js"></script>
    <script src="assets/js/plugins/magnific-popup.js"></script>
    <script src="assets/js/plugins/select2.min.js"></script>
    <script src="assets/js/plugins/waypoints.js"></script>
    <script src="assets/js/plugins/counterup.js"></script>
    <script src="assets/js/plugins/jquery.countdown.min.js"></script>
    <script src="assets/js/plugins/images-loaded.js"></script>
    <script src="assets/js/plugins/isotope.js"></script>
    <script src="assets/js/plugins/scrollup.js"></script>
    <script src="assets/js/plugins/jquery.vticker-min.js"></script>
    <script src="assets/js/plugins/jquery.theia.sticky.js"></script>
    <!-- Template  JS -->
    <script src="./assets/js/main.js?v=3.4"></script>
    
    <!-- Validación de contraseñas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formRegistro = document.querySelector('form[action*="registro"]');
            const clave = formRegistro.querySelector('input[name="clave"]');
            const confirmarClave = formRegistro.querySelector('input[name="confirmar_clave"]');
            
            formRegistro.addEventListener('submit', function(e) {
                if (clave.value !== confirmarClave.value) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return false;
                }
                
                if (clave.value.length < 6) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 6 caracteres');
                    return false;
                }
            });
        });
    </script>
</body>

</html>