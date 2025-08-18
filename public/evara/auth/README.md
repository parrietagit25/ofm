# Autenticación Social - Facebook y Google

Este sistema permite a los usuarios registrarse e iniciar sesión usando sus cuentas de Facebook y Google.

## Configuración Requerida

### 1. Configurar Facebook App

1. Ve a [Facebook Developers](https://developers.facebook.com/)
2. Crea una nueva aplicación
3. En la configuración de la app, agrega:
   - **App ID**: Tu ID de aplicación de Facebook
   - **App Secret**: Tu secreto de aplicación de Facebook
   - **Valid OAuth Redirect URIs**: `https://tudominio.com/evara/auth/facebook-callback.php`

### 2. Configurar Google OAuth

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la API de Google+ 
4. En "Credenciales", crea una nueva credencial OAuth 2.0
5. Configura:
   - **Client ID**: Tu ID de cliente de Google
   - **Client Secret**: Tu secreto de cliente de Google
   - **Authorized redirect URIs**: `https://tudominio.com/evara/auth/google-callback.php`

### 3. Actualizar Configuración

Edita el archivo `config/social_auth.php` y reemplaza los valores de ejemplo:

```php
<?php
return [
    'facebook' => [
        'app_id' => 'TU_FACEBOOK_APP_ID', // Reemplazar con tu App ID real
        'app_secret' => 'TU_FACEBOOK_APP_SECRET', // Reemplazar con tu App Secret real
        'redirect_uri' => 'https://tudominio.com/evara/auth/facebook-callback.php',
        'permissions' => ['email', 'public_profile']
    ],
    'google' => [
        'client_id' => 'TU_GOOGLE_CLIENT_ID', // Reemplazar con tu Client ID real
        'client_secret' => 'TU_GOOGLE_CLIENT_SECRET', // Reemplazar con tu Client Secret real
        'redirect_uri' => 'https://tudominio.com/evara/auth/google-callback.php',
        'scopes' => ['email', 'profile']
    ]
];
```

### 4. Base de Datos

Ejecuta el script SQL para crear la tabla necesaria:

```sql
CREATE TABLE IF NOT EXISTS usuarios_sociales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    social_id VARCHAR(255) NOT NULL,
    provider ENUM('facebook', 'google') NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_social_provider (social_id, provider),
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_social_id (social_id),
    INDEX idx_provider (provider)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Características

### Funcionalidades Implementadas

1. **Login con Facebook**: Los usuarios pueden iniciar sesión usando su cuenta de Facebook
2. **Login con Google**: Los usuarios pueden iniciar sesión usando su cuenta de Google
3. **Registro automático**: Si el usuario no existe, se crea automáticamente
4. **Vinculación de cuentas**: Si el email ya existe, se vincula la cuenta social
5. **Sesión persistente**: El usuario queda logueado después de la autenticación social

### Flujo de Autenticación

1. Usuario hace clic en "Login con Facebook/Google"
2. Se redirige a la plataforma correspondiente
3. Usuario autoriza la aplicación
4. Se procesa la información del usuario
5. Se crea o vincula la cuenta
6. Se inicia sesión automáticamente

### Seguridad

- Las contraseñas se generan automáticamente para cuentas sociales
- Se valida que el email no esté duplicado
- Se usan tokens seguros para la autenticación
- Las sesiones se manejan de forma segura

## Archivos Principales

- `SocialAuthController.php`: Controlador principal para la autenticación social
- `facebook-callback.php`: Maneja el callback de Facebook
- `google-callback.php`: Maneja el callback de Google
- `config/social_auth.php`: Configuración de las APIs

## Notas Importantes

1. **HTTPS requerido**: Las APIs de Facebook y Google requieren HTTPS en producción
2. **Dominios autorizados**: Asegúrate de que tu dominio esté autorizado en las configuraciones de las APIs
3. **Permisos**: Solo se solicitan permisos básicos (email y perfil público)
4. **Privacidad**: Revisa las políticas de privacidad de tu aplicación

## Solución de Problemas

### Error "Invalid redirect URI"
- Verifica que las URIs de redirección estén correctamente configuradas en las APIs

### Error "App not configured"
- Asegúrate de que la aplicación esté en modo "Live" en Facebook
- Verifica que las credenciales de Google estén correctas

### Error de conexión
- Verifica que cURL esté habilitado en tu servidor
- Comprueba que las URLs de las APIs sean accesibles 