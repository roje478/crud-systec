<?php
/**
 * OrdenExternaController - CRUD de las órdenes entregadas a técnicos externos
 *
 * Opciones de permisos asociadas:
 *   TE01 -> Órdenes Externas (acceso al listado, ver, editar)
 *   TE02 -> Nueva Orden Externa (crear)
 */
class OrdenExternaController extends BaseController {
    const OPCION_LISTADO = 'TE01';
    const OPCION_CREAR   = 'TE02';

    private $ordenModel;
    private $tecnicoModel;
    private $motivoModel;
    private $permisoModel = null;

    public function __construct() {
        $this->verificarAcceso();
        $this->ordenModel = new OrdenExterna();
        $this->tecnicoModel = new TecnicoExterno();
        $this->motivoModel = new MotivoOrdenExterna();
    }

    /**
     * Comprobar un permiso contra la tabla `perfil_opciones`.
     *
     * Se consulta el modelo Permiso directamente y NO PermisoHelper: en algunos
     * despliegues ese helper resuelve los permisos contra la constante
     * USER_PROFILES de config/auth.php (un mapa fijo de nombres de perfil) en
     * vez de contra la base, y por tanto desconoce los códigos de este módulo.
     * El modelo sí lee las opciones reales que se asignan desde Permisos.
     */
    private function tienePermisoOpcion($codigo) {
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if (!$usuarioId) {
            return false;
        }

        if ($this->permisoModel === null) {
            $this->permisoModel = new Permiso();
        }

        return $this->permisoModel->tienePermiso($usuarioId, $codigo);
    }

    /**
     * El usuario debe tener al menos una de las dos opciones del módulo.
     * Ocultar el menú no protege la URL; esta guarda sí.
     */
    private function verificarAcceso() {
        $tieneAcceso = $this->tienePermisoOpcion(self::OPCION_LISTADO)
                    || $this->tienePermisoOpcion(self::OPCION_CREAR);

        if (!$tieneAcceso) {
            $this->setFlash('error', 'No tiene permisos para acceder a las órdenes externas.');
            $this->redirect('servicios');
        }
    }

    /**
     * Exigir una opción concreta para una acción concreta
     */
    private function requierePermiso($opcion, $mensaje) {
        if (!$this->tienePermisoOpcion($opcion)) {
            $this->setFlash('error', $mensaje);
            $this->redirect('servicios');
        }
    }

    /**
     * Leer los filtros del listado desde la query string
     */
    private function obtenerFiltros() {
        return [
            'busqueda'    => trim($_GET['q'] ?? ''),
            'tecnico'     => $_GET['tecnico'] ?? '',
            'motivo'      => $_GET['motivo'] ?? '',
            'estado'      => $_GET['estado'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];
    }

    /**
     * Listado con filtros y paginación
     */
    public function index() {
        $this->requierePermiso(self::OPCION_LISTADO, 'No tiene permisos para ver las órdenes externas.');

        $filtros = $this->obtenerFiltros();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 25;

        $ordenes    = $this->ordenModel->getFiltradas($filtros, $page, $perPage);
        $paginacion = $this->ordenModel->getPaginationInfo($filtros, $page, $perPage);
        $resumen    = $this->ordenModel->getResumen($filtros);
        $tecnicos   = $this->tecnicoModel->getActivos();
        $motivos    = $this->motivoModel->getActivos();
        $estados    = OrdenExterna::getEstados();

        $this->render('ordenes_externas/index', compact(
            'ordenes', 'paginacion', 'resumen', 'tecnicos', 'motivos', 'estados', 'filtros'
        ));
    }

    /**
     * Formulario de creación
     */
    public function create() {
        $this->requierePermiso(self::OPCION_CREAR, 'No tiene permisos para crear órdenes externas.');

        $tecnicos      = $this->tecnicoModel->getActivos();
        $motivos       = $this->motivoModel->getActivos();
        $responsables  = $this->ordenModel->getResponsablesInternos();
        $usuarioActual = $_SESSION['usuario_id'] ?? null;

        // Si una validación falló, recuperar lo que el usuario ya había escrito
        $orden = $_SESSION['orden_externa_form'] ?? [];
        unset($_SESSION['orden_externa_form']);

        // Valores por defecto del formulario
        // Vista previa del código: el definitivo se asigna al guardar
        $orden['CodOrden']     = $this->ordenModel->generarCodigo();
        $orden['Fecha']        = $orden['Fecha'] ?? date('Y-m-d');
        $orden['QuienEntrega'] = $orden['QuienEntrega'] ?? $usuarioActual;

        $this->render('ordenes_externas/create', compact(
            'tecnicos', 'motivos', 'responsables', 'orden', 'usuarioActual'
        ));
    }

    /**
     * Guardar una orden nueva
     */
    public function store() {
        $this->requierePermiso(self::OPCION_CREAR, 'No tiene permisos para crear órdenes externas.');

        $data = $this->getPostData();

        // El código de orden lo asigna SIEMPRE el sistema en el momento de
        // guardar: el campo del formulario es solo una vista previa. Así dos
        // usuarios con el formulario abierto a la vez no reciben el mismo código.
        $data['CodOrden'] = $this->ordenModel->generarCodigo();

        $errores = $this->validarDatos($data);
        if (!empty($errores)) {
            $_SESSION['orden_externa_form'] = $data;
            $this->setFlash('error', implode(' ', $errores));
            $this->redirect('ordenes-externas/create');
            return;
        }

        $ordenData = $this->prepararDatos($data);
        $ordenData['RegistradoPor'] = $_SESSION['usuario_id'] ?? null;

        $id = $this->ordenModel->create($ordenData);

        if ($id) {
            unset($_SESSION['orden_externa_form']);
            $this->setFlash('success', 'Orden ' . $ordenData['CodOrden'] . ' registrada correctamente.');

            // "Guardar y crear otra"
            if (!empty($data['guardar_y_nuevo'])) {
                $this->redirect('ordenes-externas/create');
                return;
            }

            $this->redirect('ordenes-externas/view/' . $id);
        } else {
            $_SESSION['orden_externa_form'] = $data;
            $this->setFlash('error', 'No se pudo registrar la orden.');
            $this->redirect('ordenes-externas/create');
        }
    }

    /**
     * Detalle de la orden
     */
    public function view($id) {
        $this->requierePermiso(self::OPCION_LISTADO, 'No tiene permisos para ver las órdenes externas.');

        $orden = $this->ordenModel->getByIdWithDetails($id);

        if (!$orden) {
            $this->setFlash('error', 'Orden no encontrada.');
            $this->redirect('ordenes-externas');
            return;
        }

        $responsables = $this->ordenModel->getResponsablesInternos();
        $estados = OrdenExterna::getEstados();

        $this->render('ordenes_externas/view', compact('orden', 'responsables', 'estados'));
    }

    /**
     * Formulario de edición
     */
    public function edit($id) {
        $this->requierePermiso(self::OPCION_LISTADO, 'No tiene permisos para editar órdenes externas.');

        $orden = $this->ordenModel->getByIdWithDetails($id);

        if (!$orden) {
            $this->setFlash('error', 'Orden no encontrada.');
            $this->redirect('ordenes-externas');
            return;
        }

        $tecnicos     = $this->tecnicoModel->getActivos();
        $motivos      = $this->motivoModel->getActivos();
        $responsables = $this->ordenModel->getResponsablesInternos();
        $estados      = OrdenExterna::getEstados();

        $this->render('ordenes_externas/edit', compact(
            'orden', 'tecnicos', 'motivos', 'responsables', 'estados'
        ));
    }

    /**
     * Actualizar la orden
     */
    public function update($id) {
        $this->requierePermiso(self::OPCION_LISTADO, 'No tiene permisos para editar órdenes externas.');

        $orden = $this->ordenModel->getById($id);

        if (!$orden) {
            $this->setFlash('error', 'Orden no encontrada.');
            $this->redirect('ordenes-externas');
            return;
        }

        $data = $this->getPostData();

        // El código de orden no es editable: se conserva el ya asignado
        $data['CodOrden'] = $orden['CodOrden'];

        $errores = $this->validarDatos($data, $id);
        if (!empty($errores)) {
            $this->setFlash('error', implode(' ', $errores));
            $this->redirect('ordenes-externas/edit/' . $id);
            return;
        }

        $ordenData = $this->prepararDatos($data);

        // Estado: si se registra quién recibe, la orden pasa a "recibido"
        $estadosValidos = array_keys(OrdenExterna::getEstados());
        $estado = $data['Estado'] ?? $orden['Estado'];

        if (!in_array($estado, $estadosValidos, true)) {
            $estado = $orden['Estado'];
        }

        if ($estado !== 'anulado') {
            $estado = !empty($ordenData['QuienRecibe']) ? 'recibido' : 'entregado';
        }

        $ordenData['Estado'] = $estado;

        if ($this->ordenModel->update($id, $ordenData)) {
            $this->setFlash('success', 'Orden actualizada correctamente.');
            $this->redirect('ordenes-externas/view/' . $id);
        } else {
            $this->setFlash('error', 'No se pudo actualizar la orden.');
            $this->redirect('ordenes-externas/edit/' . $id);
        }
    }

    /**
     * Registrar el retorno del equipo (marcar como recibida)
     */
    public function recibir($id) {
        $this->requierePermiso(self::OPCION_LISTADO, 'No tiene permisos para editar órdenes externas.');

        $orden = $this->ordenModel->getById($id);

        if (!$orden) {
            $this->json(['success' => false, 'message' => 'Orden no encontrada.'], 404);
        }

        if ($orden['Estado'] === 'anulado') {
            $this->json(['success' => false, 'message' => 'La orden está anulada.'], 400);
        }

        $data = $this->getPostData();
        $quienRecibe = $data['QuienRecibe'] ?? ($_SESSION['usuario_id'] ?? null);
        $fechaRecibe = $data['FechaRecibe'] ?? date('Y-m-d');

        if (empty($quienRecibe)) {
            $this->json(['success' => false, 'message' => 'Debe indicar quién recibe el producto.'], 400);
        }

        if ($this->ordenModel->marcarRecibida($id, $quienRecibe, $fechaRecibe)) {
            $this->json(['success' => true, 'message' => 'Orden marcada como recibida.']);
        }

        $this->json(['success' => false, 'message' => 'No se pudo actualizar la orden.'], 500);
    }

    /**
     * Anular la orden (conserva la trazabilidad)
     */
    public function anular($id) {
        $this->requierePermiso(self::OPCION_LISTADO, 'No tiene permisos para editar órdenes externas.');

        if (!$this->ordenModel->getById($id)) {
            $this->json(['success' => false, 'message' => 'Orden no encontrada.'], 404);
        }

        if ($this->ordenModel->anular($id)) {
            $this->json(['success' => true, 'message' => 'Orden anulada.']);
        }

        $this->json(['success' => false, 'message' => 'No se pudo anular la orden.'], 500);
    }

    /**
     * Eliminar definitivamente
     */
    public function delete($id) {
        $this->requierePermiso(self::OPCION_LISTADO, 'No tiene permisos para eliminar órdenes externas.');

        if (!$this->ordenModel->getById($id)) {
            $this->json(['success' => false, 'message' => 'Orden no encontrada.'], 404);
        }

        if ($this->ordenModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Orden eliminada.']);
        }

        $this->json(['success' => false, 'message' => 'No se pudo eliminar la orden.'], 500);
    }

    /**
     * Vista de impresión (remisión) - se renderiza sin el layout del sistema
     */
    public function imprimir($id) {
        $this->requierePermiso(self::OPCION_LISTADO, 'No tiene permisos para imprimir órdenes externas.');

        $orden = $this->ordenModel->getByIdWithDetails($id);

        if (!$orden) {
            $this->setFlash('error', 'Orden no encontrada.');
            $this->redirect('ordenes-externas');
            return;
        }

        if (!class_exists('EmpresaHelper')) {
            require_once __DIR__ . '/../helpers/EmpresaHelper.php';
        }

        $estados = OrdenExterna::getEstados();

        include __DIR__ . '/../views/ordenes_externas/imprimir.php';
    }

    /**
     * Exportar el resultado de los filtros a CSV
     */
    public function exportar() {
        $this->requierePermiso(self::OPCION_LISTADO, 'No tiene permisos para exportar órdenes externas.');

        $filtros = $this->obtenerFiltros();
        $ordenes = $this->ordenModel->getTodasFiltradas($filtros);

        while (ob_get_level()) {
            ob_end_clean();
        }

        $nombreArchivo = 'ordenes_externas_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Cache-Control: no-cache, must-revalidate');

        // BOM para que Excel reconozca UTF-8
        echo "\xEF\xBB\xBF";

        $columnas = [
            'Cod. Orden', 'Fecha', 'Tecnico Externo', 'Taller', 'Detalle del Producto',
            'Servicio Relacionado', 'Motivo', 'Quien Entrega', 'Quien Recibe',
            'Fecha Recibido', 'Observaciones', 'Precio', 'Estado'
        ];
        echo implode(';', $columnas) . "\r\n";

        foreach ($ordenes as $orden) {
            $fila = [
                $orden['CodOrden'],
                $orden['Fecha'],
                $orden['tecnico_nombre'],
                $orden['tecnico_taller'] ?? '',
                $orden['DetalleProducto'],
                !empty($orden['IdServicio']) ? '#' . (int)$orden['IdServicio'] : '',
                $orden['motivo_descripcion'],
                $orden['entrega_nombre'],
                $orden['recibe_nombre'] ?? '',
                $orden['FechaRecibe'] ?? '',
                $orden['Observaciones'] ?? '',
                number_format((float)$orden['Precio'], 2, ',', ''),
                $orden['Estado']
            ];

            // Escapar el separador y los saltos de línea de cada celda
            $fila = array_map(function ($valor) {
                $valor = str_replace(["\r\n", "\r", "\n"], ' ', (string)$valor);
                $valor = str_replace('"', '""', $valor);
                return '"' . $valor . '"';
            }, $fila);

            echo implode(';', $fila) . "\r\n";
        }

        exit;
    }

    // ------------------------------------------------------------------
    // Helpers privados
    // ------------------------------------------------------------------

    /**
     * Validaciones del formulario. Los tres datos clave son obligatorios:
     * técnico externo, código de orden y detalle del producto.
     */
    private function validarDatos($data, $idActual = null) {
        $errores = [];

        $codOrden = trim($data['CodOrden'] ?? '');
        if ($codOrden === '') {
            $errores[] = 'El código de la orden es obligatorio.';
        } elseif ($this->ordenModel->existeCodigo($codOrden, $idActual)) {
            $errores[] = 'El código de orden "' . $codOrden . '" ya está registrado.';
        }

        if (empty($data['IdTecnicoExterno'])) {
            $errores[] = 'Debe seleccionar el técnico externo.';
        } elseif (!$this->tecnicoModel->getById($data['IdTecnicoExterno'])) {
            $errores[] = 'El técnico externo seleccionado no existe.';
        }

        if (trim($data['DetalleProducto'] ?? '') === '') {
            $errores[] = 'El detalle del producto es obligatorio.';
        }

        if (empty($data['IdMotivo'])) {
            $errores[] = 'Debe seleccionar el motivo.';
        } elseif (!$this->motivoModel->getById($data['IdMotivo'])) {
            $errores[] = 'El motivo seleccionado no existe.';
        }

        if (empty($data['Fecha'])) {
            $errores[] = 'La fecha es obligatoria.';
        }

        if (empty($data['QuienEntrega'])) {
            $errores[] = 'Debe indicar quién entrega el producto.';
        }

        if (isset($data['Precio']) && $data['Precio'] !== '' && (float)$data['Precio'] < 0) {
            $errores[] = 'El precio no puede ser negativo.';
        }

        return $errores;
    }

    /**
     * Normalizar los datos del formulario antes de persistir
     */
    private function prepararDatos($data) {
        $quienRecibe = trim((string)($data['QuienRecibe'] ?? ''));
        $fechaRecibe = trim((string)($data['FechaRecibe'] ?? ''));
        $idServicio  = trim((string)($data['IdServicio'] ?? ''));

        return [
            'CodOrden'         => trim($data['CodOrden']),
            'Fecha'            => $data['Fecha'],
            'IdTecnicoExterno' => (int)$data['IdTecnicoExterno'],
            'DetalleProducto'  => trim($data['DetalleProducto']),
            'IdMotivo'         => (int)$data['IdMotivo'],
            'QuienEntrega'     => (int)$data['QuienEntrega'],
            'QuienRecibe'      => $quienRecibe !== '' ? (int)$quienRecibe : null,
            'FechaRecibe'      => $fechaRecibe !== '' ? $fechaRecibe : null,
            'Observaciones'    => trim($data['Observaciones'] ?? '') ?: null,
            'Precio'           => isset($data['Precio']) && $data['Precio'] !== '' ? (float)$data['Precio'] : 0,
            'IdServicio'       => $idServicio !== '' ? (int)$idServicio : null,
            'Estado'           => !empty($quienRecibe) ? 'recibido' : 'entregado'
        ];
    }
}
