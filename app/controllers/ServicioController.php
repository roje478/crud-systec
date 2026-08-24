<?php
/**
 * ServicioController - Controlador de servicios simplificado
 */
class ServicioController extends BaseController {
    private $servicioModel;

    public function __construct() {
        $this->servicioModel = new Servicio();
    }

    // Listar servicios con DataTables
    public function index() {
        // Verificar si el usuario es técnico
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        // Verificar si el usuario es técnico administrador
        $esTecnicoAdministrador = !empty($perfilNombre) && 
                                (strtolower(trim($perfilNombre)) === 'técnico administrador' || 
                                 strtolower(trim($perfilNombre)) === 'tecnico administrador');

        // Verificar si el usuario es asesor
        $esAsesor = !empty($perfilNombre) && 
                   strtolower(trim($perfilNombre)) === 'asesor';


        if ($esTecnico) {
            // Para técnicos, obtener solo los servicios asignados a ellos
            $tecnicoId = $_SESSION['usuario_id'] ?? null;
            if ($tecnicoId) {
                $servicios = $this->servicioModel->getServiciosByTecnico($tecnicoId);
            } else {
                $servicios = [];
            }
        } else {
            // Para otros usuarios, obtener todos los servicios
            $servicios = $this->servicioModel->getAllWithDetails();
        }

        // Obtener estados disponibles para los dropdowns
        $estados = $this->servicioModel->getEstados();

        // Renderizar vista con los datos
        $this->render('servicios/index', compact('servicios', 'estados', 'esTecnico', 'esTecnicoAdministrador', 'esAsesor'));
    }

    // Lista completa de servicios - TODOS los servicios sin límite con paginación de DataTables
    public function listaCompleta() {
        // Verificar si el usuario es técnico
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        // Verificar si el usuario es asesor
        $esAsesor = !empty($perfilNombre) && 
                   strtolower(trim($perfilNombre)) === 'asesor';

        // Obtener TODOS los servicios sin límite (DataTables manejará la paginación)
        $servicios = $this->servicioModel->getAllServiciosCompletos();

        // Obtener total de servicios
        $totalServicios = count($servicios);

        // Obtener estados disponibles para los dropdowns
        $estados = $this->servicioModel->getEstados();

        // Renderizar vista con los datos
        $this->render('servicios/lista_completa', compact(
            'servicios',
            'estados',
            'totalServicios',
            'esTecnico',
            'esAsesor'
        ));
    }

    // Mostrar formulario de selección de cliente
    public function selectCliente() {
        $clientes = $this->servicioModel->getClientes();
        $this->render('servicios/select_cliente', compact('clientes'));
    }

    // Mostrar vista de búsqueda de servicios (versión mejorada)
    public function buscar() {
        // Verificar si el usuario es técnico
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        // Verificar si el usuario es técnico administrador
        $esTecnicoAdministrador = !empty($perfilNombre) && 
                                (strtolower(trim($perfilNombre)) === 'técnico administrador' || 
                                 strtolower(trim($perfilNombre)) === 'tecnico administrador');

        // Verificar si el usuario es asesor
        $esAsesor = !empty($perfilNombre) && 
                   strtolower(trim($perfilNombre)) === 'asesor';

        // Obtener estados disponibles para los dropdowns
        $estados = $this->servicioModel->getEstados();

        // Inicializar array vacío de servicios (se llenará con búsqueda)
        $servicios = [];

        // Usar la vista mejorada en lugar de la original
        $this->render('servicios/buscar_mejorada', compact('servicios', 'estados', 'esTecnico', 'esTecnicoAdministrador', 'esAsesor'));
    }

    // Mostrar vista de búsqueda simplificada para diagnóstico
    public function buscarSimple() {
        // Verificar si el usuario es técnico
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        // Verificar si el usuario es asesor
        $esAsesor = !empty($perfilNombre) && 
                   strtolower(trim($perfilNombre)) === 'asesor';

        // Obtener estados disponibles para los dropdowns
        $estados = $this->servicioModel->getEstados();

        // Inicializar array vacío de servicios (se llenará con búsqueda)
        $servicios = [];

        $this->render('servicios/buscar_simple', compact('servicios', 'estados', 'esTecnico', 'esAsesor'));
    }

    // Mostrar vista de búsqueda mejorada (versión final)
    public function buscarMejorada() {
        // Verificar si el usuario es técnico
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        // Verificar si el usuario es técnico administrador
        $esTecnicoAdministrador = !empty($perfilNombre) && 
                                (strtolower(trim($perfilNombre)) === 'técnico administrador' || 
                                 strtolower(trim($perfilNombre)) === 'tecnico administrador');

        // Verificar si el usuario es asesor
        $esAsesor = !empty($perfilNombre) && 
                   strtolower(trim($perfilNombre)) === 'asesor';

        // Obtener estados disponibles para los dropdowns
        $estados = $this->servicioModel->getEstados();

        // Inicializar array vacío de servicios (se llenará con búsqueda)
        $servicios = [];

        $this->render('servicios/buscar_mejorada', compact('servicios', 'estados', 'esTecnico', 'esTecnicoAdministrador', 'esAsesor'));
    }

    // Mostrar formulario de creación
    public function create() {
        // Verificar si se pasó un cliente_id por URL
        $clienteId = $_GET['cliente_id'] ?? null;

        if (!$clienteId) {
            // Si no hay cliente seleccionado, redirigir a selección de cliente
            $this->redirect('servicios/select-cliente');
        }

        // Obtener información del cliente seleccionado
        $clienteSeleccionado = $this->servicioModel->getClienteById($clienteId);
        if (!$clienteSeleccionado) {
            $this->setFlash('error', 'Cliente no encontrado');
            $this->redirect('servicios/select-cliente');
        }

        $clientes = $this->servicioModel->getClientes();
        $estados = $this->servicioModel->getEstados();
        $tiposServicio = $this->servicioModel->getTiposServicio();
        $tecnicos = $this->servicioModel->getTecnicos();

        $this->render('servicios/create', compact('clientes', 'estados', 'tiposServicio', 'tecnicos', 'clienteSeleccionado'));
    }

    // Guardar servicio
    public function store() {
        $data = $this->getPostData();

        // Validación básica
        $required = ['idcliente', 'IdTipoServicio', 'equipo', 'problema', 'IdEstadoEnTaller'];
        $errors = $this->validateRequired($data, $required);

        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
        }

        // Preparar datos para el modelo
        $servicioData = [
            'idcliente' => $data['idcliente'],
            'NoIdentificacionEmpleado' => ($data['NoIdentificacionEmpleado'] === 'sin_asignar') ? null : ($data['NoIdentificacionEmpleado'] ?? null),
            'IdTipoServicio' => $data['IdTipoServicio'],
            'equipo' => $data['equipo'],
            'condicionesentrega' => $data['condicionesentrega'] ?? '',
            'problema' => $data['problema'],
            'notainterna' => $data['notainterna'] ?? '',
            'costo' => $data['costo'] ?? null,
            'IdEstadoEnTaller' => $data['IdEstadoEnTaller']
        ];

        $id = $this->servicioModel->create($servicioData);

        if ($id) {
            $this->json(['success' => true, 'message' => 'Servicio creado exitosamente', 'id' => $id]);
        } else {
            $this->json(['success' => false, 'message' => 'Error al crear el servicio'], 500);
        }
    }

    // Ver detalles del servicio
    public function view($id) {
        $servicio = $this->servicioModel->getByIdWithDetails($id);
        if (!$servicio) {
            $this->setFlash('error', 'Servicio no encontrado');
            $this->redirect(BASE_URL . '?route=servicios');
        }

        // Verificar si el usuario es técnico y si el servicio le fue asignado
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        // Verificar si el usuario es técnico administrador
        $esTecnicoAdministrador = !empty($perfilNombre) && 
                                (strtolower(trim($perfilNombre)) === 'técnico administrador' || 
                                 strtolower(trim($perfilNombre)) === 'tecnico administrador');

        // Verificar si el usuario es asesor
        $esAsesor = !empty($perfilNombre) && 
                   strtolower(trim($perfilNombre)) === 'asesor';

        // Debug temporal - REMOVER DESPUÉS
        error_log("DEBUG ServicioController::view() - perfilNombre: '$perfilNombre'");
        error_log("DEBUG ServicioController::view() - esTecnico: " . ($esTecnico ? 'true' : 'false'));
        error_log("DEBUG ServicioController::view() - esAsesor: " . ($esAsesor ? 'true' : 'false'));
        error_log("DEBUG ServicioController::view() - servicio estado: " . $servicio['IdEstadoEnTaller']);
        error_log("DEBUG ServicioController::view() - esTecnicoAdministrador: " . ($esTecnicoAdministrador ? 'true' : 'false'));

        if ($esTecnico) {
            $tecnicoId = $_SESSION['usuario_id'] ?? null;
            if ($tecnicoId && $servicio['NoIdentificacionEmpleado'] != $tecnicoId) {
                $this->setFlash('error', 'No tienes permisos para ver este servicio');
                $this->redirect(BASE_URL . '?route=servicios');
            }
        }

        // Obtener estados disponibles para el dropdown
        $estados = $this->servicioModel->getEstados();

        $this->render('servicios/view', compact('servicio', 'estados', 'esTecnico', 'esTecnicoAdministrador', 'esAsesor'));
    }

    // Mostrar formulario de edición
    public function edit($id) {
        $servicio = $this->servicioModel->getByIdWithDetails($id);
        if (!$servicio) {
            $this->setFlash('error', 'Servicio no encontrado');
            $this->redirect(BASE_URL . '?route=servicios');
        }

        // Verificar si el usuario es técnico y si el servicio le fue asignado
        $esTecnico = isset($_SESSION['usuario_perfil_nombre']) &&
                   strtolower($_SESSION['usuario_perfil_nombre']) === 'Técnico';

        if ($esTecnico) {
            $tecnicoId = $_SESSION['usuario_id'] ?? null;
            if ($tecnicoId && $servicio['NoIdentificacionEmpleado'] != $tecnicoId) {
                $this->setFlash('error', 'No tienes permisos para editar este servicio');
                $this->redirect(BASE_URL . '?route=servicios');
            }
        }

        // Obtener datos para los dropdowns
        $clientes = $this->servicioModel->getClientes();
        $estados = $this->servicioModel->getEstados();
        $tiposServicio = $this->servicioModel->getTiposServicio();
        $tecnicos = $this->servicioModel->getTecnicos();

        $this->render('servicios/edit', compact('servicio', 'clientes', 'estados', 'tiposServicio', 'tecnicos'));
    }

    // Actualizar servicio
    public function update($id) {
        // Debug: Log de inicio de actualización
        error_log("ServicioController::update() - Iniciando actualización para ID: $id");

        $data = $this->getPostData();
        error_log("ServicioController::update() - Datos recibidos: " . json_encode($data));

        // Obtener el servicio actual para validaciones
        $servicioActual = $this->servicioModel->getByIdWithDetails($id);
        if (!$servicioActual) {
            $this->json(['success' => false, 'message' => 'Servicio no encontrado'], 404);
        }

        // Verificar permisos para cambiar técnico asignado
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');
        $esTecnicoAdministrador = !empty($perfilNombre) && 
                                (strtolower(trim($perfilNombre)) === 'técnico administrador' || 
                                 strtolower(trim($perfilNombre)) === 'tecnico administrador');
        
        $servicioTerminado = isset($servicioActual['IdEstadoEnTaller']) && $servicioActual['IdEstadoEnTaller'] == 3;
        $tecnicoPuedeCambiarTecnico = $esTecnico && !$servicioTerminado && PermisoHelper::tienePermiso('cambiar_tecnico_servicio');
        $tecnicoAdminPuedeCambiarTecnico = $esTecnicoAdministrador && !$servicioTerminado && PermisoHelper::tienePermiso('cambiar_tecnico_servicio');
        
        // Si el técnico está intentando cambiar el técnico asignado, verificar permisos
        if ($esTecnico && isset($data['NoIdentificacionEmpleado']) && 
            $data['NoIdentificacionEmpleado'] != $servicioActual['NoIdentificacionEmpleado']) {
            
            if (!$tecnicoPuedeCambiarTecnico) {
                $this->json(['success' => false, 'message' => 'No tienes permisos para cambiar el técnico asignado'], 403);
            }
        }
        
        // Si el técnico administrador está intentando cambiar el técnico asignado, verificar permisos
        if ($esTecnicoAdministrador && isset($data['NoIdentificacionEmpleado']) && 
            $data['NoIdentificacionEmpleado'] != $servicioActual['NoIdentificacionEmpleado']) {
            
            if (!$tecnicoAdminPuedeCambiarTecnico) {
                $this->json(['success' => false, 'message' => 'No tienes permisos para cambiar el técnico asignado'], 403);
            }
        }

        // Validación básica
        $required = ['idcliente', 'IdTipoServicio', 'equipo', 'problema', 'IdEstadoEnTaller'];
        $errors = $this->validateRequired($data, $required);

        if (!empty($errors)) {
            error_log("ServicioController::update() - Errores de validación: " . json_encode($errors));
            $this->json(['success' => false, 'errors' => $errors], 400);
        }

        // Preparar datos para el modelo
        $servicioData = [
            'NoIdentificacionCliente' => $data['idcliente'],
            'NoIdentificacionEmpleado' => ($data['NoIdentificacionEmpleado'] === 'sin_asignar' || empty($data['NoIdentificacionEmpleado'])) ? null : (int)$data['NoIdentificacionEmpleado'],
            'IdTipoServicio' => (int)$data['IdTipoServicio'],
            'Equipo' => $data['equipo'],
            'CondicionesEntrega' => $data['condicionesentrega'] ?? '',
            'Problema' => $data['problema'],
            'Solucion' => $data['solucion'] ?? '',
            'NotaInterna' => $data['notainterna'] ?? '',
            'Costo' => !empty($data['costo']) ? (int)$data['costo'] : null,
            'IdEstadoEnTaller' => (int)$data['IdEstadoEnTaller']
        ];

        error_log("ServicioController::update() - Datos preparados: " . json_encode($servicioData));

        // Intentar actualizar
        $result = $this->servicioModel->update($id, $servicioData);
        error_log("ServicioController::update() - Resultado del modelo: " . ($result ? 'true' : 'false'));

        if ($result) {
            error_log("ServicioController::update() - Actualización exitosa");
            $this->json(['success' => true, 'message' => 'Servicio actualizado exitosamente']);
        } else {
            error_log("ServicioController::update() - Error en la actualización");
            $this->json(['success' => false, 'message' => 'Error al actualizar el servicio'], 500);
        }
    }

    // Cambiar estado
    public function changeStatus($id) {
        $data = $this->getPostData();

        if (!isset($data['estado'])) {
            $this->json(['success' => false, 'message' => 'Estado requerido'], 400);
        }

        if ($this->servicioModel->changeStatus($id, $data['estado'])) {
            $this->json(['success' => true, 'message' => 'Estado actualizado']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al actualizar estado'], 500);
        }
    }

    // Eliminar servicio
    public function delete($id) {
        try {
            // Verificar si el servicio existe
            $servicio = $this->servicioModel->getById($id);
            if (!$servicio) {
                $this->json(['success' => false, 'message' => 'Servicio no encontrado'], 404);
                return;
            }

            // Verificar permisos si es técnico
            $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
            $esTecnico = !empty($perfilNombre) && 
                       (strtolower(trim($perfilNombre)) === 'técnico' || 
                        strtolower(trim($perfilNombre)) === 'tecnico');

            if ($esTecnico) {
                $tecnicoId = $_SESSION['usuario_id'] ?? null;
                if ($tecnicoId && $servicio['NoIdentificacionEmpleado'] != $tecnicoId) {
                    $this->json(['success' => false, 'message' => 'No tienes permisos para eliminar este servicio'], 403);
                    return;
                }
            }

            // Eliminar el servicio
            if ($this->servicioModel->delete($id)) {
                $this->json(['success' => true, 'message' => 'Servicio eliminado correctamente']);
            } else {
                $this->json(['success' => false, 'message' => 'Error al eliminar el servicio'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Buscar clientes para autocompletado
     */
    public function buscarClientes() {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            $this->json(['success' => true, 'clientes' => []]);
            return;
        }

        $clientes = $this->servicioModel->buscarClientes($query);
        $this->json(['success' => true, 'clientes' => $clientes]);
    }

    // Buscar servicios para autocompletado
    public function buscarServicios() {
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 1) {
            $this->json(['success' => true, 'servicios' => []]);
            return;
        }

        // Verificar si el usuario es técnico
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        if ($esTecnico) {
            // Para técnicos, buscar solo en sus servicios asignados
            $tecnicoId = $_SESSION['usuario_id'] ?? null;
            if ($tecnicoId) {
                $servicios = $this->servicioModel->buscarServiciosByTecnico($query, $tecnicoId);
                
                // Si no hay resultados, buscar en servicios de otros técnicos para mostrar mensaje informativo
                if (empty($servicios)) {
                    $serviciosOtrosTecnicos = $this->servicioModel->buscarServiciosOtrosTecnicos($query, $tecnicoId);
                    if (!empty($serviciosOtrosTecnicos)) {
                        // Agregar mensaje informativo
                        $servicios = [
                            [
                                'IdServicio' => 'info',
                                'cliente_nombre' => 'Servicio encontrado en otro técnico',
                                'Equipo' => 'El servicio existe pero está asignado a otro técnico',
                                'Problema' => 'Contacta al administrador si necesitas acceso',
                                'estado_descripcion' => 'Información',
                                'tecnico_nombre' => $serviciosOtrosTecnicos[0]['tecnico_nombre'] ?? 'Otro técnico',
                                'FechaIngreso' => date('Y-m-d H:i:s'),
                                'tipo_resultado' => 'mensaje_info',
                                'servicios_otros_tecnicos' => $serviciosOtrosTecnicos
                            ]
                        ];
                    }
                }
            } else {
                $servicios = [];
            }
        } else {
            // Para otros usuarios, buscar en todos los servicios
            $servicios = $this->servicioModel->buscarServicios($query);
        }

        $this->json(['success' => true, 'servicios' => $servicios]);
    }

    /**
     * Generar PDF de orden de servicio
     */
    public function imprimir($id) {
        // Obtener servicio con todos los detalles
        $servicio = $this->servicioModel->getByIdWithDetails($id);

        if (!$servicio) {
            $this->setFlash('error', 'Servicio no encontrado');
            $this->redirect('servicios');
            return;
        }

        // Cargar helpers necesarios
        require_once __DIR__ . '/../helpers/PdfHelper.php';
        require_once __DIR__ . '/../helpers/EmpresaHelper.php';

        // Generar PDF
        PdfHelper::generarOrdenServicio($servicio);
    }

    /**
     * Consulta avanzada de servicios con filtros
     */
    public function consultar() {
        // Verificar si el usuario es técnico
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        // Verificar si el usuario es técnico administrador
        $esTecnicoAdministrador = !empty($perfilNombre) && 
                                (strtolower(trim($perfilNombre)) === 'técnico administrador' || 
                                 strtolower(trim($perfilNombre)) === 'tecnico administrador');

        // Verificar si el usuario es asesor
        $esAsesor = !empty($perfilNombre) && 
                   strtolower(trim($perfilNombre)) === 'asesor';

        // Obtener datos para los filtros
        $estados = $this->servicioModel->getEstados();
        $tecnicos = $this->servicioModel->getTecnicos();
        $tiposServicio = $this->servicioModel->getTiposServicio();

        // Inicializar variables de filtros
        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'tecnico_id' => $_GET['tecnico_id'] ?? '',
            'cliente_id' => $_GET['cliente_id'] ?? '',
            'cliente_nombre' => $_GET['cliente_nombre'] ?? '',
            'estado_id' => $_GET['estado_id'] ?? '',
            'tipo_servicio_id' => $_GET['tipo_servicio_id'] ?? '',
            'equipo' => $_GET['equipo'] ?? '',
            'problema' => $_GET['problema'] ?? '',
            'servicio_id' => $_GET['servicio_id'] ?? ''
        ];

        // Obtener resultados de la consulta
        $servicios = [];
        $totalServicios = 0;
        
        if ($this->tieneFiltrosActivos($filtros)) {
            $servicios = $this->servicioModel->consultarServicios($filtros, $esTecnico, $esTecnicoAdministrador);
            $totalServicios = count($servicios);
        }

        $this->render('servicios/consultar', compact(
            'servicios', 
            'estados', 
            'tecnicos', 
            'tiposServicio', 
            'filtros',
            'totalServicios',
            'esTecnico', 
            'esTecnicoAdministrador', 
            'esAsesor'
        ));
    }

    /**
     * Verificar si hay filtros activos
     */
    private function tieneFiltrosActivos($filtros) {
        foreach ($filtros as $filtro) {
            if (!empty($filtro)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Exportar resultados de consulta
     */
    public function exportarConsulta() {
        // Deshabilitar límites de tiempo y buffer
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        
        // Limpiar cualquier buffer de salida previo
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Verificar si el usuario es técnico
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        // Verificar si el usuario es técnico administrador
        $esTecnicoAdministrador = !empty($perfilNombre) && 
                                (strtolower(trim($perfilNombre)) === 'técnico administrador' || 
                                 strtolower(trim($perfilNombre)) === 'tecnico administrador');

        // Obtener filtros de la URL
        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'tecnico_id' => $_GET['tecnico_id'] ?? '',
            'cliente_id' => $_GET['cliente_id'] ?? '',
            'cliente_nombre' => $_GET['cliente_nombre'] ?? '',
            'estado_id' => $_GET['estado_id'] ?? '',
            'tipo_servicio_id' => $_GET['tipo_servicio_id'] ?? '',
            'equipo' => $_GET['equipo'] ?? '',
            'problema' => $_GET['problema'] ?? '',
            'servicio_id' => $_GET['servicio_id'] ?? ''
        ];

        // Obtener servicios con filtros
        $servicios = $this->servicioModel->consultarServicios($filtros, $esTecnico, $esTecnicoAdministrador);

        // Configurar headers para descarga
        $filename = 'consulta_servicios_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Pragma: no-cache');

        // Crear archivo CSV
        $output = fopen('php://output', 'w');
        
        // Desactivar buffering para el output
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }
        @ini_set('zlib.output_compression', '0');
        @ini_set('implicit_flush', '1');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Delimitador y encerramiento para CSV (usar punto y coma puede funcionar mejor en Excel)
        $delimiter = ',';  // Coma estándar
        $enclosure = '"';  // Comillas dobles estándar

        // Encabezados
        fputcsv($output, [
            'ID Servicio',
            'Cliente',
            'ID Cliente',
            'Equipo',
            'Problema',
            'Estado',
            'Técnico',
            'Tipo Servicio',
            'Fecha Ingreso',
            'Costo',
            'Condiciones Entrega',
            'Solución',
            'Nota Interna'
        ], $delimiter, $enclosure);

        // Datos
        foreach ($servicios as $servicio) {
            // Exportar campos COMPLETOS sin truncar (hasta 1000+ caracteres)
            // La tabla HTML muestra solo 20 caracteres, pero el CSV exporta TODO el contenido
            
            // Limpiar y preparar campos largos - eliminar saltos de línea que rompen el CSV
            // Asegurar que se exporta TODO el contenido sin truncar
            $problemaCompleto = !empty($servicio['Problema']) ? $servicio['Problema'] : 'N/A';
            $problemaCompleto = trim($problemaCompleto);
            $problemaCompleto = str_replace(["\r\n", "\r", "\n"], ' ', $problemaCompleto);
            
            $equipoCompleto = !empty($servicio['Equipo']) ? $servicio['Equipo'] : 'N/A';
            $equipoCompleto = trim($equipoCompleto);
            $equipoCompleto = str_replace(["\r\n", "\r", "\n"], ' ', $equipoCompleto);
            
            $solucionCompleta = !empty($servicio['Solucion']) ? $servicio['Solucion'] : '';
            $solucionCompleta = trim($solucionCompleta);
            $solucionCompleta = str_replace(["\r\n", "\r", "\n"], ' ', $solucionCompleta);
            
            $condicionesCompletas = !empty($servicio['CondicionesEntrega']) ? $servicio['CondicionesEntrega'] : '';
            $condicionesCompletas = trim($condicionesCompletas);
            $condicionesCompletas = str_replace(["\r\n", "\r", "\n"], ' ', $condicionesCompletas);
            
            $notaInternaCompleta = !empty($servicio['NotaInterna']) ? $servicio['NotaInterna'] : '';
            $notaInternaCompleta = trim($notaInternaCompleta);
            $notaInternaCompleta = str_replace(["\r\n", "\r", "\n"], ' ', $notaInternaCompleta);
            
            // Escribir fila al CSV - fputcsv maneja el escape automáticamente
            $result = fputcsv($output, [
                $servicio['IdServicio'],
                $servicio['cliente_nombre'] ?? 'N/A',
                $servicio['NoIdentificacionCliente'] ?? 'N/A',
                $equipoCompleto,
                $problemaCompleto,  // CAMPO COMPLETO SIN TRUNCAR
                $servicio['estado_descripcion'] ?? 'N/A',
                $servicio['tecnico_nombre'] ?? 'Sin asignar',
                $servicio['tipo_servicio_nombre'] ?? 'N/A',
                DateHelper::extractDateTime($servicio['FechaIngreso']),
                $servicio['Costo'] ?? '0',
                $condicionesCompletas,
                $solucionCompleta,  // CAMPO COMPLETO SIN TRUNCAR
                $notaInternaCompleta
            ], $delimiter, $enclosure);
            
            // Forzar flush para evitar problemas de buffering en textos largos
            if ($result !== false) {
                flush();
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Exportar resultados - Método alternativo escribiendo CSV manualmente
     * Para asegurar que campos largos no se trunquen
     */
    public function exportarConsultaManual() {
        // Configuración inicial
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Verificar permisos de usuario
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');
        $esTecnicoAdministrador = !empty($perfilNombre) && 
                                (strtolower(trim($perfilNombre)) === 'técnico administrador' || 
                                 strtolower(trim($perfilNombre)) === 'tecnico administrador');

        // Obtener filtros
        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'tecnico_id' => $_GET['tecnico_id'] ?? '',
            'cliente_id' => $_GET['cliente_id'] ?? '',
            'cliente_nombre' => $_GET['cliente_nombre'] ?? '',
            'estado_id' => $_GET['estado_id'] ?? '',
            'tipo_servicio_id' => $_GET['tipo_servicio_id'] ?? '',
            'equipo' => $_GET['equipo'] ?? '',
            'problema' => $_GET['problema'] ?? '',
            'servicio_id' => $_GET['servicio_id'] ?? ''
        ];

        // Obtener servicios
        $servicios = $this->servicioModel->consultarServicios($filtros, $esTecnico, $esTecnicoAdministrador);

        // Obtener columnas seleccionadas (si no hay, exportar todas)
        $columnasParam = $_GET['columnas'] ?? '';
        
        // Si hay columnas especificadas, usarlas; si no, todas por defecto
        if (!empty($columnasParam)) {
            $columnasSeleccionadas = explode(',', $columnasParam);
        } else {
            $columnasSeleccionadas = ['0','1','2','3','4','5','6','7','8','9'];
        }
        
        // Mapeo de índices a nombres de columnas
        $columnasDisponibles = [
            '0' => ['header' => 'ID Servicio', 'key' => 'IdServicio'],
            '1' => ['header' => 'Cliente', 'key' => 'cliente_nombre'],
            '2' => ['header' => 'Equipo', 'key' => 'Equipo'],
            '3' => ['header' => 'Problema', 'key' => 'Problema'],
            '4' => ['header' => 'Solución', 'key' => 'Solucion'],
            '5' => ['header' => 'Estado', 'key' => 'estado_descripcion'],
            '6' => ['header' => 'Técnico', 'key' => 'tecnico_nombre'],
            '7' => ['header' => 'Tipo Servicio', 'key' => 'tipo_servicio_nombre'],
            '8' => ['header' => 'Fecha Ingreso', 'key' => 'FechaIngreso'],
            '9' => ['header' => 'Costo', 'key' => 'Costo']
        ];

        // Headers HTTP
        $filename = 'consulta_servicios_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        // BOM para UTF-8
        echo chr(0xEF) . chr(0xBB) . chr(0xBF);

        // Función helper para escapar campos CSV
        $escapeCsv = function($field) {
            $field = str_replace('"', '""', $field); // Duplicar comillas
            $field = str_replace(["\r\n", "\r", "\n"], ' ', $field); // Eliminar saltos
            return '"' . $field . '"'; // Encerrar en comillas
        };

        // Construir encabezados dinámicamente según columnas seleccionadas
        $headers = [];
        foreach ($columnasSeleccionadas as $colIndex) {
            $colIndex = trim($colIndex); // Limpiar espacios
            if (isset($columnasDisponibles[$colIndex])) {
                $headers[] = $columnasDisponibles[$colIndex]['header'];
            }
        }
        
        echo implode(',', $headers) . "\n";

        // Datos - escribir solo columnas seleccionadas
        foreach ($servicios as $servicio) {
            $row = [];
            
            // Procesar cada columna seleccionada
            foreach ($columnasSeleccionadas as $colIndex) {
                $colIndex = trim($colIndex); // Limpiar espacios
                switch($colIndex) {
                    case '0': // ID Servicio
                        $row[] = $servicio['IdServicio'];
                        break;
                    case '1': // Cliente
                        $row[] = $escapeCsv($servicio['cliente_nombre'] ?? 'N/A');
                        break;
                    case '2': // Equipo
                        $row[] = $escapeCsv(trim($servicio['Equipo'] ?? 'N/A'));
                        break;
                    case '3': // Problema - COMPLETO
                        $row[] = $escapeCsv(trim($servicio['Problema'] ?? 'N/A'));
                        break;
                    case '4': // Solución - COMPLETO
                        $row[] = $escapeCsv(trim($servicio['Solucion'] ?? ''));
                        break;
                    case '5': // Estado
                        $row[] = $escapeCsv($servicio['estado_descripcion'] ?? 'N/A');
                        break;
                    case '6': // Técnico
                        $row[] = $escapeCsv($servicio['tecnico_nombre'] ?? 'Sin asignar');
                        break;
                    case '7': // Tipo Servicio
                        $row[] = $escapeCsv($servicio['tipo_servicio_nombre'] ?? 'N/A');
                        break;
                    case '8': // Fecha Ingreso
                        $row[] = $escapeCsv(DateHelper::extractDateTime($servicio['FechaIngreso']));
                        break;
                    case '9': // Costo
                        $row[] = $servicio['Costo'] ?? '0';
                        break;
                }
            }
            
            // Escribir fila solo con columnas seleccionadas
            echo implode(',', $row) . "\n";
            flush();
        }

        exit;
    }

    /**
     * Cargar resultados de consulta via AJAX
     */
    public function cargarResultados() {
        // Verificar si el usuario es técnico
        $perfilNombre = $_SESSION['usuario_perfil_nombre'] ?? '';
        $esTecnico = !empty($perfilNombre) && 
                   (strtolower(trim($perfilNombre)) === 'técnico' || 
                    strtolower(trim($perfilNombre)) === 'tecnico');

        // Verificar si el usuario es técnico administrador
        $esTecnicoAdministrador = !empty($perfilNombre) && 
                                (strtolower(trim($perfilNombre)) === 'técnico administrador' || 
                                 strtolower(trim($perfilNombre)) === 'tecnico administrador');

        // Verificar si el usuario es asesor
        $esAsesor = !empty($perfilNombre) && 
                   strtolower(trim($perfilNombre)) === 'asesor';

        // Obtener filtros del POST
        $filtros = [
            'fecha_desde' => $_POST['fecha_desde'] ?? '',
            'fecha_hasta' => $_POST['fecha_hasta'] ?? '',
            'tecnico_id' => $_POST['tecnico_id'] ?? '',
            'cliente_id' => $_POST['cliente_id'] ?? '',
            'cliente_nombre' => $_POST['cliente_nombre'] ?? '',
            'estado_id' => $_POST['estado_id'] ?? '',
            'tipo_servicio_id' => $_POST['tipo_servicio_id'] ?? '',
            'equipo' => $_POST['equipo'] ?? '',
            'problema' => $_POST['problema'] ?? '',
            'servicio_id' => $_POST['servicio_id'] ?? ''
        ];

        // Validar ID del servicio si se proporciona
        if (!empty($filtros['servicio_id'])) {
            if (!is_numeric($filtros['servicio_id']) || $filtros['servicio_id'] < 1) {
                $this->json([
                    'success' => false,
                    'message' => 'El ID del servicio debe ser un número válido mayor a 0'
                ], 400);
                return;
            }
        }

        // Validar nombre del cliente si se proporciona
        if (!empty($filtros['cliente_nombre'])) {
            if (strlen(trim($filtros['cliente_nombre'])) < 2) {
                $this->json([
                    'success' => false,
                    'message' => 'El nombre del cliente debe tener al menos 2 caracteres'
                ], 400);
                return;
            }
        }

        try {
            // Obtener servicios con filtros
            $servicios = $this->servicioModel->consultarServicios($filtros, $esTecnico, $esTecnicoAdministrador);
            $totalServicios = count($servicios);

            // Preparar respuesta
            $response = [
                'success' => true,
                'servicios' => $servicios,
                'total' => $totalServicios,
                'filtros' => $filtros,
                'esTecnico' => $esTecnico,
                'esTecnicoAdministrador' => $esTecnicoAdministrador,
                'esAsesor' => $esAsesor
            ];

            $this->json($response);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar los resultados: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>
