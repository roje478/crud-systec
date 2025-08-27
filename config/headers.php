<?php
/**
 * Configuración de Headers y Output Buffering
 * Maneja problemas de "headers already sent"
 */

// Función para limpiar cualquier salida anterior
function cleanOutput() {
    while (ob_get_level()) {
        ob_end_clean();
    }
}

// Función para verificar si se pueden enviar headers
function canSendHeaders() {
    return !headers_sent();
}

// Función para enviar headers de forma segura
function safeHeader($header, $replace = true, $http_response_code = 0) {
    if (canSendHeaders()) {
        header($header, $replace, $http_response_code);
        return true;
    }
    return false;
}

// Función para redirección segura
function safeRedirect($url) {
    if (canSendHeaders()) {
        header("Location: $url");
        exit;
    } else {
        // Fallback con JavaScript
        echo "<script>window.location.href = '$url';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=$url'></noscript>";
        exit;
    }
}

// Función para respuesta JSON segura
function safeJsonResponse($data, $statusCode = 200) {
    cleanOutput();

    if (canSendHeaders()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Configuración inicial
if (!headers_sent()) {
    // Configurar headers básicos
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
}
?>
