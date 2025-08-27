<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Modelo EstadoServicio - Manejo de estados de servicios
 */
class EstadoServicio extends BaseModel {
    protected $table = 'estadoentaller';
    protected $primaryKey = 'IdEstadoEnTaller';

    /**
     * Obtener todos los estados ordenados por descripción
     */
    public function getAllOrdered() {
        $sql = "SELECT * FROM {$this->table} ORDER BY Descripcion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener estado por descripción
     */
    public function findByDescripcion($descripcion) {
        $sql = "SELECT * FROM {$this->table} WHERE Descripcion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$descripcion]);
        return $stmt->fetch();
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

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Verificar si un estado se puede eliminar (no tiene servicios asociados)
     */
    public function canDelete($id) {
        $sql = "SELECT COUNT(*) as count FROM servicio WHERE IdEstadoEnTaller = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result['count'] == 0;
    }

    /**
     * Eliminar estado con verificación de uso
     */
    public function safeDelete($id) {
        if (!$this->canDelete($id)) {
            return false;
        }

        return $this->delete($id);
    }

    /**
     * Obtener estados para dropdown
     */
    public function getForDropdown() {
        $estados = $this->getAllOrdered();
        $dropdown = [];
        foreach ($estados as $estado) {
            $dropdown[] = [
                'id' => $estado['IdEstadoEnTaller'],
                'descripcion' => $estado['Descripcion']
            ];
        }
        return $dropdown;
    }
}