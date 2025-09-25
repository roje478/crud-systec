<?php
/**
 * Configuración de Autenticación y Autorización
 */

// Rutas que no requieren autenticación (públicas)
define('PUBLIC_ROUTES', [
    'auth/login',
    'auth/authenticate',
    'auth/logout',
    'servicios/buscar-servicios',
    'servicios/buscar-clientes'
]);

// Rutas que requieren autenticación pero no permisos específicos
define('AUTHENTICATED_ROUTES', [
    'servicios',
    'clientes',
    'usuarios',
    'configuracion',
    'permisos',
    'clausulas',
    'empresa'
]);

// Perfiles de usuario y sus permisos
define('USER_PROFILES', [
    'administrador' => [
        'name' => 'Administrador',
        'description' => 'Acceso completo al sistema',
        'permissions' => ['*'] // Todos los permisos
    ],
    'asesor' => [
        'name' => 'Asesor',
        'description' => 'Gestión de servicios y clientes',
        'permissions' => [
            'servicios.view',
            'servicios.create',
            'servicios.edit',
            'clientes.view',
            'clientes.create',
            'clientes.edit'
        ]
    ],
    'tecnico' => [
        'name' => 'Técnico',
        'description' => 'Gestión de servicios asignados',
        'permissions' => [
            'servicios.view',
            'servicios.edit'
        ]
    ]
]);

// Configuración de sesión
define('SESSION_CONFIG', [
    'lifetime' => 0, // 0 = Sesión de navegador (expira al cerrar ventana)
    'path' => '/',
    'domain' => '',
    'secure' => false, // Cambiar a true en producción con HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Configuración de seguridad
define('SECURITY_CONFIG', [
    'max_login_attempts' => 5,
    'lockout_duration' => 900, // 15 minutos
    'password_min_length' => 6,
    'session_regenerate_id' => true
]);
