<?php
/**
 * Modelo OrdenExterna - Órdenes entregadas a técnicos externos
 */
class OrdenExterna extends BaseModel {
    protected $table = 'orden_tecnico_externo';
    protected $primaryKey = 'IdOrden';

    // Prefijo del consecutivo: OE-2026-0001
    const PREFIJO_CODIGO = 'OE';

    /**
     * SELECT base con todos los JOIN de la orden
     */
    private function selectBase() {
        return "SELECT
                    o.*,
                    t.nombre        as tecnico_nombre,
                    t.documento     as tecnico_documento,
                    t.telefono      as tecnico_telefono,
                    t.correo        as tecnico_correo,
                    t.taller        as tecnico_taller,
                    m.descripcion   as motivo_descripcion,
                    m.color         as motivo_color,
                    TRIM(CONCAT(COALESCE(ce.nombres, ''), ' ', COALESCE(ce.apellidos, ''))) as entrega_nombre,
                    TRIM(CONCAT(COALESCE(cr.nombres, ''), ' ', COALESCE(cr.apellidos, ''))) as recibe_nombre,
                    TRIM(COALESCE(ce.nombres, '')) as entrega_nombres,
                    TRIM(COALESCE(cr.nombres, '')) as recibe_nombres,
                    TRIM(CONCAT(COALESCE(cg.nombres, ''), ' ', COALESCE(cg.apellidos, ''))) as registrado_nombre
                FROM {$this->table} o
                INNER JOIN tecnico_externo t      ON t.id = o.IdTecnicoExterno
                INNER JOIN motivo_orden_externa m ON m.id = o.IdMotivo
                LEFT JOIN cliente ce ON ce.no_identificacion = o.QuienEntrega
                LEFT JOIN cliente cr ON cr.no_identificacion = o.QuienRecibe
                LEFT JOIN cliente cg ON cg.no_identificacion = o.RegistradoPor";
    }

    /**
     * Construir el WHERE a partir de los filtros del listado
     * Devuelve [$whereSql, $params]
     */
    private function construirFiltros($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $where[] = "(o.CodOrden LIKE ? OR o.DetalleProducto LIKE ? OR o.Observaciones LIKE ?)";
            $like = '%' . $filtros['busqueda'] . '%';
            $params = array_merge($params, [$like, $like, $like]);
        }

        if (!empty($filtros['tecnico'])) {
            $where[] = "o.IdTecnicoExterno = ?";
            $params[] = $filtros['tecnico'];
        }

        if (!empty($filtros['motivo'])) {
            $where[] = "o.IdMotivo = ?";
            $params[] = $filtros['motivo'];
        }

        if (!empty($filtros['estado'])) {
            $where[] = "o.Estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $where[] = "o.Fecha >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = "o.Fecha <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $whereSql = empty($where) ? '' : ' WHERE ' . implode(' AND ', $where);
        return [$whereSql, $params];
    }

    /**
     * Listado paginado con filtros
     */
    public function getFiltradas($filtros = [], $page = 1, $perPage = 25) {
        list($whereSql, $params) = $this->construirFiltros($filtros);

        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;

        $sql = $this->selectBase() . $whereSql . " ORDER BY o.Fecha DESC, o.IdOrden DESC LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Total de registros que cumplen los filtros (para la paginación)
     */
    public function contarFiltradas($filtros = []) {
        list($whereSql, $params) = $this->construirFiltros($filtros);

        $sql = "SELECT COUNT(*) as total
                FROM {$this->table} o
                INNER JOIN tecnico_externo t      ON t.id = o.IdTecnicoExterno
                INNER JOIN motivo_orden_externa m ON m.id = o.IdMotivo"
                . $whereSql;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    /**
     * Información de paginación
     */
    public function getPaginationInfo($filtros = [], $page = 1, $perPage = 25) {
        $total = $this->contarFiltradas($filtros);
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $perPage;

        return [
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
            'total_pages'  => $totalPages,
            'has_previous' => $page > 1,
            'has_next'     => $page < $totalPages,
            'start_record' => $total > 0 ? $offset + 1 : 0,
            'end_record'   => min($offset + $perPage, $total)
        ];
    }

    /**
     * Todas las órdenes que cumplen los filtros, sin paginar (exportación)
     */
    public function getTodasFiltradas($filtros = []) {
        list($whereSql, $params) = $this->construirFiltros($filtros);
        $sql = $this->selectBase() . $whereSql . " ORDER BY o.Fecha DESC, o.IdOrden DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Una orden con todos sus datos relacionados
     */
    public function getByIdWithDetails($id) {
        $sql = $this->selectBase() . " WHERE o.IdOrden = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Órdenes de un técnico externo concreto (historial de su ficha)
     */
    public function getByTecnico($idTecnico, $limite = 100) {
        $limite = max(1, (int)$limite);
        $sql = $this->selectBase() . " WHERE o.IdTecnicoExterno = ? ORDER BY o.Fecha DESC, o.IdOrden DESC LIMIT {$limite}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idTecnico]);
        return $stmt->fetchAll();
    }

    /**
     * Órdenes vinculadas a un servicio interno, para mostrarlas en su detalle
     */
    public function getByServicio($idServicio) {
        $sql = $this->selectBase() . " WHERE o.IdServicio = ? ORDER BY o.Fecha DESC, o.IdOrden DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idServicio]);
        return $stmt->fetchAll();
    }

    /**
     * Verificar si un código de orden ya existe (opcionalmente excluyendo una orden)
     */
    public function existeCodigo($codOrden, $excluirId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE CodOrden = ?";
        $params = [trim($codOrden)];

        if ($excluirId !== null) {
            $sql .= " AND IdOrden <> ?";
            $params[] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0) > 0;
    }

    /**
     * Generar el siguiente código consecutivo: OE-0001
     *
     * El consecutivo es global (no se reinicia cada año) y lo asigna siempre el
     * sistema al guardar, nunca el formulario.
     */
    public function generarCodigo() {
        $prefijo = self::PREFIJO_CODIGO . '-';

        // Tomar el mayor consecutivo numérico ya asignado
        $sql = "SELECT CodOrden FROM {$this->table}
                WHERE CodOrden REGEXP ?
                ORDER BY CAST(SUBSTRING(CodOrden, ?) AS UNSIGNED) DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            '^' . self::PREFIJO_CODIGO . '-[0-9]+$',
            strlen($prefijo) + 1
        ]);
        $ultimo = $stmt->fetch();

        $consecutivo = 1;
        if ($ultimo && preg_match('/(\d+)$/', $ultimo['CodOrden'], $coincidencias)) {
            $consecutivo = (int)$coincidencias[1] + 1;
        }

        // Si el código ya estuviera ocupado, avanzar hasta encontrar uno libre
        do {
            $codigo = $prefijo . str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
            $consecutivo++;
        } while ($this->existeCodigo($codigo));

        return $codigo;
    }

    /**
     * Marcar la orden como recibida
     */
    public function marcarRecibida($id, $quienRecibe, $fechaRecibe = null) {
        return $this->update($id, [
            'QuienRecibe' => $quienRecibe,
            'FechaRecibe' => $fechaRecibe ?: date('Y-m-d'),
            'Estado'      => 'recibido'
        ]);
    }

    /**
     * Anular la orden (no se borra, queda trazabilidad)
     */
    public function anular($id) {
        return $this->update($id, ['Estado' => 'anulado']);
    }

    /**
     * Totales para las tarjetas del listado
     */
    public function getResumen($filtros = []) {
        list($whereSql, $params) = $this->construirFiltros($filtros);

        $sql = "SELECT
                    COUNT(*) as total,
                    COUNT(CASE WHEN o.Estado = 'entregado' THEN 1 END) as entregadas,
                    COUNT(CASE WHEN o.Estado = 'recibido'  THEN 1 END) as recibidas,
                    COUNT(CASE WHEN o.Estado = 'anulado'   THEN 1 END) as anuladas,
                    COALESCE(SUM(CASE WHEN o.Estado <> 'anulado' THEN o.Precio END), 0) as monto_total
                FROM {$this->table} o
                INNER JOIN tecnico_externo t      ON t.id = o.IdTecnicoExterno
                INNER JOIN motivo_orden_externa m ON m.id = o.IdMotivo"
                . $whereSql;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $resumen = $stmt->fetch();

        return $resumen ?: [
            'total' => 0, 'entregadas' => 0, 'recibidas' => 0,
            'anuladas' => 0, 'monto_total' => 0
        ];
    }

    /**
     * Usuarios internos (empleados del sistema) para los selects
     * de "quién entrega" y "quién recibe"
     */
    public function getResponsablesInternos() {
        $sql = "SELECT DISTINCT
                    u.no_identificacion as id,
                    TRIM(
                        CONCAT(
                            UPPER(LEFT(TRIM(c.nombres), 1)),
                            LOWER(SUBSTRING(TRIM(c.nombres), 2)),
                            ' ',
                            UPPER(LEFT(TRIM(c.apellidos), 1)),
                            LOWER(SUBSTRING(TRIM(c.apellidos), 2))
                        )
                    ) as nombre,
                    p.descripcion as perfil
                FROM usuario u
                INNER JOIN cliente c ON c.no_identificacion = u.no_identificacion
                INNER JOIN perfil p  ON p.codigo_perfil = u.codigo_perfil
                WHERE u.activo = 1 AND p.activo = 1
                ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Etiquetas de los estados
     */
    public static function getEstados() {
        return [
            'entregado' => ['label' => 'Entregado', 'color' => 'warning', 'icono' => 'fas fa-truck'],
            'recibido'  => ['label' => 'Recibido',  'color' => 'success', 'icono' => 'fas fa-check-circle'],
            'anulado'   => ['label' => 'Anulado',   'color' => 'danger',  'icono' => 'fas fa-ban']
        ];
    }
}
