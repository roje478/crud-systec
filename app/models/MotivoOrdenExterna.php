<?php
/**
 * Modelo MotivoOrdenExterna - Catálogo de motivos de las órdenes externas
 * (Reparación, Garantía, Revisión, ...)
 */
class MotivoOrdenExterna extends BaseModel {
    protected $table = 'motivo_orden_externa';
    protected $primaryKey = 'id';

    // Colores disponibles para los badges
    public static function getColoresDisponibles() {
        return [
            'primary'   => 'Azul',
            'success'   => 'Verde',
            'warning'   => 'Amarillo',
            'danger'    => 'Rojo',
            'info'      => 'Celeste',
            'secondary' => 'Gris',
            'dark'      => 'Negro'
        ];
    }

    // Obtener todos los motivos ordenados
    public function getAllOrdered() {
        $sql = "SELECT * FROM {$this->table} ORDER BY orden ASC, descripcion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener solo los motivos activos (para los selects del formulario)
    public function getActivos() {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY orden ASC, descripcion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Buscar por descripción (para evitar duplicados)
    public function findByDescripcion($descripcion) {
        $sql = "SELECT * FROM {$this->table} WHERE descripcion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([trim($descripcion)]);
        return $stmt->fetch();
    }

    // Estadísticas de uso: cuántas órdenes tiene cada motivo
    public function getUsageStats() {
        $sql = "SELECT
                    m.id,
                    m.descripcion,
                    m.color,
                    COUNT(o.IdOrden) as total_ordenes,
                    COUNT(CASE WHEN o.Fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as ordenes_mes
                FROM {$this->table} m
                LEFT JOIN orden_tecnico_externo o ON o.IdMotivo = m.id
                GROUP BY m.id, m.descripcion, m.color
                ORDER BY total_ordenes DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Contar órdenes asociadas a un motivo
    public function contarOrdenes($id) {
        $sql = "SELECT COUNT(*) as total FROM orden_tecnico_externo WHERE IdMotivo = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    // Un motivo solo se puede eliminar si no tiene órdenes asociadas
    public function canDelete($id) {
        return $this->contarOrdenes($id) === 0;
    }

    // Eliminar con verificación previa
    public function safeDelete($id) {
        if (!$this->canDelete($id)) {
            return false;
        }
        return $this->delete($id);
    }

    // Siguiente número de orden de aparición
    public function getSiguienteOrden() {
        $sql = "SELECT MAX(orden) as max_orden FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)($result['max_orden'] ?? 0) + 1;
    }

    // Activar / desactivar
    public function toggleEstado($id) {
        $motivo = $this->getById($id);
        if (!$motivo) {
            return false;
        }
        $nuevoEstado = $motivo['activo'] ? 0 : 1;
        return $this->update($id, ['activo' => $nuevoEstado]);
    }
}
