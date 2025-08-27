<?php
/**
 * Modelo Empresa - Gestión de información de la empresa
 */

class Empresa extends BaseModel {
    protected $table = 'empresa';
    protected $primaryKey = 'id';

    /**
     * Obtener información de la empresa
     */
    public function getInformacionEmpresa() {
        $sql = "SELECT * FROM {$this->table} WHERE id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Actualizar información de la empresa
     */
    public function actualizarInformacion($data) {
        $sql = "UPDATE {$this->table} SET
                nit = :nit,
                nombreempresa = :nombreempresa,
                correo = :correo,
                direccion = :direccion,
                telefono = :telefono,
                nombrerepresentante = :nombrerepresentante,
                descripcion = :descripcion,
                logo = :logo
                WHERE id = 1";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nit' => $data['nit'],
            'nombreempresa' => $data['nombreempresa'],
            'correo' => $data['correo'],
            'direccion' => $data['direccion'],
            'telefono' => $data['telefono'],
            'nombrerepresentante' => $data['nombrerepresentante'],
            'descripcion' => $data['descripcion'],
            'logo' => $data['logo'] ?? null
        ]);
    }

    /**
     * Procesar y guardar logo de la empresa
     */
    public function procesarLogo($file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return null;
        }

        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Solo se permiten archivos JPG, PNG y GIF');
        }

        // Validar tamaño (máximo 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception('El archivo es demasiado grande. Máximo 2MB');
        }

        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'logo_empresa_' . time() . '.' . $extension;
        $uploadPath = dirname(__DIR__) . '/assets/images/logos/' . $filename;

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return 'app/assets/images/logos/' . $filename;
        } else {
            // Si move_uploaded_file falla, intentar con copy
            if (copy($file['tmp_name'], $uploadPath)) {
                return 'app/assets/images/logos/' . $filename;
            } else {
                // Obtener información detallada del error
                $error = error_get_last();
                $errorMsg = 'Error al subir el archivo';

                if ($error) {
                    $errorMsg .= ': ' . $error['message'];
                }

                // Verificar permisos del directorio
                if (!is_writable(dirname($uploadPath))) {
                    $errorMsg .= ' - El directorio no tiene permisos de escritura';
                }

                // Verificar espacio en disco
                $freeSpace = @disk_free_space(dirname($uploadPath));
                if ($freeSpace !== false && $freeSpace < $file['size']) {
                    $errorMsg .= ' - No hay suficiente espacio en disco';
                }

                throw new Exception($errorMsg);
            }
        }
    }

    /**
     * Eliminar logo anterior si existe
     */
    public function eliminarLogoAnterior($logoActual) {
        if ($logoActual && file_exists(__DIR__ . '/../../' . $logoActual)) {
            unlink(__DIR__ . '/../../' . $logoActual);
        }
    }

    /**
     * Validar datos de la empresa
     */
    public function validarDatos($data) {
        $errors = [];

        if (empty(trim($data['nombreempresa']))) {
            $errors['nombreempresa'] = 'El nombre de la empresa es requerido';
        }

        if (empty(trim($data['correo']))) {
            $errors['correo'] = 'El correo electrónico es requerido';
        } elseif (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = 'El correo electrónico no es válido';
        }

        if (empty(trim($data['direccion']))) {
            $errors['direccion'] = 'La dirección es requerida';
        }

        if (empty(trim($data['telefono']))) {
            $errors['telefono'] = 'El teléfono es requerido';
        }

        if (empty(trim($data['nombrerepresentante']))) {
            $errors['nombrerepresentante'] = 'El nombre del representante es requerido';
        }

        return $errors;
    }
}