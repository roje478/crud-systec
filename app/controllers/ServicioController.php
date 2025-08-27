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
        $esTecnico = isset($_SESSION['usuario_perfil_nombre']) &&
                   strtolower($_SESSION['usuario_perfil_nombre']) === 'tecnico';

        // Verificar si el usuario es asesor
        $esAsesor = isset($_SESSION['usuario_perfil_nombre']) &&
                   strtolower($_SESSION['usuario_perfil_nombre']) === 'asesor';

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
        $this->render('servicios/index', compact('servicios', 'estados', 'esTecnico', 'esAsesor'));
    }

    // Lista completa de servicios - TODOS los servicios sin límite con paginación de DataTables
    public function listaCompleta() {
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
            'totalServicios'
        ));
    }

    // Mostrar formulario de selección de cliente
    public function selectCliente() {
        $clientes = $this->servicioModel->getClientes();
        $this->render('servicios/select_cliente', compact('clientes'));
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
        $esTecnico = isset($_SESSION['usuario_perfil_nombre']) &&
                   strtolower($_SESSION['usuario_perfil_nombre']) === 'tecnico';

        // Verificar si el usuario es asesor
        $esAsesor = isset($_SESSION['usuario_perfil_nombre']) &&
                   strtolower($_SESSION['usuario_perfil_nombre']) === 'asesor';

        if ($esTecnico) {
            $tecnicoId = $_SESSION['usuario_id'] ?? null;
            if ($tecnicoId && $servicio['NoIdentificacionEmpleado'] != $tecnicoId) {
                $this->setFlash('error', 'No tienes permisos para ver este servicio');
                $this->redirect(BASE_URL . '?route=servicios');
            }
        }

        // Obtener estados disponibles para el dropdown
        $estados = $this->servicioModel->getEstados();

        $this->render('servicios/view', compact('servicio', 'estados', 'esTecnico', 'esAsesor'));
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
                   strtolower($_SESSION['usuario_perfil_nombre']) === 'tecnico';

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
            $esTecnico = isset($_SESSION['usuario_perfil_nombre']) &&
                       strtolower($_SESSION['usuario_perfil_nombre']) === 'tecnico';

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
}
?>
