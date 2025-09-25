<?php
/**
 * Script de prueba para verificar la configuración de sesión
 * Acceder a: http://localhost/systecsoluciones_mvc/test_session.php
 */

// Configurar y iniciar sesión
require_once __DIR__ . '/config/auth.php';

// Configurar parámetros de cookie de sesión para sesión de navegador
session_set_cookie_params([
    'lifetime' => SESSION_CONFIG['lifetime'], // 0 = Sesión de navegador
    'path' => SESSION_CONFIG['path'],
    'domain' => SESSION_CONFIG['domain'],
    'secure' => SESSION_CONFIG['secure'],
    'httponly' => SESSION_CONFIG['httponly'],
    'samesite' => SESSION_CONFIG['samesite']
]);

// Configurar tiempo de vida de la sesión (0 = indefinido hasta cerrar navegador)
ini_set('session.gc_maxlifetime', 0);
ini_set('session.cache_expire', 0);

// Iniciar sesión
session_start();

// Simular login
if (!isset($_SESSION['test_user'])) {
    $_SESSION['test_user'] = 'Usuario de Prueba';
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
    $_SESSION['session_id'] = session_id();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Configuración de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🔧 Test de Configuración de Sesión</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5>📋 Información de la Sesión</h5>
                            <ul class="mb-0">
                                <li><strong>Usuario:</strong> <?= htmlspecialchars($_SESSION['test_user']) ?></li>
                                <li><strong>Hora de Login:</strong> <?= htmlspecialchars($_SESSION['login_time']) ?></li>
                                <li><strong>Session ID:</strong> <code><?= htmlspecialchars($_SESSION['session_id']) ?></code></li>
                                <li><strong>Hora Actual:</strong> <?= date('Y-m-d H:i:s') ?></li>
                            </ul>
                        </div>

                        <div class="alert alert-success">
                            <h5>✅ Configuración Aplicada</h5>
                            <ul class="mb-0">
                                <li><strong>Lifetime:</strong> <?= SESSION_CONFIG['lifetime'] ?> segundos (0 = Sesión de navegador)</li>
                                <li><strong>Path:</strong> <?= SESSION_CONFIG['path'] ?></li>
                                <li><strong>Secure:</strong> <?= SESSION_CONFIG['secure'] ? 'Sí' : 'No' ?></li>
                                <li><strong>HttpOnly:</strong> <?= SESSION_CONFIG['httponly'] ? 'Sí' : 'No' ?></li>
                                <li><strong>SameSite:</strong> <?= SESSION_CONFIG['samesite'] ?></li>
                            </ul>
                        </div>

                        <div class="alert alert-warning">
                            <h5>⚠️ Comportamiento Esperado</h5>
                            <p class="mb-0">
                                La sesión permanecerá activa mientras mantengas la ventana del navegador abierta. 
                                Solo expirará cuando cierres completamente el navegador o la pestaña.
                            </p>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="test_session.php" class="btn btn-primary">
                                🔄 Recargar Página (Mantener Sesión)
                            </a>
                            <a href="index.php?route=auth/login" class="btn btn-success">
                                🚀 Ir al Sistema Principal
                            </a>
                            <button onclick="window.close()" class="btn btn-danger">
                                ❌ Cerrar Ventana (Expirar Sesión)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
