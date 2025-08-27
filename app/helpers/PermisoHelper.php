<?php
/**
 * PermisoHelper - Helper para manejo de permisos en vistas
 */
class PermisoHelper {
    private static $permisoModel = null;

    private static function getModel() {
        if (self::$permisoModel === null) {
            self::$permisoModel = new Permiso();
        }
        return self::$permisoModel;
    }

    /**
     * Verificar si el usuario actual tiene permiso para una opción
     */
    public static function tienePermiso($opcion) {
        // Obtener ID del usuario de la sesión
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if (!$usuarioId) {
            return false;
        }

        return self::getModel()->tienePermiso($usuarioId, $opcion);
    }

    /**
     * Mostrar contenido solo si tiene permiso
     */
    public static function mostrarSi($opcion, $contenido) {
        if (self::tienePermiso($opcion)) {
            echo $contenido;
        }
    }

    /**
     * Ocultar contenido si no tiene permiso
     */
    public static function ocultarSiNo($opcion, $contenido) {
        if (!self::tienePermiso($opcion)) {
            echo 'style="display: none;"';
        }
    }

    /**
     * Generar menú dinámico para el usuario
     */
    public static function generarMenu($usuarioId) {
        $menu = self::getModel()->getMenuByUsuario($usuarioId);
        $menuOriginal = $menu; // Guardar copia original

        $menuEstructurado = [];
        $menusPrincipales = [];

        // Primero, identificar todos los menús principales
        foreach ($menu as $opcion) {
            if ($opcion['submenu'] == 1) {
                $menusPrincipales[$opcion['codigo']] = $opcion;
            }
        }

        // Luego, agregar las subopciones a sus menús principales
        foreach ($menuOriginal as $opcion) {
            if ($opcion['submenu'] == 0) {
                // Es un submenú
                $padre = substr($opcion['codigo'], 0, 2);
                if (isset($menusPrincipales[$padre])) {
                    // Solo crear el menú principal si tiene subopciones
                    if (!isset($menuEstructurado[$padre])) {
                        $menuEstructurado[$padre] = [
                            'descripcion' => $menusPrincipales[$padre]['descripcion'],
                            'url' => $menusPrincipales[$padre]['url'],
                            'icono' => $menusPrincipales[$padre]['icono'],
                            'submenu' => []
                        ];
                    }
                    $menuEstructurado[$padre]['submenu'][] = $opcion;
                }
            }
        }

        return $menuEstructurado;
    }

    /**
     * Generar HTML del menú
     */
    public static function generarMenuHTML($usuarioId) {
        $menu = self::generarMenu($usuarioId);
        $html = '';

        foreach ($menu as $codigo => $opcion) {
            if (!empty($opcion['submenu'])) {
                // Menú con submenús
                $html .= '<li class="sidebar__item--dropdown">';
                $html .= '<a href="#" class="sidebar__link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#' . $codigo . 'Submenu">';
                $html .= '<i class="' . ($opcion['icono'] ?: 'fas fa-folder') . ' sidebar__link-icon"></i>';
                $html .= '<span class="sidebar__link-text">' . htmlspecialchars($opcion['descripcion']) . '</span>';
                $html .= '<i class="fas fa-chevron-down sidebar__link-arrow"></i>';
                $html .= '</a>';
                $html .= '<div class="collapse" id="' . $codigo . 'Submenu">';
                $html .= '<ul class="sidebar__submenu">';

                foreach ($opcion['submenu'] as $subopcion) {
                    $url = self::getUrl($subopcion['url']);
                    $html .= '<li class="sidebar__submenu-item">';
                    $html .= '<a href="' . $url . '" class="sidebar__submenu-link">';
                    $html .= '<i class="fas fa-circle sidebar__submenu-icon"></i>';
                    $html .= '<span>' . htmlspecialchars($subopcion['descripcion']) . '</span>';
                    $html .= '</a>';
                    $html .= '</li>';
                }

                $html .= '</ul>';
                $html .= '</div>';
                $html .= '</li>';
            } else {
                // Menú simple
                $url = self::getUrl($opcion['url']);
                $html .= '<li>';
                $html .= '<a href="' . $url . '" class="sidebar__link">';
                $html .= '<i class="' . ($opcion['icono'] ?: 'fas fa-link') . ' sidebar__link-icon"></i>';
                $html .= '<span class="sidebar__link-text">' . htmlspecialchars($opcion['descripcion']) . '</span>';
                $html .= '</a>';
                $html .= '</li>';
            }
        }

        return $html;
    }

    /**
     * Verificar si una ruta está permitida
     */
    public static function rutaPermitida($ruta) {
        // Obtener usuario de sesión
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if (!$usuarioId) {
            return false;
        }

        // Buscar la opción que corresponde a esta ruta
        $menu = self::getModel()->getMenuByUsuario($usuarioId);

        foreach ($menu as $opcion) {
            if ($opcion['url'] === $ruta) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtener opciones disponibles para un perfil
     */
    public static function getOpcionesPorPerfil($perfilId) {
        return self::getModel()->getOpcionesByPerfil($perfilId);
    }



    /**
     * Obtener URL de forma segura
     */
    private static function getUrl($path) {
        if (function_exists('url')) {
            return url($path);
        } else {
            // Fallback para contextos donde url() no está disponible
            $baseUrl = 'index.php?route=';
            return $baseUrl . $path;
        }
    }
}