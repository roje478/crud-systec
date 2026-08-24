<?php
/**
 * Modelo TecnicoExterno - Catálogo de técnicos externos (talleres de terceros)
 */
class TecnicoExterno extends BaseModel {
    protected $table = 'tecnico_externo';
    protected $primaryKey = 'id';

    // Listado completo con el contador de órdenes de cada técnico
    public function getAllWithStats($soloActivos = false, $busqueda = '') {
        $where = [];
        $params = [];

        if ($soloActivos) {
            $where[] = "t.activo = 1";
        }

        if ($busqueda !== '') {
            $where[] = "(t.nombre LIKE ? OR t.documento LIKE ? OR t.taller LIKE ? OR t.telefono LIKE ?)";
            $like = '%' . $busqueda . '%';
            $params = array_merge($params, [$like, $like, $like, $like]);
        }

        $whereSql = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

        $sql = "SELECT
                    t.*,
                    COUNT(o.IdOrden) as total_ordenes,
                    COUNT(CASE WHEN o.Estado = 'entregado' THEN 1 END) as ordenes_pendientes,
                    COALESCE(SUM(CASE WHEN o.Estado <> 'anulado' THEN o.Precio END), 0) as monto_total
                FROM {$this->table} t
                LEFT JOIN orden_tecnico_externo o ON o.IdTecnicoExterno = t.id
                {$whereSql}
                GROUP BY t.id
                ORDER BY t.activo DESC, t.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Solo los activos, para poblar los selects del formulario de órdenes
    public function getActivos() {
        $sql = "SELECT id, nombre, documento, telefono, taller, especialidad
                FROM {$this->table}
                WHERE activo = 1
                ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Buscar por documento (para evitar duplicados)
    public function findByDocumento($documento) {
        $documento = trim($documento);
        if ($documento === '') {
            return false;
        }
        $sql = "SELECT * FROM {$this->table} WHERE documento = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documento]);
        return $stmt->fetch();
    }

    // Buscar por nombre exacto (para evitar duplicados)
    public function findByNombre($nombre) {
        $sql = "SELECT * FROM {$this->table} WHERE nombre = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([trim($nombre)]);
        return $stmt->fetch();
    }

    // Ficha del técnico junto con sus totales
    public function getByIdWithStats($id) {
        $sql = "SELECT
                    t.*,
                    COUNT(o.IdOrden) as total_ordenes,
                    COUNT(CASE WHEN o.Estado = 'entregado' THEN 1 END) as ordenes_pendientes,
                    COUNT(CASE WHEN o.Estado = 'recibido' THEN 1 END) as ordenes_recibidas,
                    COALESCE(SUM(CASE WHEN o.Estado <> 'anulado' THEN o.Precio END), 0) as monto_total
                FROM {$this->table} t
                LEFT JOIN orden_tecnico_externo o ON o.IdTecnicoExterno = t.id
                WHERE t.id = ?
                GROUP BY t.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Contar órdenes del técnico
    public function contarOrdenes($id) {
        $sql = "SELECT COUNT(*) as total FROM orden_tecnico_externo WHERE IdTecnicoExterno = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    // Un técnico con órdenes no se elimina, se desactiva
    public function canDelete($id) {
        return $this->contarOrdenes($id) === 0;
    }

    public function safeDelete($id) {
        if (!$this->canDelete($id)) {
            return false;
        }
        return $this->delete($id);
    }

    // Activar / desactivar (baja lógica)
    public function toggleEstado($id) {
        $tecnico = $this->getById($id);
        if (!$tecnico) {
            return false;
        }
        $nuevoEstado = $tecnico['activo'] ? 0 : 1;
        return $this->update($id, ['activo' => $nuevoEstado]);
    }

    // Totales para las tarjetas del listado
    public function getResumen() {
        $sql = "SELECT
                    COUNT(*) as total,
                    COUNT(CASE WHEN activo = 1 THEN 1 END) as activos,
                    COUNT(CASE WHEN activo = 0 THEN 1 END) as inactivos
                FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch() ?: ['total' => 0, 'activos' => 0, 'inactivos' => 0];
    }
}
