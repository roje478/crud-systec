<?php
/**
 * AuthController - Controlador para autenticación de usuarios
 */
class AuthController extends BaseController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    // Mostrar formulario de login
    public function login() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_activo']) && $_SESSION['usuario_activo'] === true) {
            $this->redirect('servicios/buscar');
        }

        // Renderizar vista de login sin header/footer
        $this->renderLogin('auth/login');
    }

    // Renderizar vista de login sin layout
    protected function renderLogin($view, $data = []) {
        // Extraer variables para la vista
        extract($data);

        // Incluir solo la vista específica
        include __DIR__ . "/../views/{$view}.php";
    }

    // Procesar login
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
        }

        $identificacion = $_POST['identificacion'] ?? '';
        $contrasenia = $_POST['contrasenia'] ?? '';

        if (empty($identificacion) || empty($contrasenia)) {
            $this->setFlash('error', 'Por favor ingresa tu identificación y contraseña');
            $this->redirect('auth/login');
        }

        // Verificar usuario en la base de datos
        $usuario = $this->usuarioModel->getById($identificacion);

        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado');
            $this->redirect('auth/login');
        }

        // Verificar contraseña (MD5 por compatibilidad con sistema existente)
        $contraseniaHash = md5($contrasenia);

        if ($usuario['contrasenia'] !== $contraseniaHash) {
            $this->setFlash('error', 'Contraseña incorrecta');
            $this->redirect('auth/login');
        }

        // Verificar que el usuario esté activo
        if (!$usuario['activo']) {
            $this->setFlash('error', 'Tu cuenta está deshabilitada');
            $this->redirect('auth/login');
        }

        // Obtener información del perfil
        $perfil = $this->usuarioModel->getPerfilById($usuario['codigo_perfil']);

        // Obtener información del cliente (nombres y apellidos)
        $usuarioCompleto = $this->usuarioModel->getByIdWithDetails($usuario['no_identificacion']);

        // Crear sesión
        $_SESSION['usuario_id'] = $usuario['no_identificacion'];
        $_SESSION['usuario_nombres'] = $usuarioCompleto['nombres'] ?? '';
        $_SESSION['usuario_apellidos'] = $usuarioCompleto['apellidos'] ?? '';
        $_SESSION['usuario_nombre_completo'] = trim(($usuarioCompleto['nombres'] ?? '') . ' ' . ($usuarioCompleto['apellidos'] ?? ''));
        $_SESSION['usuario_perfil'] = $usuario['codigo_perfil'];
        $_SESSION['usuario_perfil_nombre'] = $perfil['descripcion'] ?? 'Sin Perfil';
        $_SESSION['usuario_activo'] = true;

        $this->setFlash('success', '¡Bienvenido al sistema!');
        $this->redirect('servicios/buscar');
    }

    // Cerrar sesión
    public function logout() {
        // Destruir sesión
        session_destroy();

        $this->setFlash('success', 'Has cerrado sesión correctamente');
        $this->redirect('auth/login');
    }

    // Verificar si está autenticado
    public static function isAuthenticated() {
        return isset($_SESSION['usuario_id']) && $_SESSION['usuario_activo'] === true;
    }

    // Obtener usuario actual
    public static function getCurrentUser() {
        if (!self::isAuthenticated()) {
            return null;
        }

        $usuarioModel = new Usuario();
        return $usuarioModel->getById($_SESSION['usuario_id']);
    }

    // Obtener perfil actual
    public static function getCurrentProfile() {
        if (!self::isAuthenticated()) {
            return null;
        }

        return $_SESSION['usuario_perfil'] ?? null;
    }
}