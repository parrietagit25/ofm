<?php
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../models/Usuario.php';

class SocialAuthController {
    private $pdo;
    private $usuario;
    private $config;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->usuario = new Usuario($pdo);
        $this->config = require __DIR__ . '/../config/social_auth.php';
    }

    // Método para obtener URL de autorización de Facebook
    public function getFacebookAuthUrl() {
        // Detectar automáticamente la URL base
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $baseUrl = $protocol . '://' . $host;
        $redirectUri = $baseUrl . '/evara/auth/facebook-callback.php';
        
        $params = [
            'client_id' => $this->config['facebook']['app_id'],
            'redirect_uri' => $redirectUri,
            'scope' => implode(',', $this->config['facebook']['permissions']),
            'response_type' => 'code'
        ];
        
        return 'https://www.facebook.com/dialog/oauth?' . http_build_query($params);
    }

    // Método para obtener URL de autorización de Google
    public function getGoogleAuthUrl() {
        // Detectar automáticamente la URL base
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $baseUrl = $protocol . '://' . $host;
        $redirectUri = $baseUrl . '/public/evara/auth/google-callback.php';
        
        $params = [
            'client_id' => $this->config['google']['client_id'],
            'redirect_uri' => $redirectUri,
            'scope' => implode(' ', $this->config['google']['scopes']),
            'response_type' => 'code',
            'access_type' => 'offline'
        ];
        
        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
    }

    // Procesar callback de Facebook
    public function processFacebookCallback($code) {
        if (!$code) {
            return ['success' => false, 'message' => 'Código de autorización no recibido'];
        }

        // Detectar automáticamente la URL base
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $baseUrl = $protocol . '://' . $host;
        $redirectUri = $baseUrl . '/evara/auth/facebook-callback.php';
        
        // Intercambiar código por token de acceso
        $tokenUrl = 'https://graph.facebook.com/oauth/access_token';
        $tokenParams = [
            'client_id' => $this->config['facebook']['app_id'],
            'client_secret' => $this->config['facebook']['app_secret'],
            'redirect_uri' => $redirectUri,
            'code' => $code
        ];

        $tokenResponse = $this->makeHttpRequest($tokenUrl, $tokenParams);
        $tokenData = json_decode($tokenResponse, true);

        if (!isset($tokenData['access_token'])) {
            return ['success' => false, 'message' => 'Error al obtener token de acceso'];
        }

        // Obtener información del usuario
        $userUrl = 'https://graph.facebook.com/me';
        $userParams = [
            'access_token' => $tokenData['access_token'],
            'fields' => 'id,name,email'
        ];

        $userResponse = $this->makeHttpRequest($userUrl, $userParams);
        $userData = json_decode($userResponse, true);

        if (!isset($userData['id'])) {
            return ['success' => false, 'message' => 'Error al obtener información del usuario'];
        }

        return $this->processSocialUser($userData, 'facebook');
    }

    // Procesar callback de Google
    public function processGoogleCallback($code) {
        if (!$code) {
            return ['success' => false, 'message' => 'Código de autorización no recibido'];
        }

        // Detectar automáticamente la URL base
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $baseUrl = $protocol . '://' . $host;
        $redirectUri = $baseUrl . '/evara/auth/google-callback.php';
        
        // Intercambiar código por token de acceso
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $tokenParams = [
            'client_id' => $this->config['google']['client_id'],
            'client_secret' => $this->config['google']['client_secret'],
            'redirect_uri' => $redirectUri,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $tokenResponse = $this->makeHttpRequest($tokenUrl, $tokenParams, 'POST');
        $tokenData = json_decode($tokenResponse, true);

        if (!isset($tokenData['access_token'])) {
            return ['success' => false, 'message' => 'Error al obtener token de acceso'];
        }

        // Obtener información del usuario
        $userUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $userParams = [
            'access_token' => $tokenData['access_token']
        ];

        $userResponse = $this->makeHttpRequest($userUrl, $userParams);
        $userData = json_decode($userResponse, true);

        if (!isset($userData['id'])) {
            return ['success' => false, 'message' => 'Error al obtener información del usuario'];
        }

        return $this->processSocialUser($userData, 'google');
    }

    // Procesar usuario de red social
    private function processSocialUser($userData, $provider) {
        $socialId = $userData['id'];
        $email = $userData['email'] ?? '';
        $name = $userData['name'] ?? $userData['given_name'] . ' ' . $userData['family_name'] ?? 'Usuario';
        
        // Verificar si el usuario ya existe por social_id
        $existingUser = $this->usuario->obtenerPorSocialId($socialId, $provider);
        
        if ($existingUser) {
            // Usuario existe, iniciar sesión
            $_SESSION['usuario'] = $existingUser;
            return ['success' => true, 'action' => 'login', 'user' => $existingUser];
        }

        // Verificar si el email ya existe
        if ($email && $this->usuario->emailExiste($email)) {
            // Email existe, vincular cuenta social
            $this->usuario->vincularCuentaSocial($email, $socialId, $provider);
            $user = $this->usuario->obtenerPorEmail($email);
            $_SESSION['usuario'] = $user;
            return ['success' => true, 'action' => 'link', 'user' => $user];
        }

        // Crear nuevo usuario
        $username = $this->generateUniqueUsername($name);
        $password = $this->generateRandomPassword();
        
        $userId = $this->usuario->crearConSocial($name, $email, $password, $socialId, $provider);
        
        if ($userId) {
            $user = $this->usuario->obtenerPorId($userId);
            $_SESSION['usuario'] = $user;
            return ['success' => true, 'action' => 'register', 'user' => $user];
        }

        return ['success' => false, 'message' => 'Error al crear usuario'];
    }

    // Generar nombre de usuario único
    private function generateUniqueUsername($name) {
        $baseUsername = $this->cleanUsername($name);
        $username = $baseUsername;
        $counter = 1;

        while ($this->usuario->usernameExiste($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    // Limpiar nombre de usuario
    private function cleanUsername($name) {
        $username = strtolower(trim($name));
        $username = preg_replace('/[^a-z0-9]/', '', $username);
        return substr($username, 0, 20);
    }

    // Generar contraseña aleatoria
    private function generateRandomPassword() {
        return bin2hex(random_bytes(16));
    }

    // Realizar petición HTTP
    private function makeHttpRequest($url, $params, $method = 'GET') {
        $ch = curl_init();
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            $url .= '?' . http_build_query($params);
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
} 