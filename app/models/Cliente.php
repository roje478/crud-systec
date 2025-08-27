<?php
/**
 * Modelo Cliente - Gestión completa de clientes
 */
class Cliente extends BaseModel {
    protected $table = 'cliente';
    protected $primaryKey = 'no_identificacion';

    // Obtener todos los clientes ordenados alfabéticamente
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombres, apellidos";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener todos los clientes con paginación
    public function getAllPaginated($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;

        // Log para debug
        error_log("Cliente::getAllPaginated() - Página: " . $page . ", Por página: " . $perPage . ", Offset: " . $offset);

        $sql = "SELECT * FROM {$this->table} ORDER BY nombres, apellidos LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perPage, $offset]);
        $result = $stmt->fetchAll();

        error_log("Cliente::getAllPaginated() - SQL ejecutado: " . $sql . " con LIMIT=" . $perPage . ", OFFSET=" . $offset);
        error_log("Cliente::getAllPaginated() - Resultados obtenidos: " . count($result) . " registros");

        return $result;
    }

    // Contar total de clientes
    public function getTotalClientes() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtener información de paginación
    public function getPaginationInfo($page = 1, $perPage = 20) {
        $total = $this->getTotalClientes();
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

    // Obtener cliente por ID
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Crear cliente
    public function create($data) {
        try {
            // Preparar datos con valores por defecto
            $clienteData = [
                'no_identificacion' => $data['no_identificacion'],
                'tipo_id' => $data['tipo_id'],
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'genero' => $data['genero'] ?? 'O',
                'telefono' => $data['telefono'] ?? '',
                'direccion' => $data['direccion'] ?? '',
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
                'fecha_registro' => date('Y-m-d')
            ];

            // Log para debug
            error_log("Cliente::create() - Datos preparados: " . json_encode($clienteData));

            $fields = array_keys($clienteData);
            $placeholders = array_fill(0, count($fields), '?');

            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";

            // Log para debug
            error_log("Cliente::create() - SQL: " . $sql);
            error_log("Cliente::create() - Valores: " . json_encode(array_values($clienteData)));

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute(array_values($clienteData))) {
                $id = $this->db->lastInsertId();
                error_log("Cliente::create() - lastInsertId(): " . $id);

                // Si lastInsertId() retorna 0, buscar el registro por no_identificacion
                if ($id == 0) {
                    error_log("Cliente::create() - lastInsertId() retornó 0, buscando por no_identificacion");

                    // Buscar el registro recién insertado por no_identificacion
                    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE no_identificacion = ?");
                    $stmt->execute([$data['no_identificacion']]);
                    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($cliente) {
                        // Usar el ID del registro encontrado o el no_identificacion como identificador
                        $id = $cliente[$this->primaryKey] ?? $data['no_identificacion'];
                        error_log("Cliente::create() - Cliente encontrado, usando ID: " . $id);
                        return $id;
                    } else {
                        error_log("Cliente::create() - No se pudo encontrar el cliente insertado");
                        return false;
                    }
                } else {
                    error_log("Cliente::create() - Cliente creado exitosamente con ID: " . $id);
                    return $id;
                }
            } else {
                // Obtener información del error
                $errorInfo = $stmt->errorInfo();
                error_log("Cliente::create() - Error en execute(): " . json_encode($errorInfo));
                return false;
            }

        } catch (Exception $e) {
            error_log("Cliente::create() - Excepción: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar cliente
    public function update($id, $data) {
        try {
            // Log para debug
            error_log("Cliente::update() - ID: " . $id . ", Datos: " . json_encode($data));

            $fields = array_keys($data);
            $setClause = implode(' = ?, ', $fields) . ' = ?';

            $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";

            // Log para debug
            error_log("Cliente::update() - SQL: " . $sql);

            $stmt = $this->db->prepare($sql);

            $values = array_values($data);
            $values[] = $id;

            // Log para debug
            error_log("Cliente::update() - Valores: " . json_encode($values));

            if ($stmt->execute($values)) {
                $rowCount = $stmt->rowCount();
                error_log("Cliente::update() - Filas afectadas: " . $rowCount);

                // Verificar si el cliente existe antes de considerar el update como fallido
                $checkStmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE {$this->primaryKey} = ?");
                $checkStmt->execute([$id]);
                $exists = $checkStmt->fetch()['count'] > 0;

                if ($rowCount > 0) {
                    error_log("Cliente::update() - Cliente actualizado exitosamente");
                    return true;
                } else if ($exists) {
                    // El cliente existe pero no hubo cambios (rowCount = 0)
                    error_log("Cliente::update() - Cliente existe pero no hubo cambios en los datos");
                    return true; // Considerar como éxito aunque no haya cambios
                } else {
                    error_log("Cliente::update() - No se encontró el cliente para actualizar");
                    return false;
                }
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Cliente::update() - Error en execute(): " . json_encode($errorInfo));
                return false;
            }

        } catch (Exception $e) {
            error_log("Cliente::update() - Excepción: " . $e->getMessage());
            return false;
        }
    }



    // Obtener tipos de identificación
    public function getTiposIdentificacion() {
        return [
            ['id' => 'CC', 'descripcion' => 'Cédula de Ciudadanía'],
            ['id' => 'CE', 'descripcion' => 'Cédula de Extranjería'],
            ['id' => 'TI', 'descripcion' => 'Tarjeta de Identidad'],
            ['id' => 'NIT', 'descripcion' => 'NIT (Empresa)'],
            ['id' => 'CD', 'descripcion' => 'Otro tipo de documento']
        ];
    }

    // Obtener géneros
    public function getGeneros() {
        return [
            ['id' => 'M', 'descripcion' => 'Masculino'],
            ['id' => 'F', 'descripcion' => 'Femenino'],
            ['id' => 'O', 'descripcion' => 'Otro']
        ];
    }

    // Buscar clientes
    public function search($query, $page = 1, $perPage = 30) {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table}
                WHERE nombres LIKE ? OR apellidos LIKE ? OR telefono LIKE ? OR no_identificacion LIKE ?
                ORDER BY nombres, apellidos
                LIMIT ? OFFSET ?";

        $searchTerm = "%{$query}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $perPage, $offset]);

        return $stmt->fetchAll();
    }

    // Contar resultados de búsqueda
    public function getTotalSearchResults($query) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}
                WHERE nombres LIKE ? OR apellidos LIKE ? OR telefono LIKE ? OR no_identificacion LIKE ?";

        $searchTerm = "%{$query}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Verificar si existe un cliente con el mismo número de identificación
    public function existsByIdentificacion($identificacion, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE no_identificacion = ?";
        $params = [$identificacion];

        if ($excludeId) {
            $sql .= " AND no_identificacion != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    // Obtener estadísticas de clientes
    public function getEstadisticas() {
        $sql = "SELECT
                    COUNT(*) as total_clientes,
                    COUNT(CASE WHEN genero = 'M' THEN 1 END) as masculinos,
                    COUNT(CASE WHEN genero = 'F' THEN 1 END) as femeninos,
                    COUNT(CASE WHEN genero = 'O' THEN 1 END) as otros,
                    COUNT(CASE WHEN fecha_registro >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as nuevos_mes
                FROM {$this->table}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Obtener servicios asociados a un cliente
    public function getServiciosAsociados($clienteId) {
        $sql = "SELECT COUNT(*) as total FROM servicio WHERE NoIdentificacionCliente = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clienteId]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }
}