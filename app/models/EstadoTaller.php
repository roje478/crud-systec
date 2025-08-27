<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Modelo EstadoTaller - Manejo de estados de servicios
 */
class EstadoTaller extends BaseModel {
    protected $table = 'estadoentaller';
    protected $primaryKey = 'IdEstadoEnTaller';
    protected $fillable = ['Descripcion'];

    /**
     * Validaciones para estado taller
     */
    protected function validate($data, $id = null) {
        $this->clearErrors();

        // Descripción es requerida
        $this->validateRequired($data, 'Descripcion', 'La descripción del estado es requerida');

        // Longitud mínima y máxima
        $this->validateMinLength($data, 'Descripcion', 3, 'La descripción debe tener al menos 3 caracteres');
        $this->validateMaxLength($data, 'Descripcion', 100, 'La descripción no puede tener más de 100 caracteres');

        // Verificar que no exista otro estado con la misma descripción
        if (isset($data['Descripcion'])) {
            $existingQuery = "SELECT COUNT(*) as count FROM {$this->table} WHERE Descripcion = ?";
            $params = [$data['Descripcion']];

            // Si es actualización, excluir el registro actual
            if ($id) {
                $existingQuery .= " AND {$this->primaryKey} != ?";
                $params[] = $id;
            }

            $result = $this->db->fetchOne($existingQuery, $params);
            if ($result['count'] > 0) {
                $this->addError('Descripcion', 'Ya existe un estado con esta descripción');
            }
        }

        return !$this->hasErrors();
    }

    /**
     * Obtener todos los estados ordenados por descripción
     */
    public function getAllOrdered() {
        return $this->all('Descripcion ASC');
    }

    /**
     * Obtener estado por descripción
     */
    public function findByDescripcion($descripcion) {
        return $this->whereFirst('Descripcion = ?', [$descripcion]);
    }

    /**
     * Obtener estados activos (si tienes un campo de estado activo/inactivo)
     */
    public function getActive() {
        // Asumiendo que todos los estados están activos por defecto
        return $this->getAllOrdered();
    }

    /**
     * Crear estados por defecto del sistema
     */
    public function createDefaultStates() {
        $defaultStates = [
            'En mantenimiento',
            'Pendiente de revisión',
            'En espera de repuestos',
            'Reparado',
            'Terminado',
            'Entregado',
            'Cancelado'
        ];

        $createdStates = [];

        foreach ($defaultStates as $state) {
            // Verificar si ya existe
            $existing = $this->findByDescripcion($state);

            if (!$existing) {
                $id = $this->create(['Descripcion' => $state]);
                if ($id) {
                    $createdStates[] = $state;
                }
            }
        }

        return $createdStates;
    }

    /**
     * Obtener estadísticas de uso de estados
     */
    public function getUsageStats() {
        $sql = "
            SELECT
                e.IdEstadoEnTaller,
                e.Descripcion,
                COUNT(s.IdServicio) as total_servicios,
                COUNT(CASE WHEN s.FechaIngreso >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as servicios_mes
            FROM {$this->table} e
            LEFT JOIN servicio s ON e.IdEstadoEnTaller = s.IdEstadoEnTaller
            GROUP BY e.IdEstadoEnTaller, e.Descripcion
            ORDER BY total_servicios DESC
        ";

        return $this->db->fetchAll($sql);
    }

    /**
     * Verificar si un estado se puede eliminar (no tiene servicios asociados)
     */
    public function canDelete($id) {
        $sql = "SELECT COUNT(*) as count FROM servicio WHERE IdEstadoEnTaller = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        return $result['count'] == 0;
    }

    /**
     * Eliminar estado con verificación de uso
     */
    public function safeDelete($id) {
        if (!$this->canDelete($id)) {
            $this->addError('delete', 'No se puede eliminar este estado porque tiene servicios asociados');
            return false;
        }

        return $this->delete($id);
    }
}