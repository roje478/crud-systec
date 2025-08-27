<?php
/**
 * ClausulaController - Controlador para gestión de cláusulas
 */
class ClausulaController extends BaseController {
    private $clausulaModel;

    public function __construct() {
        $this->clausulaModel = new Clausula();
    }

    // Listar cláusulas
    public function index() {
        $clausulas = $this->clausulaModel->getClausulasActivas();
        $tipos = $this->clausulaModel->getTiposClausulas();

        $this->render('clausulas/index', compact('clausulas', 'tipos'));
    }

    // Mostrar formulario de creación
    public function create() {
        $tipos = $this->clausulaModel->getTiposClausulas();
        $this->render('clausulas/create', compact('tipos'));
    }

    // Guardar nueva cláusula
    public function store() {
        $data = $this->getPostData();

        // Validación básica
        $required = ['titulo', 'descripcion', 'tipo'];
        $errors = $this->validateRequired($data, $required);

        if (!empty($errors)) {
            $this->setFlash('error', 'Por favor complete todos los campos requeridos');
            $this->redirect('clausulas/create');
            return;
        }

        // Preparar datos
        $clausulaData = [
            'titulo' => trim($data['titulo']),
            'descripcion' => trim($data['descripcion']),
            'tipo' => $data['tipo'],
            'orden' => $data['orden'] ?? null,
            'activo' => 1
        ];

        $result = $this->clausulaModel->create($clausulaData);

        if ($result) {
            $this->setFlash('success', 'Cláusula creada correctamente');
            $this->redirect('clausulas');
        } else {
            $this->setFlash('error', 'Error al crear la cláusula');
            $this->redirect('clausulas/create');
        }
    }

    // Mostrar formulario de edición
    public function edit($id) {
        $clausula = $this->clausulaModel->getById($id);

        if (!$clausula) {
            $this->setFlash('error', 'Cláusula no encontrada');
            $this->redirect('clausulas');
            return;
        }

        $tipos = $this->clausulaModel->getTiposClausulas();
        $this->render('clausulas/edit', compact('clausula', 'tipos'));
    }

    // Actualizar cláusula
    public function update($id) {
        $data = $this->getPostData();

        // Validación básica
        $required = ['titulo', 'descripcion', 'tipo'];
        $errors = $this->validateRequired($data, $required);

        if (!empty($errors)) {
            $this->setFlash('error', 'Por favor complete todos los campos requeridos');
            $this->redirect('clausulas/edit/' . $id);
            return;
        }

        // Preparar datos
        $clausulaData = [
            'titulo' => trim($data['titulo']),
            'descripcion' => trim($data['descripcion']),
            'tipo' => $data['tipo'],
            'orden' => $data['orden'] ?? null
        ];

        $result = $this->clausulaModel->update($id, $clausulaData);

        if ($result) {
            $this->setFlash('success', 'Cláusula actualizada correctamente');
            $this->redirect('clausulas');
        } else {
            $this->setFlash('error', 'Error al actualizar la cláusula');
            $this->redirect('clausulas/edit/' . $id);
        }
    }

    // Cambiar estado de cláusula (AJAX)
    public function cambiarEstado($id) {
        $data = $this->getPostData();
        $activo = $data['activo'] ?? 0;

        $result = $this->clausulaModel->cambiarEstado($id, $activo);

        if ($result) {
            $this->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al actualizar estado'], 400);
        }
    }

    // Eliminar cláusula
    public function delete($id) {
        $result = $this->clausulaModel->delete($id);

        if ($result) {
            $this->setFlash('success', 'Cláusula eliminada correctamente');
        } else {
            $this->setFlash('error', 'Error al eliminar la cláusula');
        }

        $this->redirect('clausulas');
    }

    // Buscar cláusulas (AJAX)
    public function buscar() {
        $termino = $_GET['q'] ?? '';

        if (empty($termino)) {
            $clausulas = $this->clausulaModel->getClausulasActivas();
        } else {
            $clausulas = $this->clausulaModel->buscar($termino);
        }

        $this->json(['success' => true, 'data' => $clausulas]);
    }

    // Reordenar cláusulas (AJAX)
    public function reordenar() {
        $data = $this->getPostData();
        $ordenes = $data['ordenes'] ?? [];

        if (empty($ordenes)) {
            $this->json(['success' => false, 'message' => 'No se recibieron datos de orden'], 400);
            return;
        }

        $result = $this->clausulaModel->reordenar($ordenes);

        if ($result) {
            $this->json(['success' => true, 'message' => 'Orden actualizado correctamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al actualizar orden'], 400);
        }
    }

    // Obtener cláusulas por tipo (AJAX)
    public function getPorTipo($tipo) {
        $clausulas = $this->clausulaModel->getClausulasPorTipo($tipo);
        $this->json(['success' => true, 'data' => $clausulas]);
    }
}