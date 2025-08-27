<?php
/**
 * Helper para obtener información de la empresa
 */

class EmpresaHelper {
    private static $empresaInfo = null;

    /**
     * Obtener información de la empresa (cached)
     */
    public static function getEmpresaInfo() {
        if (self::$empresaInfo === null) {
            try {
                $database = Database::getInstance();
                $pdo = $database->getConnection();

                $stmt = $pdo->query("SELECT * FROM empresa WHERE id = 1");
                self::$empresaInfo = $stmt->fetch();
            } catch (Exception $e) {
                // En caso de error, devolver información por defecto
                self::$empresaInfo = [
                    'nombreempresa' => 'SYSTEC SOLUCIONES',
                    'logo' => null
                ];
            }
        }

        return self::$empresaInfo;
    }

    /**
     * Obtener el logo de la empresa
     */
    public static function getLogo() {
        $empresa = self::getEmpresaInfo();
        return $empresa['logo'] ?? null;
    }

    /**
     * Obtener el nombre de la empresa
     */
    public static function getNombre() {
        $empresa = self::getEmpresaInfo();
        return $empresa['nombreempresa'] ?? 'SYSTEC SOLUCIONES';
    }

    /**
     * Verificar si existe un logo
     */
    public static function tieneLogo() {
        $logo = self::getLogo();
        return $logo && file_exists($logo);
    }

    /**
     * Obtener la URL del logo
     */
    public static function getLogoUrl() {
        $logo = self::getLogo();
        if ($logo && file_exists($logo)) {
            // Detectar automáticamente la URL base
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8888';
            
            // Construir la URL base correctamente
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $basePath = dirname($scriptName);
            
            // Si estamos en el directorio raíz del proyecto
            if (strpos($basePath, '/app') !== false) {
                $baseUrl = $protocol . '://' . $host . $basePath . '/';
            } else {
                // Fallback para desarrollo local
                $baseUrl = $protocol . '://' . $host . '/app/';
            }
            
            return $baseUrl . $logo;
        }
        return null;
    }
}