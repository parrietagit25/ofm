<?php
// Configuración para autenticación social
return [
    'facebook' => [
        'app_id' => 'TU_FACEBOOK_APP_ID', // Reemplazar con tu App ID de Facebook
        'app_secret' => 'TU_FACEBOOK_APP_SECRET', // Reemplazar con tu App Secret de Facebook
        'redirect_uri' => 'https://tudominio.com/evara/auth/facebook-callback.php',
        'permissions' => ['email', 'public_profile']
    ],
    'google' => [
        'client_id' => '252559045531-e2gvveg1cdfjsfa00fr7tqtt86rpcrjm.apps.googleusercontent.com', // Reemplazar con tu Client ID de Google
        'client_secret' => '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', // Reemplazar con tu Client Secret de Google
        'redirect_uri' => 'https://panamaofertasymas.com/evara/auth/google-callback.php',
        'scopes' => ['email', 'profile']
        //AIzaSyBpcDFFt990zUMklpiB1NR7pEK1hHzb8e8
    ]
]; 