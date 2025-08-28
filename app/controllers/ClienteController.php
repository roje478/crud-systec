<?php
/**
 * Controlador Cliente - Gestión completa de clientes
 */
class ClienteController extends BaseController {
    private $clienteModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
    }

    // Listar clientes
    public function index() {
        // Obtener TODOS los clientes (DataTables maneja la paginación del lado del cliente)
        $clientes = $this->clienteModel->getAll();
        $estadisticas = $this->clienteModel->getEstadisticas();

        // Log para debug
        error_log("ClienteController::index() - Total clientes obtenidos: " . count($clientes));

        $this->render('clientes/index', compact('clientes', 'estadisticas'));
    }

    // Mostrar formulario de creación
    public function create() {
        $tiposIdentificacion = $this->clienteModel->getTiposIdentificacion();
        $generos = $this->clienteModel->getGeneros();

        $this->render('clientes/create', compact('tiposIdentificacion', 'generos'));
    }

    // Guardar cliente
    public function store() {
        try {
            $data = $this->getPostData();

            // Log para debug
            error_log("Datos recibidos en store: " . json_encode($data));

            // Validación básica
            $required = ['no_identificacion', 'tipo_id', 'nombres', 'apellidos'];
            $errors = $this->validateRequired($data, $required);

            // Log para debug
            if (!empty($errors)) {
                error_log("Errores de validación: " . json_encode($errors));
            }

            // Validar número de identificación único
            if (empty($errors) && $this->clienteModel->existsByIdentificacion($data['no_identificacion'])) {
                $errors['no_identificacion'] = 'Ya existe un cliente con este número de identificación';
                error_log("Cliente ya existe con ID: " . $data['no_identificacion']);
            }

            if (!empty($errors)) {
                $this->json(['success' => false, 'errors' => $errors], 400);
                return;
            }

            // Intentar crear el cliente
            $id = $this->clienteModel->create($data);

            if ($id) {
                error_log("Cliente creado exitosamente con ID: " . $id);
                $this->json(['success' => true, 'message' => 'Cliente creado exitosamente', 'id' => $id]);
            } else {
                error_log("Error al crear cliente - método create() retornó false");
                $this->json(['success' => false, 'message' => 'Error al crear el cliente'], 500);
            }

        } catch (Exception $e) {
            error_log("Excepción en store(): " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    // Ver detalles del cliente
    public function view($id) {
        $cliente = $this->clienteModel->getById($id);
        if (!$cliente) {
            $this->setFlash('error', 'Cliente no encontrado');
            $this->redirect(BASE_URL . '?route=clientes');
        }

        $this->render('clientes/view', compact('cliente'));
    }

    // Mostrar formulario de edición
    public function edit($id) {
        $cliente = $this->clienteModel->getById($id);
        if (!$cliente) {
            $this->setFlash('error', 'Cliente no encontrado');
            $this->redirect(BASE_URL . '?route=clientes');
        }

        $tiposIdentificacion = $this->clienteModel->getTiposIdentificacion();
        $generos = $this->clienteModel->getGeneros();

        $this->render('clientes/edit', compact('cliente', 'tiposIdentificacion', 'generos'));
    }

    // Actualizar cliente
    public function update($id) {
        try {
            $data = $this->getPostData();

            // Log para debug
            error_log("ClienteController::update() - ID: " . $id . ", Datos recibidos: " . json_encode($data));

            // Validación básica
            $required = ['no_identificacion', 'tipo_id', 'nombres', 'apellidos'];
            $errors = $this->validateRequired($data, $required);

            // Log para debug
            if (!empty($errors)) {
                error_log("ClienteController::update() - Errores de validación: " . json_encode($errors));
                $this->json(['success' => false, 'errors' => $errors], 400);
                return;
            }

            // Validar número de identificación único (excluyendo el cliente actual)
            if (empty($errors) && $this->clienteModel->existsByIdentificacion($data['no_identificacion'], $id)) {
                $errors['no_identificacion'] = 'Ya existe otro cliente con este número de identificación';
                error_log("ClienteController::update() - Cliente ya existe con ID: " . $data['no_identificacion']);
                $this->json(['success' => false, 'errors' => $errors], 400);
                return;
            }

            // Intentar actualizar el cliente
            $result = $this->clienteModel->update($id, $data);

            if ($result) {
                error_log("ClienteController::update() - Cliente actualizado exitosamente");
                $this->json(['success' => true, 'message' => 'Cliente actualizado exitosamente']);
            } else {
                error_log("ClienteController::update() - Error al actualizar cliente - método update() retornó false");
                $this->json(['success' => false, 'message' => 'No se pudo actualizar el cliente. Verifique que el cliente existe.'], 404);
            }

        } catch (Exception $e) {
            error_log("ClienteController::update() - Excepción: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }



    // Buscar clientes (AJAX)
    public function search() {
        $query = $_GET['q'] ?? '';
        $page = $_GET['page'] ?? 1;

        if (empty($query)) {
            $this->json(['success' => false, 'message' => 'Término de búsqueda requerido'], 400);
        }

        $clientes = $this->clienteModel->search($query, $page);
        $totalResults = $this->clienteModel->getTotalSearchResults($query);

        $this->json([
            'success' => true,
            'clientes' => $clientes,
            'total' => $totalResults,
            'query' => $query
        ]);
    }

    // Buscar clientes para AJAX (versión mejorada)
    public function buscarClientes() {
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            $this->json(['success' => false, 'message' => 'Término de búsqueda requerido'], 400);
            return;
        }

        try {
            $clientes = $this->clienteModel->buscarClientes($query);
            $this->json([
                'success' => true,
                'clientes' => $clientes,
                'query' => $query
            ]);
        } catch (Exception $e) {
            error_log("Error en buscarClientes: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Error en la búsqueda'], 500);
        }
    }

    // Mostrar vista de búsqueda de clientes
    public function buscar() {
        // Inicializar array vacío de clientes (se llenará con búsqueda)
        $clientes = [];

        $this->render('clientes/buscar_mejorada', compact('clientes'));
    }

    // Obtener estadísticas (AJAX)
    public function estadisticas() {
        $estadisticas = $this->clienteModel->getEstadisticas();
        $this->json(['success' => true, 'estadisticas' => $estadisticas]);
    }

    // Eliminar cliente
    public function delete($id) {
        try {
            // Verificar si el cliente existe
            $cliente = $this->clienteModel->getById($id);
            if (!$cliente) {
                $this->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
                return;
            }

            // Verificar si el cliente tiene servicios asociados
            $serviciosAsociados = $this->clienteModel->getServiciosAsociados($id);
            if (!empty($serviciosAsociados)) {
                $this->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el cliente porque tiene servicios asociados'
                ], 400);
                return;
            }

            // Eliminar el cliente
            if ($this->clienteModel->delete($id)) {
                $this->json(['success' => true, 'message' => 'Cliente eliminado correctamente']);
            } else {
                $this->json(['success' => false, 'message' => 'Error al eliminar el cliente'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
}