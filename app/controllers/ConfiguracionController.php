<?php
/**
 * ConfiguracionController - Controlador para gestión de configuración del sistema
 */
class ConfiguracionController extends BaseController {
    private $estadoServicioModel;
    private $tipoServicioModel;

    public function __construct() {
        $this->estadoServicioModel = new EstadoServicio();
        $this->tipoServicioModel = new TipoServicio();
    }

    /**
     * Página principal de configuración
     */
    public function index() {
        $this->render('configuracion/index');
    }

    /**
     * Gestión de estados de servicio
     */
    public function estados() {
        $estados = $this->estadoServicioModel->getAllOrdered();
        $stats = $this->estadoServicioModel->getUsageStats();

        $this->render('configuracion/estados', compact('estados', 'stats'));
    }

    /**
     * Crear nuevo estado
     */
    public function createEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();

            // Validación básica
            $required = ['Descripcion'];
            $errors = $this->validateRequired($data, $required);

            if (!empty($errors)) {
                $this->json(['success' => false, 'errors' => $errors], 400);
            }

            // Verificar si ya existe
            $existing = $this->estadoServicioModel->findByDescripcion($data['Descripcion']);
            if ($existing) {
                $this->json(['success' => false, 'message' => 'Ya existe un estado con esta descripción'], 400);
            }

            $id = $this->estadoServicioModel->create($data);

            if ($id) {
                $this->json(['success' => true, 'message' => 'Estado creado exitosamente', 'id' => $id]);
            } else {
                $this->json(['success' => false, 'message' => 'Error al crear el estado'], 500);
            }
        }

        $this->render('configuracion/create_estado');
    }

    /**
     * Editar estado
     */
    public function editEstado($id) {
        $estado = $this->estadoServicioModel->getById($id);

        if (!$estado) {
            $this->setFlash('error', 'Estado no encontrado');
            $this->redirect('configuracion/estados');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();

            // Validación básica
            $required = ['Descripcion'];
            $errors = $this->validateRequired($data, $required);

            if (!empty($errors)) {
                $this->json(['success' => false, 'errors' => $errors], 400);
            }

            // Verificar si ya existe otro con la misma descripción
            $existing = $this->estadoServicioModel->findByDescripcion($data['Descripcion']);
            if ($existing && $existing['IdEstadoEnTaller'] != $id) {
                $this->json(['success' => false, 'message' => 'Ya existe un estado con esta descripción'], 400);
            }

            $success = $this->estadoServicioModel->update($id, $data);

            if ($success) {
                $this->json(['success' => true, 'message' => 'Estado actualizado exitosamente']);
            } else {
                $this->json(['success' => false, 'message' => 'Error al actualizar el estado'], 500);
            }
        }

        $this->render('configuracion/edit_estado', compact('estado'));
    }

    /**
     * Eliminar estado
     */
    public function deleteEstado($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        // Verificar si se puede eliminar
        if (!$this->estadoServicioModel->canDelete($id)) {
            $this->json(['success' => false, 'message' => 'No se puede eliminar este estado porque tiene servicios asociados'], 400);
        }

        $success = $this->estadoServicioModel->safeDelete($id);

        if ($success) {
            $this->json(['success' => true, 'message' => 'Estado eliminado exitosamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al eliminar el estado'], 500);
        }
    }

    /**
     * Gestión de tipos de servicio
     */
    public function tiposServicio() {
        $tipos = $this->tipoServicioModel->getAllOrdered();
        $stats = $this->tipoServicioModel->getUsageStats();

        $this->render('configuracion/tipos_servicio', compact('tipos', 'stats'));
    }

    /**
     * Crear nuevo tipo de servicio
     */
    public function createTipoServicio() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();

            // Validación básica
            $required = ['Descripcion'];
            $errors = $this->validateRequired($data, $required);

            // Validar valor base si se proporciona
            if (!empty($data['CostoAproximado'])) {
                if (!is_numeric($data['CostoAproximado']) || $data['CostoAproximado'] < 0) {
                    $errors['CostoAproximado'] = 'El valor base debe ser un número positivo';
                }
            }

            if (!empty($errors)) {
                $this->json(['success' => false, 'errors' => $errors], 400);
            }

            // Verificar si ya existe
            $existing = $this->tipoServicioModel->findByDescripcion($data['Descripcion']);
            if ($existing) {
                $this->json(['success' => false, 'message' => 'Ya existe un tipo de servicio con esta descripción'], 400);
            }

            $id = $this->tipoServicioModel->create($data);

            if ($id) {
                $this->json(['success' => true, 'message' => 'Tipo de servicio creado exitosamente', 'id' => $id]);
            } else {
                $this->json(['success' => false, 'message' => 'Error al crear el tipo de servicio'], 500);
            }
        }

        $this->render('configuracion/create_tipo_servicio');
    }

    /**
     * Editar tipo de servicio
     */
    public function editTipoServicio($id) {
        $tipo = $this->tipoServicioModel->getById($id);

        if (!$tipo) {
            $this->setFlash('error', 'Tipo de servicio no encontrado');
            $this->redirect('configuracion/tipos-servicio');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();

            // Validación básica
            $required = ['Descripcion'];
            $errors = $this->validateRequired($data, $required);

            // Validar valor base si se proporciona
            if (!empty($data['CostoAproximado'])) {
                if (!is_numeric($data['CostoAproximado']) || $data['CostoAproximado'] < 0) {
                    $errors['CostoAproximado'] = 'El valor base debe ser un número positivo';
                }
            }

            if (!empty($errors)) {
                $this->json(['success' => false, 'errors' => $errors], 400);
            }

            // Verificar si ya existe otro con la misma descripción
            $existing = $this->tipoServicioModel->findByDescripcion($data['Descripcion']);
            if ($existing && $existing['IdTipoServicio'] != $id) {
                $this->json(['success' => false, 'message' => 'Ya existe un tipo de servicio con esta descripción'], 400);
            }

            $success = $this->tipoServicioModel->update($id, $data);

            if ($success) {
                $this->json(['success' => true, 'message' => 'Tipo de servicio actualizado exitosamente']);
            } else {
                $this->json(['success' => false, 'message' => 'Error al actualizar el tipo de servicio'], 500);
            }
        }

        $this->render('configuracion/edit_tipo_servicio', compact('tipo'));
    }

    /**
     * Eliminar tipo de servicio
     */
    public function deleteTipoServicio($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        // Verificar si se puede eliminar
        if (!$this->tipoServicioModel->canDelete($id)) {
            $this->json(['success' => false, 'message' => 'No se puede eliminar este tipo de servicio porque tiene servicios asociados'], 400);
        }

        $success = $this->tipoServicioModel->safeDelete($id);

        if ($success) {
            $this->json(['success' => true, 'message' => 'Tipo de servicio eliminado exitosamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al eliminar el tipo de servicio'], 500);
        }
    }

    /**
     * Crear datos por defecto
     */
    public function createDefaults() {
        $estadosCreados = $this->estadoServicioModel->createDefaultStates();
        $tiposCreados = $this->tipoServicioModel->createDefaultTypes();

        $this->json([
            'success' => true,
            'message' => 'Datos por defecto creados exitosamente',
            'estados_creados' => $estadosCreados,
            'tipos_creados' => $tiposCreados
        ]);
    }
}