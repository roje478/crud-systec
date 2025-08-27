<?php
/**
 * Router Principal - Sistema MVC de Servicios
 * Versión consolidada que funciona SIN .htaccess - Solución Definitiva
 */

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar buffer de salida para evitar problemas con headers
ob_start();

// Iniciar sesión
session_start();

// Definir la URL base del sitio
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

// Verificar autenticación antes de procesar cualquier ruta
$route = $_GET['route'] ?? 'servicios';
require_once __DIR__ . '/app/helpers/AuthMiddleware.php';
AuthMiddleware::checkAuth($route);

// Array para trackear clases ya cargadas
$loadedClasses = [];

// Autoloader mejorado que evita cargas duplicadas y maneja dependencias
spl_autoload_register(function ($class) use (&$loadedClasses) {
    // Si ya está cargada, no hacer nada
    if (isset($loadedClasses[$class]) || class_exists($class, false)) {
        return;
    }

    // Definir orden de carga para manejar dependencias
    $loadOrder = [
        __DIR__ . '/config/',           // Database primero
        __DIR__ . '/app/models/',       // BaseModel antes que otros modelos
        __DIR__ . '/app/helpers/',      // Helpers
        __DIR__ . '/app/controllers/'   // Controladores al final
    ];

    foreach ($loadOrder as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            $loadedClasses[$class] = true;
            return;
        }
    }
});

// Funciones helper
function url($path = '') {
    return BASE_URL . '/index.php?route=' . ltrim($path, '/');
}

/**
 * Router Principal y Seguro
 */
class RouterPrincipal {
    public function dispatch() {
        // Obtener la ruta desde GET
        $route = $_GET['route'] ?? 'servicios';

        // Limpiar la ruta de parámetros GET
        $route = explode('?', $route)[0];

        // Parsear la ruta
        $parts = explode('/', $route);
        $controller = $parts[0] ?? 'servicios';
        $action = $parts[1] ?? 'index';
        $param = $parts[2] ?? null;

        // Determinar método HTTP
        $method = $_SERVER['REQUEST_METHOD'];

        try {
            // Manejar servicios
            if ($controller === 'servicios') {
                // Debug: Log del enrutamiento
                error_log("Router: Procesando controlador 'servicios'");
                error_log("Router: Acción = '$action', Parámetro = '$param', Método = '$method'");

                // Verificar que las clases existan antes de instanciar
                if (!class_exists('ServicioController')) {
                    throw new Exception('ServicioController no encontrado');
                }

                $controllerInstance = new ServicioController();

                // Enrutar acciones
                if ($action === 'index' || empty($action)) {
                    $controllerInstance->index();
                } elseif ($action === 'select-cliente') {
                    $controllerInstance->selectCliente();
                } elseif ($action === 'create') {
                    $controllerInstance->create();
                } elseif ($action === 'store' && $method === 'POST') {
                    $controllerInstance->store();
                } elseif ($action === 'edit' && $param) {
                    if ($method === 'POST') {
                        error_log("Router Servicios: Llamando a ServicioController->update($param)");
                        $controllerInstance->update($param);
                    } else {
                        error_log("Router Servicios: Llamando a ServicioController->edit($param)");
                        $controllerInstance->edit($param);
                    }
                } elseif ($action === 'view' && $param) {
                    $controllerInstance->view($param);
                } elseif ($action === 'delete' && $param && $method === 'POST') {
                    $controllerInstance->delete($param);
                } elseif ($action === 'imprimir' && $param && $method === 'GET') {
                    $controllerInstance->imprimir($param);
                } elseif ($action === 'change-status' && $param && $method === 'POST') {
                    $controllerInstance->changeStatus($param);
                } elseif ($action === 'changeStatus' && $param && $method === 'POST') {
                    $controllerInstance->changeStatus($param);
                } elseif ($action === 'lista-completa') {
                    $controllerInstance->listaCompleta();
                } else {
                    $this->handle404("Acción '$action' no encontrada");
                }
            }
            // Manejar clientes
            elseif ($controller === 'clientes') {
                // Verificar que las clases existan antes de instanciar
                if (!class_exists('ClienteController')) {
                    throw new Exception('ClienteController no encontrado');
                }

                $controllerInstance = new ClienteController();

                // Enrutar acciones
                if ($action === 'index' || empty($action)) {
                    $controllerInstance->index();
                } elseif ($action === 'create') {
                    $controllerInstance->create();
                } elseif ($action === 'store' && $method === 'POST') {
                    $controllerInstance->store();
                } elseif ($action === 'edit' && $param) {
                    if ($method === 'POST') {
                        $controllerInstance->update($param);
                    } else {
                        $controllerInstance->edit($param);
                    }
                } elseif ($action === 'update' && $param && $method === 'POST') {
                    $controllerInstance->update($param);
                } elseif ($action === 'view' && $param) {
                    $controllerInstance->view($param);
                } elseif ($action === 'delete' && $param && $method === 'POST') {
                    $controllerInstance->delete($param);
                } elseif ($action === 'search' && $method === 'GET') {
                    $controllerInstance->search();

                } else {
                    $this->handle404("Acción '$action' no encontrada");
                }
            }
            // Manejar usuarios
            elseif ($controller === 'usuarios') {
                // Verificar que las clases existan antes de instanciar
                if (!class_exists('UsuarioController')) {
                    throw new Exception('UsuarioController no encontrado');
                }

                $controllerInstance = new UsuarioController();

                // Enrutar acciones
                if ($action === 'index' || empty($action)) {
                    $controllerInstance->index();
                } elseif ($action === 'create') {
                    $controllerInstance->create();
                } elseif ($action === 'store' && $method === 'POST') {
                    $controllerInstance->store();
                } elseif ($action === 'edit' && $param) {
                    if ($method === 'POST') {
                        $controllerInstance->update($param);
                    } else {
                        $controllerInstance->edit($param);
                    }
                } elseif ($action === 'update' && $param && $method === 'POST') {
                    $controllerInstance->update($param);
                } elseif ($action === 'view' && $param) {
                    $controllerInstance->view($param);
                } elseif ($action === 'delete' && $param && $method === 'POST') {
                    $controllerInstance->delete($param);
                } elseif ($action === 'change-status' && $param && $method === 'POST') {
                    $controllerInstance->changeStatus($param);
                } elseif ($action === 'get-estadisticas' && $method === 'GET') {
                    $controllerInstance->getEstadisticas();
                } elseif ($action === 'profile') {
                    $controllerInstance->profile();
                } elseif ($action === 'update-profile' && $method === 'POST') {
                    $controllerInstance->updateProfile();
                } else {
                    $this->handle404("Acción '$action' no encontrada");
                }
            }
            // Manejar configuración
            elseif ($controller === 'configuracion') {
                // Verificar que las clases existan antes de instanciar
                if (!class_exists('ConfiguracionController')) {
                    throw new Exception('ConfiguracionController no encontrado');
                }

                $controllerInstance = new ConfiguracionController();

                // Enrutar acciones
                if ($action === 'index' || empty($action)) {
                    $controllerInstance->index();
                } elseif ($action === 'estados') {
                    $controllerInstance->estados();
                } elseif ($action === 'create-estado') {
                    $controllerInstance->createEstado();
                } elseif ($action === 'edit-estado' && $param) {
                    $controllerInstance->editEstado($param);
                } elseif ($action === 'delete-estado' && $param && $method === 'POST') {
                    $controllerInstance->deleteEstado($param);
                } elseif ($action === 'tipos-servicio') {
                    $controllerInstance->tiposServicio();
                } elseif ($action === 'create-tipo-servicio') {
                    $controllerInstance->createTipoServicio();
                } elseif ($action === 'edit-tipo-servicio' && $param) {
                    $controllerInstance->editTipoServicio($param);
                } elseif ($action === 'delete-tipo-servicio' && $param && $method === 'POST') {
                    $controllerInstance->deleteTipoServicio($param);
                } elseif ($action === 'create-defaults' && $method === 'POST') {
                    $controllerInstance->createDefaults();
                } else {
                    $this->handle404("Acción '$action' no encontrada");
                }
            }
            // Manejar permisos
            elseif ($controller === 'permisos') {
                // Verificar que las clases existan antes de instanciar
                if (!class_exists('PermisoController')) {
                    throw new Exception('PermisoController no encontrado');
                }

                $controllerInstance = new PermisoController();

                // Enrutar acciones
                if ($action === 'index' || empty($action)) {
                    $controllerInstance->index();
                } elseif ($action === 'asignar') {
                    $controllerInstance->asignar($param);
                } elseif ($action === 'guardar-asignacion' && $param && $method === 'POST') {
                    $controllerInstance->guardarAsignacion($param);
                } elseif ($action === 'verificar-permiso' && $method === 'GET') {
                    $usuarioId = $_GET['usuario_id'] ?? null;
                    $opcion = $_GET['opcion'] ?? null;
                    if ($usuarioId && $opcion) {
                        $controllerInstance->verificarPermiso($usuarioId, $opcion);
                    } else {
                        $this->handle404("Parámetros faltantes");
                    }
                } elseif ($action === 'get-menu-usuario' && $method === 'GET') {
                    $usuarioId = $_GET['usuario_id'] ?? null;
                    if ($usuarioId) {
                        $controllerInstance->getMenuUsuario($usuarioId);
                    } else {
                        $this->handle404("ID de usuario faltante");
                    }
                } elseif ($action === 'opciones') {
                    $controllerInstance->opciones();
                } elseif ($action === 'crear-opcion' && $method === 'POST') {
                    $controllerInstance->crearOpcion();
                } elseif ($action === 'create-perfil') {
                    $controllerInstance->create();
                } elseif ($action === 'edit-perfil' && $param) {
                    $controllerInstance->edit($param);
                } elseif ($action === 'create') {
                    $controllerInstance->create();
                } elseif ($action === 'store' && $method === 'POST') {
                    $controllerInstance->store();
                } elseif ($action === 'edit' && $param) {
                    $controllerInstance->edit($param);
                } elseif ($action === 'update' && $param && $method === 'POST') {
                    $controllerInstance->update($param);
                } elseif ($action === 'delete' && $param && $method === 'POST') {
                    $controllerInstance->delete($param);
                } else {
                    $this->handle404("Acción '$action' no encontrada");
                }
            }
            // Manejar autenticación
            elseif ($controller === 'auth') {
                // Verificar que las clases existan antes de instanciar
                if (!class_exists('AuthController')) {
                    throw new Exception('AuthController no encontrado');
                }

                $controllerInstance = new AuthController();

                // Enrutar acciones
                if ($action === 'login' || empty($action)) {
                    $controllerInstance->login();
                } elseif ($action === 'authenticate' && $method === 'POST') {
                    $controllerInstance->authenticate();
                } elseif ($action === 'logout') {
                    $controllerInstance->logout();
                } else {
                    $this->handle404("Acción '$action' no encontrada");
                }
            } elseif ($controller === 'clausulas') {
                // Verificar que las clases existan antes de instanciar
                if (!class_exists('ClausulaController')) {
                    throw new Exception('ClausulaController no encontrado');
                }

                $controllerInstance = new ClausulaController();

                // Enrutar acciones
                if ($action === 'index' || empty($action)) {
                    $controllerInstance->index();
                } elseif ($action === 'create') {
                    if ($method === 'POST') {
                        $controllerInstance->store();
                    } else {
                        $controllerInstance->create();
                    }
                } elseif ($action === 'edit' && $param) {
                    if ($method === 'POST') {
                        $controllerInstance->update($param);
                    } else {
                        $controllerInstance->edit($param);
                    }
                } elseif ($action === 'update' && $param && $method === 'POST') {
                    $controllerInstance->update($param);
                } elseif ($action === 'delete' && $param) {
                    $controllerInstance->delete($param);
                } elseif ($action === 'cambiar-estado' && $param && $method === 'POST') {
                    $controllerInstance->cambiarEstado($param);
                } elseif ($action === 'buscar' && $method === 'GET') {
                    $controllerInstance->buscar();
                } elseif ($action === 'reordenar' && $method === 'POST') {
                    $controllerInstance->reordenar();
                } elseif ($action === 'get-por-tipo' && $param && $method === 'GET') {
                    $controllerInstance->getPorTipo($param);
                } else {
                    $this->handle404("Acción '$action' no encontrada");
                }
            } elseif ($controller === 'empresa') {
                // Verificar que las clases existan antes de instanciar
                if (!class_exists('EmpresaController')) {
                    throw new Exception('EmpresaController no encontrado');
                }

                $controllerInstance = new EmpresaController();

                // Enrutar acciones
                if ($action === 'index' || empty($action)) {
                    $controllerInstance->index();
                } elseif ($action === 'update' && $method === 'POST') {
                    $controllerInstance->update();
                } elseif ($action === 'get-info' && $method === 'GET') {
                    $controllerInstance->getInfo();
                } else {
                    $this->handle404("Acción '$action' no encontrada");
                }
            } else {
                $this->handle404("Controlador '$controller' no encontrado");
            }

        } catch (Exception $e) {
            $this->handle500($e->getMessage());
        }
    }

    private function handle404($message = 'La página que buscas no existe.') {
        http_response_code(404);
        echo $this->renderErrorPage(404, 'Página no encontrada', $message);
    }

    private function handle500($message) {
        http_response_code(500);
        echo $this->renderErrorPage(500, 'Error interno', $message);
    }

    private function renderErrorPage($code, $title, $message) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Error {$code} - Sistema de Servicios MVC</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light d-flex align-items-center justify-content-center' style='min-height: 100vh;'>
            <div class='container'>
                <div class='row justify-content-center'>
                    <div class='col-md-8 text-center'>
                        <div class='card shadow'>
                            <div class='card-body p-5'>
                                <i class='fas fa-exclamation-triangle fa-4x text-warning mb-4'></i>
                                <h1 class='display-4 mb-3'>{$code}</h1>
                                <h3 class='mb-3'>{$title}</h3>
                                <div class='alert alert-warning text-start'>
                                    <strong>Detalle:</strong> {$message}
                                </div>
                                <a href='" . BASE_URL . "/inicio.php' class='btn btn-primary btn-lg me-2'>
                                    <i class='fas fa-home me-2'></i>Ir al Inicio
                                </a>
                                <a href='" . BASE_URL . "/index.php?route=servicios' class='btn btn-outline-primary btn-lg'>
                                    <i class='fas fa-list me-2'></i>Ver Servicios
                                </a>
                                <hr class='my-4'>
                                <div class='text-start'>
                                    <h5>🔗 URLs disponibles:</h5>
                                    <ul class='list-unstyled'>
                                        <li><a href='" . BASE_URL . "/inicio.php' class='text-decoration-none'>📊 Página de Inicio</a></li>
                                        <li><a href='" . BASE_URL . "/index.php?route=servicios' class='text-decoration-none'>📋 Lista de Servicios</a></li>
                                        <li><a href='" . BASE_URL . "/index.php?route=servicios/create' class='text-decoration-none'>➕ Crear Servicio</a></li>
                                        <li><a href='" . BASE_URL . "/index.php?route=auth/login' class='text-decoration-none'>🔐 Login</a></li>
                                    </ul>
                                    <small class='text-muted'>
                                        <em>💡 Nota: Usando parámetros GET - Solución consolidada</em>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
}

// Ejecutar el router con manejo seguro de errores
try {
    $router = new RouterPrincipal();
    $router->dispatch();
} catch (Exception $e) {
    http_response_code(500);
    echo "
    <div style='padding: 40px; text-align: center; font-family: Arial, sans-serif;'>
        <h1 style='color: #dc3545;'>🚨 Error del Sistema</h1>
        <div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px 0;'>
            <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "
        </div>
        <a href='" . BASE_URL . "/inicio.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>
            🏠 Volver al Inicio
        </a>
    </div>";
}

// Finalizar buffer de salida
ob_end_flush();
?>
