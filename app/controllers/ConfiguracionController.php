<?php
/**
 * ConfiguracionController - Controlador para gestión de configuración del sistema
 */
class ConfiguracionController extends BaseController {
    private $estadoServicioModel;
    private $tipoServicioModel;
    private $motivoExternoModel = null;

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

    // ------------------------------------------------------------------
    // Motivos de Orden Externa (catálogo del módulo de Técnicos Externos)
    // ------------------------------------------------------------------

    /**
     * Modelo de motivos, cargado bajo demanda
     */
    private function getMotivoExternoModel() {
        if ($this->motivoExternoModel === null) {
            $this->motivoExternoModel = new MotivoOrdenExterna();
        }
        return $this->motivoExternoModel;
    }

    /**
     * Listado de motivos
     */
    public function motivosExternos() {
        $motivos = $this->getMotivoExternoModel()->getAllOrdered();
        $stats = $this->getMotivoExternoModel()->getUsageStats();
        $colores = MotivoOrdenExterna::getColoresDisponibles();

        $this->render('configuracion/motivos_externos', compact('motivos', 'stats', 'colores'));
    }

    /**
     * Crear un motivo nuevo
     */
    public function createMotivoExterno() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();

            $required = ['descripcion'];
            $errors = $this->validateRequired($data, $required);

            if (!empty($errors)) {
                $this->json(['success' => false, 'errors' => $errors], 400);
            }

            if ($this->getMotivoExternoModel()->findByDescripcion($data['descripcion'])) {
                $this->json(['success' => false, 'message' => 'Ya existe un motivo con esa descripción'], 400);
            }

            $id = $this->getMotivoExternoModel()->create([
                'descripcion' => trim($data['descripcion']),
                'color'       => $data['color'] ?? 'secondary',
                'orden'       => isset($data['orden']) && $data['orden'] !== ''
                                    ? (int)$data['orden']
                                    : $this->getMotivoExternoModel()->getSiguienteOrden(),
                'activo'      => isset($data['activo']) && $data['activo'] ? 1 : 0
            ]);

            if ($id) {
                $this->json(['success' => true, 'message' => 'Motivo creado exitosamente', 'id' => $id]);
            }

            $this->json(['success' => false, 'message' => 'Error al crear el motivo'], 500);
        }

        $colores = MotivoOrdenExterna::getColoresDisponibles();
        $siguienteOrden = $this->getMotivoExternoModel()->getSiguienteOrden();

        $this->render('configuracion/create_motivo_externo', compact('colores', 'siguienteOrden'));
    }

    /**
     * Editar un motivo
     */
    public function editMotivoExterno($id) {
        $motivo = $this->getMotivoExternoModel()->getById($id);

        if (!$motivo) {
            $this->setFlash('error', 'Motivo no encontrado');
            $this->redirect('configuracion/motivos-externos');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();

            $required = ['descripcion'];
            $errors = $this->validateRequired($data, $required);

            if (!empty($errors)) {
                $this->json(['success' => false, 'errors' => $errors], 400);
            }

            $existente = $this->getMotivoExternoModel()->findByDescripcion($data['descripcion']);
            if ($existente && (int)$existente['id'] !== (int)$id) {
                $this->json(['success' => false, 'message' => 'Ya existe un motivo con esa descripción'], 400);
            }

            $success = $this->getMotivoExternoModel()->update($id, [
                'descripcion' => trim($data['descripcion']),
                'color'       => $data['color'] ?? 'secondary',
                'orden'       => isset($data['orden']) && $data['orden'] !== '' ? (int)$data['orden'] : 0,
                'activo'      => isset($data['activo']) && $data['activo'] ? 1 : 0
            ]);

            if ($success) {
                $this->json(['success' => true, 'message' => 'Motivo actualizado exitosamente']);
            }

            $this->json(['success' => false, 'message' => 'Error al actualizar el motivo'], 500);
        }

        $colores = MotivoOrdenExterna::getColoresDisponibles();
        $totalOrdenes = $this->getMotivoExternoModel()->contarOrdenes($id);

        $this->render('configuracion/edit_motivo_externo', compact('motivo', 'colores', 'totalOrdenes'));
    }

    /**
     * Eliminar un motivo (solo si no tiene órdenes asociadas)
     */
    public function deleteMotivoExterno($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        if (!$this->getMotivoExternoModel()->canDelete($id)) {
            $this->json([
                'success' => false,
                'message' => 'No se puede eliminar este motivo porque tiene órdenes asociadas'
            ], 400);
        }

        if ($this->getMotivoExternoModel()->safeDelete($id)) {
            $this->json(['success' => true, 'message' => 'Motivo eliminado exitosamente']);
        }

        $this->json(['success' => false, 'message' => 'Error al eliminar el motivo'], 500);
    }

    /**
     * Activar / desactivar un motivo
     */
    public function toggleMotivoExterno($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        if (!$this->getMotivoExternoModel()->getById($id)) {
            $this->json(['success' => false, 'message' => 'Motivo no encontrado'], 404);
        }

        if ($this->getMotivoExternoModel()->toggleEstado($id)) {
            $motivo = $this->getMotivoExternoModel()->getById($id);
            $this->json([
                'success' => true,
                'activo'  => (int)$motivo['activo'],
                'message' => (int)$motivo['activo'] === 1 ? 'Motivo activado' : 'Motivo desactivado'
            ]);
        }

        $this->json(['success' => false, 'message' => 'Error al cambiar el estado'], 500);
    }
}
