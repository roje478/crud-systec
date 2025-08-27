<?php
/**
 * Modelo Usuario - Trabaja con las tablas cliente y usuario relacionadas
 * Los usuarios son clientes con acceso al sistema
 */
class Usuario extends BaseModel {
    protected $table = 'usuario';
    protected $primaryKey = 'no_identificacion';

    public function __construct() {
        parent::__construct();
    }

    // Obtener usuarios con información de cliente y perfil
    public function getAllWithDetails() {
        $sql = "SELECT
                    u.*,
                    c.nombres, c.apellidos, c.telefono, c.direccion,
                    p.descripcion as perfil_descripcion,
                    p.activo as perfil_activo
                FROM usuario u
                LEFT JOIN cliente c ON u.no_identificacion = c.no_identificacion
                LEFT JOIN perfil p ON u.codigo_perfil = p.codigo_perfil
                ORDER BY c.nombres, c.apellidos";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener usuarios con paginación
    public function getAllWithDetailsPaginated($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT
                    u.*,
                    c.nombres, c.apellidos, c.telefono, c.direccion,
                    p.descripcion as perfil_descripcion,
                    p.activo as perfil_activo
                FROM usuario u
                LEFT JOIN cliente c ON u.no_identificacion = c.no_identificacion
                LEFT JOIN perfil p ON u.codigo_perfil = p.codigo_perfil
                ORDER BY c.nombres, c.apellidos
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    // Obtener usuario por ID con detalles
    public function getByIdWithDetails($id) {
        $sql = "SELECT
                    u.*,
                    c.nombres, c.apellidos, c.telefono, c.direccion,
                    p.descripcion as perfil_descripcion,
                    p.activo as perfil_activo
                FROM usuario u
                LEFT JOIN cliente c ON u.no_identificacion = c.no_identificacion
                LEFT JOIN perfil p ON u.codigo_perfil = p.codigo_perfil
                WHERE u.no_identificacion = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Verificar si existe un usuario
    public function exists($noIdentificacion) {
        try {
            $sql = "SELECT COUNT(*) as count FROM usuario WHERE no_identificacion = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$noIdentificacion]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Usuario::exists() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Crear usuario (cliente + usuario)
    public function create($data) {
        try {
            // Verificar si la identificación ya existe en usuario
            if ($this->exists($data['no_identificacion'])) {
                return false;
            }

            // Verificar si el cliente ya existe
            $clienteModel = new Cliente();
            $clienteExiste = $clienteModel->getById($data['no_identificacion']);

            if (!$clienteExiste) {
                // Crear cliente primero
                $clienteData = [
                    'no_identificacion' => $data['no_identificacion'],
                    'tipo_id' => $data['tipo_id'] ?? 'CC',
                    'nombres' => $data['nombres'],
                    'apellidos' => $data['apellidos'],
                    'telefono' => $data['telefono'] ?? '',
                    'direccion' => $data['direccion'] ?? '',
                    'fecha_registro' => date('Y-m-d')
                ];

                $clienteCreado = $clienteModel->create($clienteData);
                if (!$clienteCreado) {
                    error_log("Usuario::create() - Error al crear cliente");
                    return false;
                }
            }

            // Encriptar contraseña
            $data['contrasenia'] = md5($data['contrasenia']);

            // Crear usuario (sin especificar activo para usar default)
            $sql = "INSERT INTO usuario (no_identificacion, codigo_perfil, contrasenia)
                    VALUES (?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['no_identificacion'],
                $data['codigo_perfil'],
                $data['contrasenia']
            ]);

            return $result ? $data['no_identificacion'] : false;
        } catch (Exception $e) {
            error_log("Usuario::create() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar usuario
    public function update($id, $data) {
        try {
            // Actualizar datos del usuario
            $updates = [];
            $values = [];

            if (isset($data['codigo_perfil'])) {
                $updates[] = 'codigo_perfil = ?';
                $values[] = $data['codigo_perfil'];
            }

            if (isset($data['contrasenia']) && !empty($data['contrasenia'])) {
                $updates[] = 'contrasenia = ?';
                $values[] = md5($data['contrasenia']);
            }

            if (isset($data['activo'])) {
                $updates[] = 'activo = ?';
                $values[] = $data['activo'] ? 1 : 0;
            }

            // Solo actualizar usuario si hay cambios
            if (!empty($updates)) {
                $values[] = $id;

                $sql = "UPDATE usuario SET " . implode(', ', $updates) . " WHERE no_identificacion = ?";
                $stmt = $this->db->prepare($sql);

                $result = $stmt->execute($values);
                if (!$result) {
                    error_log("Usuario::update() - Error al actualizar usuario");
                    return false;
                }
            }

            // Actualizar datos del cliente solo si se proporcionan y son diferentes
            if (isset($data['nombres']) || isset($data['apellidos']) || isset($data['direccion']) || isset($data['telefono'])) {
                $clienteModel = new Cliente();

                // Obtener datos actuales del cliente
                $clienteActual = $clienteModel->getById($id);

                if ($clienteActual) {
                    $clienteData = [];
                    $hayCambios = false;

                    // Verificar si hay cambios reales
                    if (isset($data['nombres']) && $data['nombres'] !== $clienteActual['nombres']) {
                        $clienteData['nombres'] = $data['nombres'];
                        $hayCambios = true;
                    }

                    if (isset($data['apellidos']) && $data['apellidos'] !== $clienteActual['apellidos']) {
                        $clienteData['apellidos'] = $data['apellidos'];
                        $hayCambios = true;
                    }

                    if (isset($data['direccion']) && $data['direccion'] !== $clienteActual['direccion']) {
                        $clienteData['direccion'] = $data['direccion'];
                        $hayCambios = true;
                    }

                    if (isset($data['telefono']) && $data['telefono'] !== $clienteActual['telefono']) {
                        $clienteData['telefono'] = $data['telefono'];
                        $hayCambios = true;
                    }

                    // Solo actualizar si hay cambios reales
                    if ($hayCambios && !empty($clienteData)) {
                        $clienteActualizado = $clienteModel->update($id, $clienteData);
                        if (!$clienteActualizado) {
                            error_log("Usuario::update() - Error al actualizar cliente");
                            return false;
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Usuario::update() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Cambiar estado del usuario
    public function changeStatus($id) {
        try {
            $sql = "UPDATE usuario SET activo = CASE WHEN activo = 1 THEN 0 ELSE 1 END WHERE no_identificacion = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Usuario::changeStatus() - Error: " . $e->getMessage());
            return false;
        }
    }



    // Obtener estadísticas
    public function getEstadisticas() {
        try {
            $sql = "SELECT
                        COUNT(*) as total,
                        SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
                        SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) as inactivos
                    FROM usuario";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Usuario::getEstadisticas() - Error: " . $e->getMessage());
            return ['total' => 0, 'activos' => 0, 'inactivos' => 0];
        }
    }

    // Contar total de usuarios
    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as total FROM usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtener información de paginación
    public function getPaginationInfo($page = 1, $perPage = 20) {
        $total = $this->getTotalUsers();
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

    // Obtener perfiles disponibles
    public function getPerfiles() {
        $sql = "SELECT codigo_perfil as id, descripcion, activo FROM perfil ORDER BY descripcion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener perfil por ID
    public function getPerfilById($perfilId) {
        $sql = "SELECT codigo_perfil as id, descripcion, activo, idempresa FROM perfil WHERE codigo_perfil = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perfilId]);
        return $stmt->fetch();
    }

    // Crear nuevo perfil
    public function createPerfil($data) {
        try {
            // Validar datos requeridos
            if (empty($data['descripcion'])) {
                throw new Exception('La descripción del perfil es requerida');
            }

            // Verificar si ya existe un perfil con la misma descripción
            $sql = "SELECT COUNT(*) as count FROM perfil WHERE descripcion = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['descripcion']]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                throw new Exception('Ya existe un perfil con esa descripción');
            }

            // Obtener el siguiente ID disponible
            $sql = "SELECT MAX(codigo_perfil) as max_id FROM perfil";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            $nextId = ($result['max_id'] ?? 0) + 1;

            // Insertar nuevo perfil
            $sql = "INSERT INTO perfil (codigo_perfil, descripcion, activo, idempresa) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $nextId, PDO::PARAM_INT);
            $stmt->bindValue(2, $data['descripcion'], PDO::PARAM_STR);
            $stmt->bindValue(3, ($data['activo'] ?? 1) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(4, $data['idempresa'] ?? null, PDO::PARAM_INT);
            $stmt->execute();

            return $nextId;
        } catch (Exception $e) {
            error_log("Usuario::createPerfil() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    // Verificar si existe un perfil
    public function perfilExists($descripcion) {
        try {
            $sql = "SELECT COUNT(*) as count FROM perfil WHERE descripcion = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$descripcion]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Usuario::perfilExists() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Obtener todos los perfiles con estadísticas
    public function getPerfilesWithStats() {
        $sql = "SELECT
                    p.codigo_perfil,
                    p.descripcion,
                    p.activo,
                    COUNT(u.no_identificacion) as total_usuarios,
                    COUNT(po.codigo_opcion) as total_permisos
                FROM perfil p
                LEFT JOIN usuario u ON p.codigo_perfil = u.codigo_perfil
                LEFT JOIN perfil_opciones po ON p.codigo_perfil = po.codigo_perfil
                GROUP BY p.codigo_perfil, p.descripcion, p.activo
                ORDER BY p.codigo_perfil";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Actualizar perfil existente
    public function updatePerfil($perfilId, $data) {
        try {
            // Validar datos requeridos
            if (empty($data['descripcion'])) {
                throw new Exception('La descripción del perfil es requerida');
            }

            // Verificar si ya existe otro perfil con la misma descripción (excluyendo el actual)
            $sql = "SELECT COUNT(*) as count FROM perfil WHERE descripcion = ? AND codigo_perfil != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['descripcion'], $perfilId]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                throw new Exception('Ya existe otro perfil con esa descripción');
            }

            // Actualizar perfil
            $sql = "UPDATE perfil SET descripcion = ?, activo = ? WHERE codigo_perfil = ?";
            $stmt = $this->db->prepare($sql);
            $activo = ($data['activo'] ?? 1) ? 1 : 0;
            $stmt->bindValue(1, $data['descripcion'], PDO::PARAM_STR);
            $stmt->bindValue(2, $activo, PDO::PARAM_INT);
            $stmt->bindValue(3, $perfilId, PDO::PARAM_INT);

            $result = $stmt->execute();

            if (!$result) {
                throw new Exception('Error al actualizar el perfil');
            }

            return true;
        } catch (Exception $e) {
            error_log("Usuario::updatePerfil() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    // Eliminar perfil (solo si no tiene usuarios asignados)
    public function deletePerfil($perfilId) {
        try {
            // Verificar si el perfil tiene usuarios asignados
            $sql = "SELECT COUNT(*) as count FROM usuario WHERE codigo_perfil = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$perfilId]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                throw new Exception('No se puede eliminar el perfil porque tiene usuarios asignados');
            }

            // Eliminar permisos del perfil
            $sql = "DELETE FROM perfil_opciones WHERE codigo_perfil = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$perfilId]);

            // Eliminar el perfil
            $sql = "DELETE FROM perfil WHERE codigo_perfil = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$perfilId]);

            if (!$result) {
                throw new Exception('Error al eliminar el perfil');
            }

            return true;
        } catch (Exception $e) {
            error_log("Usuario::deletePerfil() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    // Verificar si un perfil puede ser eliminado
    public function canDeletePerfil($perfilId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM usuario WHERE codigo_perfil = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$perfilId]);
            $result = $stmt->fetch();
            return $result['count'] == 0;
        } catch (Exception $e) {
            error_log("Usuario::canDeletePerfil() - Error: " . $e->getMessage());
            return false;
        }
    }
}