<?php
/**
 * Aplicación Principal - Sistema MVC sin dependencias de .htaccess
 * Funciona directamente en cualquier servidor PHP
 */

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Definir la URL base del sitio
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

// Autoloader simple
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/config/',
        __DIR__ . '/app/models/',
        __DIR__ . '/app/controllers/',
        __DIR__ . '/app/helpers/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Función helper
function url($path = '') {
    return BASE_URL . '/app.php?route=' . ltrim($path, '/');
}

// Router simple
$route = $_GET['route'] ?? 'servicios';
$parts = explode('/', $route);
$controller = $parts[0] ?? 'servicios';
$action = $parts[1] ?? 'index';
$param = $parts[2] ?? null;
$method = $_SERVER['REQUEST_METHOD'];



try {
    if ($controller === 'servicios') {
        $controllerInstance = new ServicioController();

        if ($action === 'index' || empty($action)) {
            $controllerInstance->index();
        } elseif ($action === 'create') {
            if ($method === 'POST') {
                $controllerInstance->store();
            } else {
                $controllerInstance->create();
            }
        } elseif ($action === 'view' && $param) {
            $controllerInstance->view($param);
        } elseif ($action === 'edit' && $param) {
            if ($method === 'POST') {
                $controllerInstance->update($param);
            } else {
                $controllerInstance->edit($param);
            }
        } elseif ($action === 'changeStatus' && $param && $method === 'POST') {
            $controllerInstance->changeStatus($param);
        } elseif ($action === 'delete' && $param && $method === 'POST') {
            $controllerInstance->delete($param);
        } else {
            echo "
            <div style='text-align:center; padding:50px; font-family: Arial;'>
                <h1>⚠️ Acción no disponible</h1>
                <p>La acción '$action' no está implementada aún.</p>
                <a href='app.php?route=servicios' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>
                    Volver a Servicios
                </a>
            </div>";
        }
    } else {
        echo "
        <div style='text-align:center; padding:50px; font-family: Arial;'>
            <h1>🚫 Controlador no encontrado</h1>
            <p>El controlador '$controller' no existe.</p>
            <a href='app.php?route=servicios' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>
                Ir a Servicios
            </a>
        </div>";
    }
} catch (Exception $e) {
    echo "
    <div style='text-align:center; padding:50px; font-family: Arial; color:red;'>
        <h1>💥 Error del Sistema</h1>
        <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <a href='inicio.php' style='background:#28a745; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>
            🏠 Volver al Inicio
        </a>
    </div>";
}
?>