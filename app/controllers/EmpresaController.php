<?php
/**
 * Controlador Empresa - Gestión de información de la empresa
 */

class EmpresaController extends BaseController {
    private $empresaModel;

    public function __construct() {
        $this->empresaModel = new Empresa();
    }

    /**
     * Mostrar formulario de edición de información de la empresa
     */
    public function index() {
        $empresa = $this->empresaModel->getInformacionEmpresa();

        if (!$empresa) {
            $this->setFlash('error', 'No se encontró información de la empresa');
            // Limpiar cualquier salida anterior antes de redirigir
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->redirect('servicios');
            return;
        }

        $this->render('empresa/edit', [
            'empresa' => $empresa
        ]);
    }

    /**
     * Actualizar información de la empresa
     */
    public function update() {
        $data = $this->getPostData();

        // Validar datos
        $errors = $this->empresaModel->validarDatos($data);

        if (!empty($errors)) {
            $this->setFlash('error', 'Por favor corrija los errores en el formulario');
            // Limpiar cualquier salida anterior antes de redirigir
            if (ob_get_level()) {
                ob_end_clean();
            }
            $this->redirect('empresa');
            return;
        }

        // Procesar logo si se subió uno nuevo
        $logoPath = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            try {
                $logoPath = $this->empresaModel->procesarLogo($_FILES['logo']);
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
                // Limpiar cualquier salida anterior antes de redirigir
                if (ob_get_level()) {
                    ob_end_clean();
                }
                $this->redirect('empresa');
                return;
            }
        }

        // Obtener logo actual para eliminarlo si se sube uno nuevo
        $empresaActual = $this->empresaModel->getInformacionEmpresa();
        $logoActual = $empresaActual['logo'] ?? null;

        // Preparar datos
        $empresaData = [
            'nit' => $data['nit'] ?? '',
            'nombreempresa' => trim($data['nombreempresa']),
            'correo' => trim($data['correo']),
            'direccion' => trim($data['direccion']),
            'telefono' => trim($data['telefono']),
            'nombrerepresentante' => trim($data['nombrerepresentante']),
            'descripcion' => trim($data['descripcion'] ?? ''),
            'logo' => $logoPath ?? $logoActual // Mantener logo actual si no se sube uno nuevo
        ];

        $result = $this->empresaModel->actualizarInformacion($empresaData);

        if ($result) {
            // Eliminar logo anterior si se subió uno nuevo
            if ($logoPath && $logoActual && $logoPath !== $logoActual) {
                $this->empresaModel->eliminarLogoAnterior($logoActual);
            }

            $this->setFlash('success', 'Información de la empresa actualizada correctamente');
        } else {
            $this->setFlash('error', 'Error al actualizar la información de la empresa');
        }

        // Limpiar cualquier salida anterior antes de redirigir
        if (ob_get_level()) {
            ob_end_clean();
        }

        $this->redirect('empresa');
    }

    /**
     * Obtener información de la empresa (AJAX)
     */
    public function getInfo() {
        // Limpiar cualquier salida anterior
        if (ob_get_level()) {
            ob_end_clean();
        }

        $empresa = $this->empresaModel->getInformacionEmpresa();

        if ($empresa) {
            $this->json(['success' => true, 'data' => $empresa]);
        } else {
            $this->json(['success' => false, 'message' => 'No se encontró información de la empresa'], 404);
        }
    }
}
?>