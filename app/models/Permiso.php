<?php
/**
 * Modelo Permiso - Gestión de permisos y opciones del sistema
 */
class Permiso extends BaseModel {
    protected $table = 'perfil_opciones';

    // Obtener opciones por perfil
    public function getOpcionesByPerfil($codigoPerfil) {
        $sql = "SELECT o.* FROM opciones o
                INNER JOIN perfil_opciones po ON o.codigo = po.codigo_opcion
                WHERE po.codigo_perfil = ? AND o.activo = 1
                ORDER BY o.submenu, o.descripcion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$codigoPerfil]);
        return $stmt->fetchAll();
    }

    // Verificar si un usuario tiene permiso para una opción
    public function tienePermiso($usuarioId, $opcion) {
        $sql = "SELECT COUNT(*) as count FROM perfil_opciones po
                INNER JOIN usuario u ON po.codigo_perfil = u.codigo_perfil
                INNER JOIN opciones o ON po.codigo_opcion = o.codigo
                WHERE u.no_identificacion = ? AND o.codigo = ?
                AND u.activo = 1 AND o.activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId, $opcion]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    // Obtener menú dinámico por usuario
    public function getMenuByUsuario($usuarioId) {
        $sql = "SELECT DISTINCT o.* FROM opciones o
                INNER JOIN perfil_opciones po ON o.codigo = po.codigo_opcion
                INNER JOIN usuario u ON po.codigo_perfil = u.codigo_perfil
                WHERE u.no_identificacion = ? AND u.activo = 1 AND o.activo = 1
                ORDER BY o.submenu, o.descripcion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    // Asignar opciones a un perfil (versión mejorada con transacciones)
    public function asignarOpciones($perfilId, $opciones) {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // Si es el administrador (perfil 1), proteger menús principales
            if ($perfilId == 1) {
                $menusPrincipales = ['02', '03', '04', '10', 'CF']; // Menús principales que existen en BD

                // Asegurar que los menús principales estén en las opciones
                foreach ($menusPrincipales as $menu) {
                    if (!in_array($menu, $opciones)) {
                        $opciones[] = $menu;
                    }
                }
            }

            // Eliminar asignaciones existentes solo para este perfil
            $sql = "DELETE FROM perfil_opciones WHERE codigo_perfil = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$perfilId]);

            // Insertar nuevas asignaciones
            if (!empty($opciones)) {
                $sql = "INSERT INTO perfil_opciones (codigo_perfil, codigo_opcion) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);

                foreach ($opciones as $opcion) {
                    $stmt->execute([$perfilId, $opcion]);
                }
            }

            // Confirmar transacción
            $this->db->commit();

            return true;
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->rollBack();
            error_log("Permiso::asignarOpciones() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Método alternativo: Actualizar permisos sin eliminar todos (más seguro)
    public function actualizarOpciones($perfilId, $opciones) {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // Si es el administrador (perfil 1), proteger menús principales
            if ($perfilId == 1) {
                $menusPrincipales = ['02', '03', '04', '10', 'CF']; // Menús principales que existen en BD
                foreach ($menusPrincipales as $menu) {
                    if (!in_array($menu, $opciones)) {
                        $opciones[] = $menu;
                    }
                }
            }

            // Obtener permisos actuales del perfil
            $sql = "SELECT codigo_opcion FROM perfil_opciones WHERE codigo_perfil = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$perfilId]);
            $permisosActuales = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Determinar qué agregar y qué eliminar
            $permisosAAgregar = array_diff($opciones, $permisosActuales);
            $permisosAEliminar = array_diff($permisosActuales, $opciones);

            // Eliminar permisos que ya no están seleccionados
            if (!empty($permisosAEliminar)) {
                $placeholders = str_repeat('?,', count($permisosAEliminar) - 1) . '?';
                $sql = "DELETE FROM perfil_opciones WHERE codigo_perfil = ? AND codigo_opcion IN ($placeholders)";
                $params = array_merge([$perfilId], $permisosAEliminar);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }

            // Agregar nuevos permisos
            if (!empty($permisosAAgregar)) {
                $sql = "INSERT INTO perfil_opciones (codigo_perfil, codigo_opcion) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);

                foreach ($permisosAAgregar as $opcion) {
                    $stmt->execute([$perfilId, $opcion]);
                }
            }

            // Confirmar transacción
            $this->db->commit();

            return true;
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->rollBack();
            error_log("Permiso::actualizarOpciones() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Obtener todas las opciones disponibles
    public function getAllOpciones() {
        $sql = "SELECT * FROM opciones WHERE activo = 1 ORDER BY submenu, descripcion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener opciones por submenu
    public function getOpcionesBySubmenu($submenu) {
        $sql = "SELECT * FROM opciones WHERE submenu = ? AND activo = 1 ORDER BY descripcion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$submenu]);
        return $stmt->fetchAll();
    }


}