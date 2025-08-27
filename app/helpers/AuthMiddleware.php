<?php
/**
 * AuthMiddleware - Middleware para verificar autenticación
 */

// Cargar configuración de autenticación
require_once __DIR__ . '/../../config/auth.php';

class AuthMiddleware {
    
    // Rutas que no requieren autenticación
    private static $publicRoutes = PUBLIC_ROUTES;

    /**
     * Verificar si la ruta actual requiere autenticación
     */
    public static function requiresAuth($route) {
        // Si la ruta está en la lista de rutas públicas, no requiere autenticación
        foreach (self::$publicRoutes as $publicRoute) {
            if (strpos($route, $publicRoute) === 0) {
                return false;
            }
        }
        
        // Todas las demás rutas requieren autenticación
        return true;
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public static function isAuthenticated() {
        return isset($_SESSION['usuario_id']) && 
               isset($_SESSION['usuario_activo']) && 
               $_SESSION['usuario_activo'] === true;
    }

    /**
     * Verificar autenticación y redirigir si es necesario
     */
    public static function checkAuth($route) {
        // Si la ruta requiere autenticación y el usuario no está autenticado
        if (self::requiresAuth($route) && !self::isAuthenticated()) {
            // Redirigir al login
            header('Location: ' . url('auth/login'));
            exit();
        }
        
        // Si el usuario está autenticado y trata de acceder al login, redirigir al dashboard
        if ($route === 'auth/login' && self::isAuthenticated()) {
            header('Location: ' . url('servicios'));
            exit();
        }
    }

    /**
     * Obtener información del usuario actual
     */
    public static function getCurrentUser() {
        if (!self::isAuthenticated()) {
            return null;
        }

        return [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nombres' => $_SESSION['usuario_nombres'] ?? '',
            'apellidos' => $_SESSION['usuario_apellidos'] ?? '',
            'nombre_completo' => $_SESSION['usuario_nombre_completo'] ?? '',
            'perfil' => $_SESSION['usuario_perfil'] ?? null,
            'perfil_nombre' => $_SESSION['usuario_perfil_nombre'] ?? ''
        ];
    }

    /**
     * Verificar si el usuario tiene un perfil específico
     */
    public static function hasProfile($profileName) {
        if (!self::isAuthenticated()) {
            return false;
        }

        $currentProfile = strtolower($_SESSION['usuario_perfil_nombre'] ?? '');
        return $currentProfile === strtolower($profileName);
    }

    /**
     * Verificar si el usuario es administrador
     */
    public static function isAdmin() {
        return self::hasProfile('administrador');
    }

    /**
     * Verificar si el usuario es técnico
     */
    public static function isTecnico() {
        return self::hasProfile('tecnico');
    }

    /**
     * Verificar si el usuario es asesor
     */
    public static function isAsesor() {
        return self::hasProfile('asesor');
    }
}
