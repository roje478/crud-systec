<?php
/**
 * Modelo Clausula - Gestión de cláusulas del sistema
 */
class Clausula extends BaseModel {
    protected $table = 'clausulas';
    protected $primaryKey = 'id';

    // Obtener todas las cláusulas activas ordenadas
    public function getClausulasActivas() {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY orden, titulo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener cláusulas por tipo
    public function getClausulasPorTipo($tipo) {
        $sql = "SELECT * FROM {$this->table} WHERE tipo = ? AND activo = 1 ORDER BY orden";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tipo]);
        return $stmt->fetchAll();
    }

    // Obtener cláusulas para mostrar en servicios
    public function getClausulasParaServicios() {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY orden";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener tipos de cláusulas disponibles
    public function getTiposClausulas() {
        return [
            'general' => 'General',
            'servicio' => 'Servicio',
            'garantia' => 'Garantía',
            'entrega' => 'Entrega',
            'pago' => 'Pago'
        ];
    }

    // Crear nueva cláusula
    public function create($data) {
        // Generar código único si no se proporciona
        if (empty($data['codigo'])) {
            $data['codigo'] = $this->generarCodigoUnico();
        }

        // Establecer orden si no se proporciona
        if (empty($data['orden'])) {
            $data['orden'] = $this->getSiguienteOrden();
        }

        return parent::create($data);
    }

    // Generar código único para cláusula
    private function generarCodigoUnico() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        $numero = $result['total'] + 1;

        return 'CLAUSULA_' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    // Obtener siguiente orden disponible
    private function getSiguienteOrden() {
        $sql = "SELECT MAX(orden) as max_orden FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        return ($result['max_orden'] ?? 0) + 1;
    }

    // Cambiar estado de cláusula
    public function cambiarEstado($id, $activo) {
        $sql = "UPDATE {$this->table} SET activo = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$activo, $id]);
    }

    // Reordenar cláusulas
    public function reordenar($ordenes) {
        try {
            $this->db->beginTransaction();

            foreach ($ordenes as $id => $orden) {
                $sql = "UPDATE {$this->table} SET orden = ? WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$orden, $id]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Buscar cláusulas
    public function buscar($termino) {
        $sql = "SELECT * FROM {$this->table} WHERE
                (titulo LIKE ? OR descripcion LIKE ? OR codigo LIKE ?)
                AND activo = 1
                ORDER BY orden, titulo";
        $termino = "%{$termino}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$termino, $termino, $termino]);
        return $stmt->fetchAll();
    }
}