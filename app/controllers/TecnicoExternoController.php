<?php
/**
 * TecnicoExternoController - CRUD del catálogo de técnicos externos
 *
 * Opción de permisos asociada: TE03 (Gestionar Técnicos Externos)
 */
class TecnicoExternoController extends BaseController {
    // Código de la opción en la tabla `opciones`
    const OPCION_PERMISO = 'TE03';

    private $tecnicoModel;
    private $ordenModel;
    private $permisoModel = null;

    public function __construct() {
        $this->verificarAcceso();
        $this->tecnicoModel = new TecnicoExterno();
        $this->ordenModel = new OrdenExterna();
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
     * Guarda de permisos: ocultar la opción del menú no basta,
     * la ruta también debe estar protegida.
     */
    private function verificarAcceso() {
        if (!$this->tienePermisoOpcion(self::OPCION_PERMISO)) {
            $this->setFlash('error', 'No tiene permisos para gestionar técnicos externos.');
            $this->redirect('servicios');
        }
    }

    /**
     * Listado del catálogo
     */
    public function index() {
        $busqueda = trim($_GET['q'] ?? '');
        $filtroEstado = $_GET['estado'] ?? '';
        $soloActivos = ($filtroEstado === 'activos');

        $tecnicos = $this->tecnicoModel->getAllWithStats($soloActivos, $busqueda);

        // El filtro "inactivos" se resuelve en memoria para no duplicar la consulta
        if ($filtroEstado === 'inactivos') {
            $tecnicos = array_values(array_filter($tecnicos, function ($t) {
                return (int)$t['activo'] === 0;
            }));
        }

        $resumen = $this->tecnicoModel->getResumen();

        $this->render('tecnicos_externos/index', compact('tecnicos', 'resumen', 'busqueda', 'filtroEstado'));
    }

    /**
     * Formulario de creación
     */
    public function create() {
        $this->render('tecnicos_externos/create');
    }

    /**
     * Guardar un técnico externo nuevo
     */
    public function store() {
        $data = $this->getPostData();

        $errores = $this->validarDatos($data);
        if (!empty($errores)) {
            $this->setFlash('error', implode(' ', $errores));
            $this->redirect('tecnicos-externos/create');
            return;
        }

        $tecnicoData = $this->prepararDatos($data);
        $tecnicoData['registrado_por'] = $_SESSION['usuario_id'] ?? null;

        $id = $this->tecnicoModel->create($tecnicoData);

        if ($id) {
            $this->setFlash('success', 'Técnico externo creado correctamente.');
            $this->redirect('tecnicos-externos/view/' . $id);
        } else {
            $this->setFlash('error', 'No se pudo crear el técnico externo.');
            $this->redirect('tecnicos-externos/create');
        }
    }

    /**
     * Alta rápida desde el formulario de órdenes (respuesta JSON)
     */
    public function storeAjax() {
        $data = $this->getPostData();

        $errores = $this->validarDatos($data);
        if (!empty($errores)) {
            $this->json(['success' => false, 'message' => implode(' ', $errores)], 400);
        }

        $tecnicoData = $this->prepararDatos($data);
        $tecnicoData['registrado_por'] = $_SESSION['usuario_id'] ?? null;

        $id = $this->tecnicoModel->create($tecnicoData);

        if ($id) {
            $this->json([
                'success' => true,
                'message' => 'Técnico externo creado correctamente.',
                'tecnico' => [
                    'id'     => $id,
                    'nombre' => $tecnicoData['nombre'],
                    'taller' => $tecnicoData['taller']
                ]
            ]);
        }

        $this->json(['success' => false, 'message' => 'No se pudo crear el técnico externo.'], 500);
    }

    /**
     * Ficha del técnico con su historial de órdenes
     */
    public function view($id) {
        $tecnico = $this->tecnicoModel->getByIdWithStats($id);

        if (!$tecnico) {
            $this->setFlash('error', 'Técnico externo no encontrado.');
            $this->redirect('tecnicos-externos');
            return;
        }

        $ordenes = $this->ordenModel->getByTecnico($id);

        $this->render('tecnicos_externos/view', compact('tecnico', 'ordenes'));
    }

    /**
     * Formulario de edición
     */
    public function edit($id) {
        $tecnico = $this->tecnicoModel->getById($id);

        if (!$tecnico) {
            $this->setFlash('error', 'Técnico externo no encontrado.');
            $this->redirect('tecnicos-externos');
            return;
        }

        $totalOrdenes = $this->tecnicoModel->contarOrdenes($id);

        $this->render('tecnicos_externos/edit', compact('tecnico', 'totalOrdenes'));
    }

    /**
     * Actualizar
     */
    public function update($id) {
        $tecnico = $this->tecnicoModel->getById($id);

        if (!$tecnico) {
            $this->setFlash('error', 'Técnico externo no encontrado.');
            $this->redirect('tecnicos-externos');
            return;
        }

        $data = $this->getPostData();

        $errores = $this->validarDatos($data, $id);
        if (!empty($errores)) {
            $this->setFlash('error', implode(' ', $errores));
            $this->redirect('tecnicos-externos/edit/' . $id);
            return;
        }

        $tecnicoData = $this->prepararDatos($data);
        $tecnicoData['activo'] = isset($data['activo']) && $data['activo'] ? 1 : 0;

        if ($this->tecnicoModel->update($id, $tecnicoData)) {
            $this->setFlash('success', 'Técnico externo actualizado correctamente.');
            $this->redirect('tecnicos-externos/view/' . $id);
        } else {
            $this->setFlash('error', 'No se pudo actualizar el técnico externo.');
            $this->redirect('tecnicos-externos/edit/' . $id);
        }
    }

    /**
     * Activar / desactivar (baja lógica)
     */
    public function toggleEstado($id) {
        if (!$this->tecnicoModel->getById($id)) {
            $this->json(['success' => false, 'message' => 'Técnico externo no encontrado.'], 404);
        }

        if ($this->tecnicoModel->toggleEstado($id)) {
            $tecnico = $this->tecnicoModel->getById($id);
            $this->json([
                'success' => true,
                'activo'  => (int)$tecnico['activo'],
                'message' => (int)$tecnico['activo'] === 1
                    ? 'Técnico externo activado.'
                    : 'Técnico externo desactivado.'
            ]);
        }

        $this->json(['success' => false, 'message' => 'No se pudo cambiar el estado.'], 500);
    }

    /**
     * Eliminar definitivamente (solo si no tiene órdenes)
     */
    public function delete($id) {
        if (!$this->tecnicoModel->getById($id)) {
            $this->json(['success' => false, 'message' => 'Técnico externo no encontrado.'], 404);
        }

        if (!$this->tecnicoModel->canDelete($id)) {
            $this->json([
                'success' => false,
                'message' => 'No se puede eliminar: el técnico tiene órdenes registradas. Desactívelo en su lugar.'
            ], 400);
        }

        if ($this->tecnicoModel->safeDelete($id)) {
            $this->json(['success' => true, 'message' => 'Técnico externo eliminado.']);
        }

        $this->json(['success' => false, 'message' => 'No se pudo eliminar el técnico externo.'], 500);
    }

    /**
     * Buscador para autocompletado (JSON)
     */
    public function buscar() {
        $termino = trim($_GET['q'] ?? '');
        $tecnicos = $this->tecnicoModel->getAllWithStats(true, $termino);

        $this->json(['success' => true, 'tecnicos' => $tecnicos]);
    }

    // ------------------------------------------------------------------
    // Helpers privados
    // ------------------------------------------------------------------

    /**
     * Validaciones de negocio. Devuelve un array de mensajes de error.
     */
    private function validarDatos($data, $idActual = null) {
        $errores = [];

        $nombre = trim($data['nombre'] ?? '');
        if ($nombre === '') {
            $errores[] = 'El nombre del técnico externo es obligatorio.';
        }

        $correo = trim($data['correo'] ?? '');
        if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }

        $documento = trim($data['documento'] ?? '');
        if ($documento !== '') {
            $existente = $this->tecnicoModel->findByDocumento($documento);
            if ($existente && (int)$existente['id'] !== (int)$idActual) {
                $errores[] = 'Ya existe un técnico externo con ese documento.';
            }
        }

        if ($nombre !== '') {
            $existente = $this->tecnicoModel->findByNombre($nombre);
            if ($existente && (int)$existente['id'] !== (int)$idActual) {
                $errores[] = 'Ya existe un técnico externo con ese nombre.';
            }
        }

        return $errores;
    }

    /**
     * Normalizar los datos del formulario antes de persistir
     */
    private function prepararDatos($data) {
        $documento = trim($data['documento'] ?? '');

        return [
            'nombre'        => trim($data['nombre']),
            'documento'     => $documento !== '' ? $documento : null,
            'telefono'      => trim($data['telefono'] ?? '') ?: null,
            'correo'        => trim($data['correo'] ?? '') ?: null,
            'taller'        => trim($data['taller'] ?? '') ?: null,
            'direccion'     => trim($data['direccion'] ?? '') ?: null,
            'especialidad'  => trim($data['especialidad'] ?? '') ?: null,
            'observaciones' => trim($data['observaciones'] ?? '') ?: null,
            'activo'        => 1
        ];
    }
}
