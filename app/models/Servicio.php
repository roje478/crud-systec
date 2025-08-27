<?php
/**
 * Modelo Servicio - Corregido para estructura real de BD
 */
class Servicio extends BaseModel {
    protected $table = 'servicio';
    protected $primaryKey = 'IdServicio';

    // Obtener servicios con información relacionada (sin paginación - método original)
    public function getAllWithDetails() {
        $sql = "SELECT
                    s.*,
                    CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    et.Descripcion as estado_descripcion,
                    ps.FechaSolucion as fecha_programacion,
                    ps.HoraInicio as hora_inicio,
                    ps.HoraFinaliza as hora_fin,
                    CONCAT(t.nombres, ' ', t.apellidos) as tecnico_nombre
                FROM servicio s
                LEFT JOIN cliente c ON s.NoIdentificacionCliente = c.no_identificacion
                LEFT JOIN estadoentaller et ON s.IdEstadoEnTaller = et.IdEstadoEnTaller
                LEFT JOIN programacionservicio ps ON s.IdServicio = ps.IdServicio
                LEFT JOIN cliente t ON s.NoIdentificacionEmpleado = t.no_identificacion
                ORDER BY s.IdServicio DESC
                LIMIT 50";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener TODOS los servicios sin límite para Lista Completa
    public function getAllServiciosCompletos() {
        $sql = "SELECT
                    s.*,
                    CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    et.Descripcion as estado_descripcion,
                    ps.FechaSolucion as fecha_programacion,
                    ps.HoraInicio as hora_inicio,
                    ps.HoraFinaliza as hora_fin,
                    CONCAT(t.nombres, ' ', t.apellidos) as tecnico_nombre
                FROM servicio s
                LEFT JOIN cliente c ON s.NoIdentificacionCliente = c.no_identificacion
                LEFT JOIN estadoentaller et ON s.IdEstadoEnTaller = et.IdEstadoEnTaller
                LEFT JOIN programacionservicio ps ON s.IdServicio = ps.IdServicio
                LEFT JOIN cliente t ON s.NoIdentificacionEmpleado = t.no_identificacion
                ORDER BY s.IdServicio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener servicios asignados a un técnico específico
    public function getServiciosByTecnico($tecnicoId) {
        $sql = "SELECT
                    s.*,
                    CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    et.Descripcion as estado_descripcion,
                    ps.FechaSolucion as fecha_programacion,
                    ps.HoraInicio as hora_inicio,
                    ps.HoraFinaliza as hora_fin,
                    CONCAT(t.nombres, ' ', t.apellidos) as tecnico_nombre
                FROM servicio s
                LEFT JOIN cliente c ON s.NoIdentificacionCliente = c.no_identificacion
                LEFT JOIN estadoentaller et ON s.IdEstadoEnTaller = et.IdEstadoEnTaller
                LEFT JOIN programacionservicio ps ON s.IdServicio = ps.IdServicio
                LEFT JOIN cliente t ON s.NoIdentificacionEmpleado = t.no_identificacion
                WHERE s.NoIdentificacionEmpleado = ?
                ORDER BY s.IdServicio DESC
                LIMIT 50";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tecnicoId]);
        return $stmt->fetchAll();
    }

    // Obtener servicios con paginación
    public function getAllWithDetailsPaginated($page = 1, $perPage = 30) {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT
                    s.*,
                    CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    et.Descripcion as estado_descripcion,
                    ps.FechaSolucion as fecha_programacion,
                    ps.HoraInicio as hora_inicio,
                    ps.HoraFinaliza as hora_fin,
                    CONCAT(t.nombres, ' ', t.apellidos) as tecnico_nombre
                FROM servicio s
                LEFT JOIN cliente c ON s.NoIdentificacionCliente = c.no_identificacion
                LEFT JOIN estadoentaller et ON s.IdEstadoEnTaller = et.IdEstadoEnTaller
                LEFT JOIN programacionservicio ps ON s.IdServicio = ps.IdServicio
                LEFT JOIN cliente t ON s.NoIdentificacionEmpleado = t.no_identificacion
                ORDER BY s.IdServicio DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    // Contar total de servicios
    public function getTotalServices() {
        $sql = "SELECT COUNT(*) as total FROM servicio";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtener total de servicios (alias para compatibilidad)
    public function getTotalServicios() {
        return $this->getTotalServices();
    }

    // Obtener servicios paginados con detalles completos
    public function getServiciosPaginados($offset, $limit) {
        $sql = "SELECT
                    s.*,
                    CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    et.Descripcion as estado_descripcion,
                    ps.FechaSolucion as fecha_programacion,
                    ps.HoraInicio as hora_inicio,
                    ps.HoraFinaliza as hora_fin,
                    CONCAT(t.nombres, ' ', t.apellidos) as tecnico_nombre
                FROM servicio s
                LEFT JOIN cliente c ON s.NoIdentificacionCliente = c.no_identificacion
                LEFT JOIN estadoentaller et ON s.IdEstadoEnTaller = et.IdEstadoEnTaller
                LEFT JOIN programacionservicio ps ON s.IdServicio = ps.IdServicio
                LEFT JOIN cliente t ON s.NoIdentificacionEmpleado = t.no_identificacion
                ORDER BY s.IdServicio DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    // Obtener información de paginación
    public function getPaginationInfo($page = 1, $perPage = 30) {
        $total = $this->getTotalServices();
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        return [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
            'start_record' => $offset + 1,
            'end_record' => min($offset + $perPage, $total)
        ];
    }

    // Obtener servicio por ID con detalles
    public function getByIdWithDetails($id) {
        $sql = "SELECT
                    s.*,
                    CONCAT(c.nombres, ' ', c.apellidos) as cliente_nombre,
                    c.telefono as cliente_telefono,
                    et.Descripcion as estado_descripcion,
                    ts.Descripcion as tipo_servicio_nombre,
                    CONCAT(t.nombres, ' ', t.apellidos) as tecnico_nombre
                FROM servicio s
                LEFT JOIN cliente c ON s.NoIdentificacionCliente = c.no_identificacion
                LEFT JOIN estadoentaller et ON s.IdEstadoEnTaller = et.IdEstadoEnTaller
                LEFT JOIN tiposervicio ts ON s.IdTipoServicio = ts.IdTipoServicio
                LEFT JOIN cliente t ON s.NoIdentificacionEmpleado = t.no_identificacion
                WHERE s.IdServicio = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Cambiar estado del servicio
    public function changeStatus($id, $newStatus) {
        $sql = "UPDATE servicio SET IdEstadoEnTaller = ? WHERE IdServicio = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newStatus, $id]);
    }

    // Obtener clientes para select
    public function getClientes() {
        $sql = "SELECT no_identificacion as NoIdentificacionCliente,
                       TRIM(
                           CONCAT(
                               UPPER(LEFT(TRIM(nombres), 1)),
                               LOWER(SUBSTRING(TRIM(nombres), 2)),
                               ' ',
                               UPPER(LEFT(TRIM(apellidos), 1)),
                               LOWER(SUBSTRING(TRIM(apellidos), 2))
                           )
                       ) as NombreCliente,
                       telefono,
                       direccion,
                       tipo_id
                FROM cliente
                ORDER BY nombres, apellidos";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener estados para select
    public function getEstados() {
        $sql = "SELECT IdEstadoEnTaller as id, Descripcion as descripcion
                FROM estadoentaller
                ORDER BY IdEstadoEnTaller";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener tipos de servicio para select
    public function getTiposServicio() {
        $sql = "SELECT IdTipoServicio as id, Descripcion as descripcion, CostoAproximado
                FROM tiposervicio
                ORDER BY Descripcion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Crear servicio (actualizado con todos los campos)
    public function create($data) {
        // Mapear los datos a los nombres reales de columnas
        $servicioData = [
            'NoIdentificacionCliente' => $data['idcliente'],
            'NoIdentificacionEmpleado' => $data['NoIdentificacionEmpleado'] ?? null,
            'IdTipoServicio' => $data['IdTipoServicio'],
            'Equipo' => $data['equipo'],
            'CondicionesEntrega' => $data['condicionesentrega'] ?? '',
            'Problema' => $data['problema'],
            'NotaInterna' => $data['notainterna'] ?? '',
            'Costo' => $data['costo'] ?? null,
            'IdEstadoEnTaller' => $data['IdEstadoEnTaller'],
            'FechaIngreso' => date('Y-m-d H:i:s')
        ];

        $fields = array_keys($servicioData);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute(array_values($servicioData))) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Actualizar servicio
    public function update($id, $data) {
        // Debug: Log de inicio de actualización en modelo
        error_log("Servicio::update() - Iniciando actualización para ID: $id");
        error_log("Servicio::update() - Datos recibidos: " . json_encode($data));

        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE IdServicio = ?";
        error_log("Servicio::update() - SQL generado: $sql");

        $stmt = $this->db->prepare($sql);

        $values = array_values($data);
        $values[] = $id;
        error_log("Servicio::update() - Valores a ejecutar: " . json_encode($values));

        try {
            $result = $stmt->execute($values);
            error_log("Servicio::update() - Resultado de execute: " . ($result ? 'true' : 'false'));

            if ($result) {
                $rowCount = $stmt->rowCount();
                error_log("Servicio::update() - Filas afectadas: $rowCount");
                return $rowCount > 0;
            } else {
                error_log("Servicio::update() - Error en execute: " . json_encode($stmt->errorInfo()));
                return false;
            }
        } catch (Exception $e) {
            error_log("Servicio::update() - Excepción: " . $e->getMessage());
            return false;
        }
    }



    // Obtener técnicos/empleados para select
    public function getTecnicos() {
        $sql = "SELECT
                    u.no_identificacion as NoIdentificacionEmpleado,
                    TRIM(
                        CONCAT(
                            UPPER(LEFT(TRIM(c.nombres), 1)),
                            LOWER(SUBSTRING(TRIM(c.nombres), 2)),
                            ' ',
                            UPPER(LEFT(TRIM(c.apellidos), 1)),
                            LOWER(SUBSTRING(TRIM(c.apellidos), 2))
                        )
                    ) as NombreEmpleado,
                    c.telefono,
                    p.descripcion as perfil_descripcion
                FROM usuario u
                INNER JOIN cliente c ON u.no_identificacion = c.no_identificacion
                INNER JOIN perfil p ON u.codigo_perfil = p.codigo_perfil
                WHERE u.activo = 1
                  AND p.activo = 1
                  AND (
                      LOWER(p.descripcion) LIKE '%tecnico%'
                      OR LOWER(p.descripcion) LIKE '%técnico%'
                      OR LOWER(p.descripcion) LIKE '%especializado%'
                      OR p.codigo_perfil IN (2, 8) -- IDs de perfiles de técnico conocidos
                  )
                ORDER BY c.nombres, c.apellidos";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener cliente por ID
    public function getClienteById($id) {
        $sql = "SELECT
                    no_identificacion as NoIdentificacionCliente,
                    CONCAT(nombres, ' ', apellidos) as NombreCliente,
                    nombres,
                    apellidos,
                    telefono,
                    direccion,
                    tipo_id
                FROM cliente
                WHERE no_identificacion = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}