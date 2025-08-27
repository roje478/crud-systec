<?php
/**
 * BaseController - Controlador base simplificado
 */

// Cargar configuración de headers
require_once __DIR__ . '/../../config/headers.php';

// Definir la URL base del sitio si no está definida
if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

// Funciones helper
if (!function_exists('url')) {
    function url($path = '') {
        return BASE_URL . '/index.php?route=' . ltrim($path, '/');
    }
}

abstract class BaseController {

    // Renderizar vista
    protected function render($view, $data = []) {
        // Extraer variables para la vista
        extract($data);

        // Incluir header
        include __DIR__ . '/../views/layouts/header.php';

        // Incluir vista específica
        include __DIR__ . "/../views/{$view}.php";

        // Incluir footer
        include __DIR__ . '/../views/layouts/footer.php';
    }

    // Respuesta JSON
    protected function json($data, $statusCode = 200) {
        safeJsonResponse($data, $statusCode);
    }

    // Redireccionar
    protected function redirect($url) {
        // Debug: Log de redirección
        error_log("BaseController::redirect() - URL: $url");

        // Si la URL no empieza con http, usar la función url() para construir la URL completa
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = url($url);
        }
        error_log("BaseController::redirect() - URL final: $url");

        safeRedirect($url);
    }

    // Obtener datos POST (incluye JSON)
    protected function getPostData() {
        // Debug: mostrar información de la petición
        error_log("BaseController::getPostData() - Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'no definido'));
        error_log("BaseController::getPostData() - Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'no definido'));
        error_log("BaseController::getPostData() - POST data: " . json_encode($_POST));

        // Si el contenido es JSON, decodificarlo
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $jsonData = file_get_contents('php://input');
            error_log("BaseController::getPostData() - JSON raw data: " . $jsonData);
            $data = json_decode($jsonData, true);
            error_log("BaseController::getPostData() - JSON decoded data: " . json_encode($data));
            return $data ?: [];
        }

        // Si no es JSON, devolver $_POST normal
        error_log("BaseController::getPostData() - Retornando Array: " . json_encode($_POST));
        return $_POST ?: [];
    }

    // Validar campos requeridos
    protected function validateRequired($data, $required) {
        $errors = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = "El campo $field es requerido";
            }
        }
        return $errors;
    }

    // Mensaje flash
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    // Obtener mensaje flash
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
?>
