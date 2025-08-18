<?php
/**
 * Ejemplo de uso de la autenticación social
 * 
 * Este archivo muestra cómo implementar los botones de login social
 * en cualquier página de tu aplicación.
 */

session_start();
require_once __DIR__ . '/SocialAuthController.php';
require_once __DIR__ . '/../../../includes/db.php';

$socialAuth = new SocialAuthController($pdo);
$lang = $_SESSION['lang'] ?? 'es';
$texts = require __DIR__ . '/../lang/' . $lang . '.php';
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplo de Login Social</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .social-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        .btn-social {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: bold;
        }
        .btn-facebook {
            background-color: #1877f2;
        }
        .btn-google {
            background-color: #db4437;
        }
        .btn-social:hover {
            opacity: 0.9;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2>Login Social</h2>
        
        <!-- Botones de redes sociales -->
        <div class="social-buttons">
            <a href="<?= $socialAuth->getFacebookAuthUrl() ?>" class="btn-social btn-facebook">
                <i class="fab fa-facebook-f"></i>
                <?= $texts['login_with_facebook'] ?>
            </a>
            
            <a href="<?= $socialAuth->getGoogleAuthUrl() ?>" class="btn-social btn-google">
                <i class="fab fa-google"></i>
                <?= $texts['login_with_google'] ?>
            </a>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <p>O continúa con tu email y contraseña</p>
            <a href="../page-login-register.php">Ir al login tradicional</a>
        </div>
    </div>

    <script>
        // Ejemplo de cómo manejar errores de autenticación social
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        const message = urlParams.get('message');
        
        if (error === 'social_auth') {
            alert('Error en autenticación social: ' + (message || 'Error desconocido'));
        }
    </script>
</body>
</html> 