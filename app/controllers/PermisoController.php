<?php
/**
 * PermisoController - Gestión de permisos y opciones del sistema
 */
class PermisoController extends BaseController {
    private $permisoModel;
    private $usuarioModel;

    public function __construct() {
        $this->permisoModel = new Permiso();
        $this->usuarioModel = new Usuario();
    }

    // Listar permisos por perfil
    public function index() {
        $perfiles = $this->usuarioModel->getPerfiles();

        $this->render('permisos/index', compact('perfiles'));
    }

    // Mostrar formulario de asignación de permisos
    public function asignar($perfilId = null) {
        // Si no se proporciona perfilId, mostrar lista de perfiles
        if (!$perfilId) {
            $perfiles = $this->usuarioModel->getPerfiles();

            $this->render('permisos/index', compact('perfiles'));
            return;
        }

        $perfil = $this->usuarioModel->getPerfilById($perfilId);
        if (!$perfil) {
            $this->setFlash('error', 'Perfil no encontrado');
            // Limpiar cualquier salida anterior antes de redirigir
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->redirect(url('permisos'));
        }

        $opciones = $this->permisoModel->getAllOpciones();
        $opcionesAsignadas = $this->permisoModel->getOpcionesByPerfil($perfilId);

        // Crear array de códigos asignados para el checkbox
        $codigosAsignados = array_column($opcionesAsignadas, 'codigo');

        $this->render('permisos/asignar', compact('perfil', 'opciones', 'codigosAsignados'));
    }

    // Guardar asignación de permisos (versión mejorada)
    public function guardarAsignacion($perfilId) {
        $data = $this->getPostData();
        $opciones = $data['opciones'] ?? [];
        $menusPrincipales = $data['menus_principales'] ?? [];

        // Combinar subopciones con menús principales
        $todasLasOpciones = array_merge($opciones, $menusPrincipales);

        // Usar el método más seguro que actualiza sin eliminar todos los permisos
        $resultado = $this->permisoModel->actualizarOpciones($perfilId, $todasLasOpciones);

        if ($resultado) {
            $this->setFlash('success', 'Permisos actualizados correctamente');
            // Limpiar cualquier salida anterior antes de redirigir
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->redirect('permisos');
        } else {
            $this->setFlash('error', 'Error al actualizar permisos');
            // Limpiar cualquier salida anterior antes de redirigir
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->redirect('permisos/asignar/' . $perfilId);
        }
    }

    // Verificar permiso de usuario
    public function verificarPermiso($usuarioId, $opcion) {
        $tienePermiso = $this->permisoModel->tienePermiso($usuarioId, $opcion);

        // Limpiar cualquier salida anterior antes de enviar JSON
        if (ob_get_level()) {
            ob_end_clean();
        }

        $this->json([
            'success' => true,
            'tiene_permiso' => $tienePermiso,
            'usuario_id' => $usuarioId,
            'opcion' => $opcion
        ]);
    }

    // Obtener menú dinámico para usuario
    public function getMenuUsuario($usuarioId) {
        $menu = $this->permisoModel->getMenuByUsuario($usuarioId);

        // Limpiar cualquier salida anterior antes de enviar JSON
        if (ob_get_level()) {
            ob_end_clean();
        }

        $this->json([
            'success' => true,
            'menu' => $menu
        ]);
    }

    // Vista de gestión de opciones
    public function opciones() {
        $opciones = $this->permisoModel->getAllOpciones();

        $this->render('permisos/opciones', compact('opciones'));
    }

    // Crear nueva opción
    public function crearOpcion() {
        $data = $this->getPostData();

        // Validación básica
        $required = ['codigo', 'descripcion'];
        $errors = $this->validateRequired($data, $required);

        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
        }

        // Aquí se implementaría la lógica para crear opciones
        // Por ahora solo retornamos éxito
        $this->json(['success' => true, 'message' => 'Opción creada correctamente']);
    }



    // Mostrar formulario para crear perfil
    public function create() {
        $this->render('permisos/create_perfil');
    }

    // Guardar nuevo perfil
    public function store() {
        $data = $this->getPostData();

        // Validar datos requeridos
        $required = ['descripcion'];
        $errors = $this->validateRequired($data, $required);

        if (!empty($errors)) {
            $this->setFlash('error', 'Por favor complete todos los campos requeridos');
            $this->redirect('permisos/create');
            return;
        }

        try {
            // Crear el perfil
            $perfilId = $this->usuarioModel->createPerfil($data);

            if ($perfilId) {
                $this->setFlash('success', 'Perfil creado correctamente. Ahora puede asignar permisos.');
                // Limpiar cualquier salida anterior antes de redirigir
                if (ob_get_level()) {
                    ob_end_clean();
                }
                $this->redirect('permisos/asignar/' . $perfilId);
            } else {
                $this->setFlash('error', 'Error al crear el perfil');
                // Limpiar cualquier salida anterior antes de redirigir
                if (ob_get_level()) {
                    ob_end_clean();
                }
                $this->redirect('permisos/create');
            }
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            // Limpiar cualquier salida anterior antes de redirigir
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->redirect('permisos/create');
        }
    }

    // Mostrar formulario para editar perfil
    public function edit($perfilId) {
        $perfil = $this->usuarioModel->getPerfilById($perfilId);

        if (!$perfil) {
            $this->setFlash('error', 'Perfil no encontrado');
            // Limpiar cualquier salida anterior antes de redirigir
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->redirect('permisos');
            return;
        }

        $this->render('permisos/edit_perfil', compact('perfil'));
    }

    // Actualizar perfil existente
    public function update($perfilId) {
        $data = $this->getPostData();

        // Validar datos requeridos
        $required = ['descripcion'];
        $errors = $this->validateRequired($data, $required);

        if (!empty($errors)) {
            // Limpiar cualquier salida anterior antes de enviar JSON
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->json([
                'success' => false,
                'message' => 'Por favor complete todos los campos requeridos'
            ], 400);
            return;
        }

        try {
            // Actualizar el perfil
            $resultado = $this->usuarioModel->updatePerfil($perfilId, $data);

            // Limpiar cualquier salida anterior antes de enviar JSON
            if (ob_get_level()) {
                ob_end_clean();
            }

            if ($resultado) {
                $this->json([
                    'success' => true,
                    'message' => 'Perfil actualizado correctamente'
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Error al actualizar el perfil'
                ], 500);
            }
        } catch (Exception $e) {
            // Limpiar cualquier salida anterior antes de enviar JSON
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar perfil
    public function delete($perfilId) {
        try {
            // Verificar si se puede eliminar
            if (!$this->usuarioModel->canDeletePerfil($perfilId)) {
                // Limpiar cualquier salida anterior antes de enviar JSON
                if (ob_get_level()) {
                    ob_end_clean();
                }
                $this->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el perfil porque tiene usuarios asignados'
                ], 400);
                return;
            }

            // Eliminar el perfil
            $resultado = $this->usuarioModel->deletePerfil($perfilId);

            // Limpiar cualquier salida anterior antes de enviar JSON
            if (ob_get_level()) {
                ob_end_clean();
            }

            if ($resultado) {
                $this->json([
                    'success' => true,
                    'message' => 'Perfil eliminado correctamente'
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Error al eliminar el perfil'
                ], 500);
            }
        } catch (Exception $e) {
            // Limpiar cualquier salida anterior antes de enviar JSON
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}