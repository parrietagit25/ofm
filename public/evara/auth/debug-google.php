<?php
/**
 * Script de debug para verificar la configuración de Google OAuth
 */

require_once __DIR__ . '/SocialAuthController.php';
require_once __DIR__ . '/../../../includes/db.php';

$socialAuth = new SocialAuthController($pdo);

echo "<h2>Debug de Configuración Google OAuth</h2>";

// Mostrar la URL de autorización
$authUrl = $socialAuth->getGoogleAuthUrl();
echo "<h3>URL de Autorización:</h3>";
echo "<p><strong>URL completa:</strong> " . htmlspecialchars($authUrl) . "</p>";

// Extraer y mostrar los parámetros
$urlParts = parse_url($authUrl);
if (isset($urlParts['query'])) {
    parse_str($urlParts['query'], $params);
    echo "<h3>Parámetros de la URL:</h3>";
    echo "<ul>";
    foreach ($params as $key => $value) {
        echo "<li><strong>$key:</strong> " . htmlspecialchars($value) . "</li>";
    }
    echo "</ul>";
}

// Mostrar información del servidor
echo "<h3>Información del Servidor:</h3>";
echo "<ul>";
echo "<li><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "</li>";
echo "<li><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "</li>";
echo "<li><strong>HTTPS:</strong> " . (isset($_SERVER['HTTPS']) ? 'Sí' : 'No') . "</li>";
echo "<li><strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'No definido') . "</li>";
echo "</ul>";

// Mostrar la URL base actual
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
$baseUrl = $protocol . '://' . $host;
echo "<h3>URL Base Detectada:</h3>";
echo "<p><strong>URL Base:</strong> $baseUrl</p>";

// Mostrar la URL de callback que debería estar configurada
$callbackUrl = $baseUrl . '/evara/auth/google-callback.php';
echo "<p><strong>URL de Callback Sugerida:</strong> $callbackUrl</p>";

echo "<h3>Instrucciones:</h3>";
echo "<ol>";
echo "<li>Ve a <a href='https://console.cloud.google.com/apis/credentials' target='_blank'>Google Cloud Console</a></li>";
echo "<li>Selecciona tu proyecto</li>";
echo "<li>Ve a 'APIs & Services' > 'Credentials'</li>";
echo "<li>Encuentra tu OAuth 2.0 Client ID</li>";
echo "<li>Haz clic para editarlo</li>";
echo "<li>En 'Authorized redirect URIs' agrega: <code>$callbackUrl</code></li>";
echo "<li>Guarda los cambios</li>";
echo "</ol>";

echo "<h3>Probar Configuración:</h3>";
echo "<p><a href='$authUrl' target='_blank'>Probar Login con Google</a></p>";
?> 