<?php
/**
 * UsuarioController - Controlador de usuarios del sistema
 */
class UsuarioController extends BaseController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    // Listar usuarios
    public function index() {
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 20;

        // Obtener usuarios con paginación
        $usuarios = $this->usuarioModel->getAllWithDetailsPaginated($page, $perPage);

        // Obtener información de paginación
        $pagination = $this->usuarioModel->getPaginationInfo($page, $perPage);

        // Obtener estadísticas
        $estadisticas = $this->usuarioModel->getEstadisticas();

        $this->render('usuarios/index', compact('usuarios', 'pagination', 'estadisticas'));
    }

    // Mostrar formulario de creación
    public function create() {
        $perfiles = $this->usuarioModel->getPerfiles();

        $this->render('usuarios/create', compact('perfiles'));
    }

    // Crear usuario
    public function store() {
        $data = $this->getPostData();

        // Validación básica
        $required = ['nombres', 'apellidos', 'no_identificacion', 'codigo_perfil', 'contrasenia'];
        $errors = $this->validateRequired($data, $required);

        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
        }

        // Validar contraseña
        if (strlen($data['contrasenia']) < 6) {
            $this->json(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
        }

        // Preparar datos para el modelo
        $usuarioData = [
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'no_identificacion' => $data['no_identificacion'],
            'direccion' => $data['direccion'] ?? '',
            'telefono' => $data['telefono'] ?? '',
            'codigo_perfil' => $data['codigo_perfil'],
            'contrasenia' => $data['contrasenia'],
            'activo' => $data['activo'] ?? 1
        ];

        $id = $this->usuarioModel->create($usuarioData);

        if ($id) {
            $this->json(['success' => true, 'message' => 'Usuario creado exitosamente', 'id' => $id]);
        } else {
            $this->json(['success' => false, 'message' => 'Error al crear el usuario'], 500);
        }
    }

    // Ver detalles del usuario
    public function view($id) {
        $usuario = $this->usuarioModel->getByIdWithDetails($id);
        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado');
            $this->redirect(BASE_URL . '?route=usuarios');
        }

        $this->render('usuarios/view', compact('usuario'));
    }

    // Mostrar formulario de edición
    public function edit($id) {
        $usuario = $this->usuarioModel->getByIdWithDetails($id);
        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado');
            $this->redirect(BASE_URL . '?route=usuarios');
        }

        $perfiles = $this->usuarioModel->getPerfiles();

        $this->render('usuarios/edit', compact('usuario', 'perfiles'));
    }

    // Mostrar perfil del usuario actual
    public function profile() {
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['usuario_id'])) {
            $this->setFlash('error', 'Debes iniciar sesión para acceder a tu perfil');
            $this->redirect(BASE_URL . '?route=auth/login');
        }

        $usuarioId = $_SESSION['usuario_id'];
        $usuario = $this->usuarioModel->getByIdWithDetails($usuarioId);

        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado');
            $this->redirect(BASE_URL . '?route=servicios/buscar');
        }

        $this->render('usuarios/profile', compact('usuario'));
    }

    // Actualizar perfil del usuario actual
    public function updateProfile() {
        try {
            error_log("UsuarioController::updateProfile() - Iniciando actualización de perfil");
            
            // Verificar que el usuario esté autenticado
            if (!isset($_SESSION['usuario_id'])) {
                error_log("UsuarioController::updateProfile() - Usuario no autenticado");
                $this->json(['success' => false, 'message' => 'Debes iniciar sesión'], 401);
                return;
            }

            $usuarioId = $_SESSION['usuario_id'];
            $data = $this->getPostData();
            
            error_log("UsuarioController::updateProfile() - Datos recibidos: " . json_encode($data));

            // Validación básica
            $required = ['nombres', 'apellidos', 'telefono'];
            $errors = $this->validateRequired($data, $required);

            if (!empty($errors)) {
                error_log("UsuarioController::updateProfile() - Errores de validación: " . json_encode($errors));
                $this->json(['success' => false, 'errors' => $errors], 400);
                return;
            }

            // Validar contraseña si se proporciona
            if (!empty($data['contrasenia'])) {
                if (strlen($data['contrasenia']) < 6) {
                    $this->json(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
                    return;
                }

                // Validar confirmación de contraseña
                if (empty($data['confirmar_contrasenia'])) {
                    $this->json(['success' => false, 'message' => 'Debes confirmar la contraseña'], 400);
                    return;
                }

                if ($data['contrasenia'] !== $data['confirmar_contrasenia']) {
                    $this->json(['success' => false, 'message' => 'Las contraseñas no coinciden'], 400);
                    return;
                }
            }

            // Preparar datos para el modelo
            $usuarioData = [
                'nombres' => trim($data['nombres']),
                'apellidos' => trim($data['apellidos']),
                'direccion' => trim($data['direccion'] ?? ''),
                'telefono' => trim($data['telefono'])
            ];

            // Agregar contraseña solo si se proporciona
            if (!empty($data['contrasenia'])) {
                $usuarioData['contrasenia'] = $data['contrasenia'];
            }

            error_log("UsuarioController::updateProfile() - Datos a enviar al modelo: " . json_encode($usuarioData));

            $result = $this->usuarioModel->update($usuarioId, $usuarioData);

            if ($result) {
                error_log("UsuarioController::updateProfile() - Actualización exitosa");
                
                // Actualizar todas las variables de sesión relacionadas con el nombre
                if (isset($data['nombres']) || isset($data['apellidos'])) {
                    $nombres = $data['nombres'] ?? $_SESSION['usuario_nombres'] ?? '';
                    $apellidos = $data['apellidos'] ?? $_SESSION['usuario_apellidos'] ?? '';

                    // Actualizar todas las variables de sesión del nombre
                    $_SESSION['usuario_nombres'] = trim($nombres);
                    $_SESSION['usuario_apellidos'] = trim($apellidos);
                    $_SESSION['usuario_nombre_completo'] = trim($nombres . ' ' . $apellidos);

                    // Mantener compatibilidad con la variable anterior
                    $_SESSION['usuario_nombre'] = $_SESSION['usuario_nombre_completo'];
                }

                $this->json(['success' => true, 'message' => 'Perfil actualizado exitosamente']);
            } else {
                error_log("UsuarioController::updateProfile() - Error en actualización");
                $this->json(['success' => false, 'message' => 'Error al actualizar el perfil'], 500);
            }
        } catch (Exception $e) {
            error_log("UsuarioController::updateProfile() - Excepción: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    // Actualizar usuario
    public function update($id) {
        try {
            error_log("UsuarioController::update() - Iniciando actualización para ID: $id");
            
            $data = $this->getPostData();
            error_log("UsuarioController::update() - Datos recibidos: " . json_encode($data));

            // Verificar que el usuario existe
            $usuarioExistente = $this->usuarioModel->getById($id);
            if (!$usuarioExistente) {
                error_log("UsuarioController::update() - Usuario no encontrado: $id");
                $this->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
                return;
            }

            // Validación básica
            $required = ['codigo_perfil', 'nombres', 'apellidos'];
            $errors = $this->validateRequired($data, $required);

            if (!empty($errors)) {
                error_log("UsuarioController::update() - Errores de validación: " . json_encode($errors));
                $this->json(['success' => false, 'errors' => $errors], 400);
                return;
            }

            // Validar contraseña si se proporciona
            if (isset($data['contrasenia']) && !empty($data['contrasenia'])) {
                if (strlen($data['contrasenia']) < 6) {
                    $this->json(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
                    return;
                }
            }

            // Preparar datos para el modelo
            $usuarioData = [
                'codigo_perfil' => $data['codigo_perfil'],
                'nombres' => trim($data['nombres']),
                'apellidos' => trim($data['apellidos']),
                'telefono' => trim($data['telefono'] ?? ''),
                'direccion' => trim($data['direccion'] ?? ''),
                'activo' => $data['activo'] ?? 1
            ];

            // Agregar contraseña solo si se proporciona
            if (isset($data['contrasenia']) && !empty($data['contrasenia'])) {
                $usuarioData['contrasenia'] = $data['contrasenia'];
            }

            error_log("UsuarioController::update() - Datos a enviar al modelo: " . json_encode($usuarioData));

            $result = $this->usuarioModel->update($id, $usuarioData);

            if ($result) {
                error_log("UsuarioController::update() - Actualización exitosa");
                
                // Actualizar sesión si es el usuario actual
                if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
                    $_SESSION['usuario_nombres'] = $usuarioData['nombres'];
                    $_SESSION['usuario_apellidos'] = $usuarioData['apellidos'];
                    $_SESSION['usuario_nombre_completo'] = trim($usuarioData['nombres'] . ' ' . $usuarioData['apellidos']);
                }
                
                $this->json(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
            } else {
                error_log("UsuarioController::update() - Error en actualización");
                $this->json(['success' => false, 'message' => 'Error al actualizar el usuario'], 500);
            }
        } catch (Exception $e) {
            error_log("UsuarioController::update() - Excepción: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    // Cambiar estado de usuario
    public function changeStatus($id) {
        try {
            error_log("UsuarioController::changeStatus() - Iniciando cambio de estado para ID: $id");
            
            // Verificar que el usuario existe
            $usuarioExistente = $this->usuarioModel->getById($id);
            if (!$usuarioExistente) {
                error_log("UsuarioController::changeStatus() - Usuario no encontrado: $id");
                if ($this->isAjaxRequest()) {
                    $this->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
                } else {
                    $this->setFlash('error', 'Usuario no encontrado');
                    $this->redirect('usuarios');
                }
                return;
            }
            
            // Verificar si es el usuario actual (no permitir auto-desactivación)
            $usuarioActual = $_SESSION['usuario_id'] ?? null;
            if ($usuarioActual == $id) {
                error_log("UsuarioController::changeStatus() - Intento de auto-desactivación: $id");
                if ($this->isAjaxRequest()) {
                    $this->json([
                        'success' => false,
                        'message' => 'No puedes desactivar tu propia cuenta'
                    ], 400);
                } else {
                    $this->setFlash('error', 'No puedes desactivar tu propia cuenta');
                    $this->redirect('usuarios');
                }
                return;
            }
            
            $result = $this->usuarioModel->changeStatus($id);

            if ($result) {
                error_log("UsuarioController::changeStatus() - Estado cambiado exitosamente");
                if ($this->isAjaxRequest()) {
                    $this->json(['success' => true, 'message' => 'Estado del usuario cambiado exitosamente']);
                } else {
                    $this->setFlash('success', 'Estado del usuario cambiado exitosamente');
                    $this->redirect('usuarios');
                }
            } else {
                error_log("UsuarioController::changeStatus() - Error en cambio de estado");
                if ($this->isAjaxRequest()) {
                    $this->json(['success' => false, 'message' => 'Error al cambiar el estado del usuario'], 500);
                } else {
                    $this->setFlash('error', 'Error al cambiar el estado del usuario');
                    $this->redirect('usuarios');
                }
            }
        } catch (Exception $e) {
            error_log("UsuarioController::changeStatus() - Excepción: " . $e->getMessage());
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
            } else {
                $this->setFlash('error', 'Error interno del servidor');
                $this->redirect('usuarios');
            }
        }
    }

    // Verificar si es una petición AJAX
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }



    // Obtener estadísticas (AJAX)
    public function getEstadisticas() {
        $estadisticas = $this->usuarioModel->getEstadisticas();
        $this->json($estadisticas);
    }

    // Eliminar usuario
    public function delete($id) {
        try {
            // Verificar si el usuario existe
            $usuario = $this->usuarioModel->getById($id);
            if (!$usuario) {
                $this->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
                return;
            }

            // Verificar si es el usuario actual (no permitir auto-eliminación)
            $usuarioActual = $_SESSION['usuario_id'] ?? null;
            if ($usuarioActual == $id) {
                $this->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta'
                ], 400);
                return;
            }

            // Eliminar el usuario
            if ($this->usuarioModel->delete($id)) {
                $this->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
            } else {
                $this->json(['success' => false, 'message' => 'Error al eliminar el usuario'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
}