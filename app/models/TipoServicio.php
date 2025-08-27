<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Modelo TipoServicio - Manejo de tipos de servicio
 */
class TipoServicio extends BaseModel {
    protected $table = 'tiposervicio';
    protected $primaryKey = 'IdTipoServicio';

    /**
     * Obtener todos los tipos de servicio ordenados por descripción
     */
    public function getAllOrdered() {
        $sql = "SELECT * FROM {$this->table} ORDER BY Descripcion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener tipo de servicio por descripción
     */
    public function findByDescripcion($descripcion) {
        $sql = "SELECT * FROM {$this->table} WHERE Descripcion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$descripcion]);
        return $stmt->fetch();
    }

    /**
     * Crear tipos de servicio por defecto del sistema
     */
    public function createDefaultTypes() {
        $defaultTypes = [
            'Mantenimiento preventivo',
            'Reparación',
            'Instalación',
            'Actualización de software',
            'Limpieza',
            'Diagnóstico',
            'Calibración',
            'Reemplazo de piezas'
        ];

        $createdTypes = [];

        foreach ($defaultTypes as $type) {
            // Verificar si ya existe
            $existing = $this->findByDescripcion($type);

            if (!$existing) {
                $id = $this->create(['Descripcion' => $type]);
                if ($id) {
                    $createdTypes[] = $type;
                }
            }
        }

        return $createdTypes;
    }

    /**
     * Obtener estadísticas de uso de tipos de servicio
     */
    public function getUsageStats() {
        $sql = "
            SELECT
                t.IdTipoServicio,
                t.Descripcion,
                COUNT(s.IdServicio) as total_servicios,
                COUNT(CASE WHEN s.FechaIngreso >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as servicios_mes
            FROM {$this->table} t
            LEFT JOIN servicio s ON t.IdTipoServicio = s.IdTipoServicio
            GROUP BY t.IdTipoServicio, t.Descripcion
            ORDER BY total_servicios DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Verificar si un tipo de servicio se puede eliminar (no tiene servicios asociados)
     */
    public function canDelete($id) {
        $sql = "SELECT COUNT(*) as count FROM servicio WHERE IdTipoServicio = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result['count'] == 0;
    }

    /**
     * Eliminar tipo de servicio con verificación de uso
     */
    public function safeDelete($id) {
        if (!$this->canDelete($id)) {
            return false;
        }

        return $this->delete($id);
    }

    /**
     * Obtener tipos de servicio para dropdown
     */
    public function getForDropdown() {
        $tipos = $this->getAllOrdered();
        $dropdown = [];
        foreach ($tipos as $tipo) {
            $dropdown[] = [
                'id' => $tipo['IdTipoServicio'],
                'descripcion' => $tipo['Descripcion']
            ];
        }
        return $dropdown;
    }
}