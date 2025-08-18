<?php
/**
 * Script para configurar automáticamente las URIs de OAuth
 * Este script te ayudará a configurar correctamente las URIs de redirección
 */

// Detectar la URL base automáticamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
$baseUrl = $protocol . '://' . $host;

$googleCallback = $baseUrl . '/public/evara/auth/google-callback.php';
$facebookCallback = $baseUrl . '/evara/auth/facebook-callback.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración OAuth - Panama Ofertas y Más</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .section { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .url { background: #e8f4fd; padding: 10px; border-radius: 3px; font-family: monospace; word-break: break-all; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .step { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; }
        .copy-btn { background: #007bff; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Configuración OAuth - Panama Ofertas y Más</h1>
        
        <div class="section">
            <h2>📊 Información Detectada</h2>
            <p><strong>URL Base:</strong> <span class="url"><?= $baseUrl ?></span></p>
            <p><strong>Protocolo:</strong> <?= $protocol ?></p>
            <p><strong>Host:</strong> <?= $host ?></p>
        </div>

        <div class="section">
            <h2>🔗 URIs de Callback</h2>
            
            <h3>Google OAuth</h3>
            <div class="step">
                <p><strong>URL de Callback para Google:</strong></p>
                <div class="url" id="google-callback"><?= $googleCallback ?></div>
                <button class="copy-btn" onclick="copyToClipboard('google-callback')">Copiar URL</button>
            </div>

            <h3>Facebook OAuth</h3>
            <div class="step">
                <p><strong>URL de Callback para Facebook:</strong></p>
                <div class="url" id="facebook-callback"><?= $facebookCallback ?></div>
                <button class="copy-btn" onclick="copyToClipboard('facebook-callback')">Copiar URL</button>
            </div>
        </div>

        <div class="section">
            <h2>⚙️ Configuración en Google Cloud Console</h2>
            
            <div class="step">
                <h4>Paso 1: Ir a Google Cloud Console</h4>
                <p><a href="https://console.cloud.google.com/apis/credentials" target="_blank">https://console.cloud.google.com/apis/credentials</a></p>
            </div>

            <div class="step">
                <h4>Paso 2: Seleccionar tu proyecto</h4>
                <p>Asegúrate de que el proyecto correcto esté seleccionado en la parte superior.</p>
            </div>

            <div class="step">
                <h4>Paso 3: Editar OAuth 2.0 Client ID</h4>
                <p>Busca tu cliente OAuth 2.0 (ID: <code>252559045531-e2gvveg1cdfjsfa00fr7tqtt86rpcrjm.apps.googleusercontent.com</code>) y haz clic para editarlo.</p>
            </div>

            <div class="step">
                <h4>Paso 4: Agregar URI de redirección</h4>
                <p>En la sección "Authorized redirect URIs", agrega esta URL:</p>
                <div class="url"><?= $googleCallback ?></div>
                <button class="copy-btn" onclick="copyToClipboard('google-callback')">Copiar URL</button>
            </div>

            <div class="step">
                <h4>Paso 5: Guardar cambios</h4>
                <p>Haz clic en "Save" para guardar los cambios.</p>
            </div>
        </div>

        <div class="section">
            <h2>📱 Configuración en Facebook Developers</h2>
            
            <div class="step">
                <h4>Paso 1: Ir a Facebook Developers</h4>
                <p><a href="https://developers.facebook.com/" target="_blank">https://developers.facebook.com/</a></p>
            </div>

            <div class="step">
                <h4>Paso 2: Seleccionar tu aplicación</h4>
                <p>Selecciona la aplicación que creaste para este proyecto.</p>
            </div>

            <div class="step">
                <h4>Paso 3: Configurar OAuth</h4>
                <p>Ve a "Facebook Login" > "Settings" y agrega esta URL en "Valid OAuth Redirect URIs":</p>
                <div class="url"><?= $facebookCallback ?></div>
                <button class="copy-btn" onclick="copyToClipboard('facebook-callback')">Copiar URL</button>
            </div>
        </div>

        <div class="warning">
            <h3>⚠️ Notas Importantes</h3>
            <ul>
                <li>Asegúrate de que tu dominio esté autorizado en Google Cloud Console</li>
                <li>Para desarrollo local, agrega también: <code>http://localhost/evara/public/evara/auth/google-callback.php</code></li>
                <li>Los cambios pueden tardar unos minutos en propagarse</li>
                <li>Si usas HTTPS, asegúrate de que el certificado SSL sea válido</li>
            </ul>
        </div>

        <div class="section">
            <h2>🧪 Probar Configuración</h2>
            <p>Una vez configurado, puedes probar la autenticación:</p>
            <ul>
                <li><a href="../page-login-register.php">Ir a la página de login</a></li>
                <li><a href="debug-google.php">Debug de configuración Google</a></li>
                <li><a href="example-usage.php">Ejemplo de uso</a></li>
            </ul>
        </div>
    </div>

    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            navigator.clipboard.writeText(text).then(function() {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = '¡Copiado!';
                button.style.background = '#28a745';
                
                setTimeout(function() {
                    button.textContent = originalText;
                    button.style.background = '#007bff';
                }, 2000);
            }).catch(function(err) {
                console.error('Error al copiar: ', err);
                alert('Error al copiar al portapapeles');
            });
        }
    </script>
</body>
</html> 