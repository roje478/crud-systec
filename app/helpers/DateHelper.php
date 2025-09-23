<?php
/**
 * DateHelper - Helper para procesar fechas con formato personalizado
 */
class DateHelper {

    /**
     * Convierte fechas con formato personalizado a formato legible
     * Maneja formatos como: "2025-07-23 Hora: 10:07:55", "2023-08-01<br> Hora: 15:08:02"
     */
    public static function formatCustomDate($fechaString, $outputFormat = 'd/m/Y') {
        if (empty($fechaString) || $fechaString === 'NULL') {
            return '-';
        }

        // Limpiar HTML tags como <br>, </br>, etc.
        $fechaLimpia = strip_tags($fechaString);
        $fechaLimpia = trim($fechaLimpia);
        
        // Si contiene "Hora:", extraer solo la parte de la fecha
        if (strpos($fechaLimpia, 'Hora:') !== false) {
            $partes = explode('Hora:', $fechaLimpia);
            $fechaLimpia = trim($partes[0]);
        }

        // Patrón para extraer fecha en formato YYYY-MM-DD
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $fechaLimpia, $matches)) {
            $fechaExtraida = $matches[1];

            // Validar que la fecha sea válida
            $timestamp = strtotime($fechaExtraida);
            if ($timestamp !== false) {
                return date($outputFormat, $timestamp);
            }
        }

        // Si no se puede procesar, intentar con strtotime directamente
        $timestamp = strtotime($fechaLimpia);
        if ($timestamp !== false) {
            return date($outputFormat, $timestamp);
        }

        // Como último recurso, devolver la fecha limpia truncada
        return substr($fechaLimpia, 0, 30) . '...';
    }

    /**
     * Extrae solo la fecha (sin hora) de strings con formato personalizado
     */
    public static function extractDate($fechaString) {
        return self::formatCustomDate($fechaString, 'd/m/Y');
    }

    /**
     * Extrae fecha y hora de strings con formato personalizado
     */
    public static function extractDateTime($fechaString) {
        if (empty($fechaString) || $fechaString === 'NULL') {
            return '-';
        }

        // Limpiar HTML tags
        $fechaLimpia = strip_tags($fechaString);
        $fechaLimpia = trim($fechaLimpia);

        // Patrón para extraer fecha y hora YYYY-MM-DD y HH:MM:SS
        if (preg_match('/(\d{4}-\d{2}-\d{2}).*?(\d{2}:\d{2}:\d{2})/', $fechaLimpia, $matches)) {
            $fecha = $matches[1];
            $hora = $matches[2];

            $timestamp = strtotime("$fecha $hora");
            if ($timestamp !== false) {
                return date('d/m/Y H:i', $timestamp);
            }
        }

        // Si no encuentra hora específica, intentar extraer de "Hora:"
        if (strpos($fechaLimpia, 'Hora:') !== false) {
            $partes = explode('Hora:', $fechaLimpia);
            if (count($partes) > 1) {
                $fecha = trim($partes[0]);
                $hora = trim($partes[1]);
                
                // Buscar patrón de hora en la segunda parte
                if (preg_match('/(\d{2}:\d{2}:\d{2})/', $hora, $horaMatches)) {
                    $horaExtraida = $horaMatches[1];
                    $timestamp = strtotime("$fecha $horaExtraida");
                    if ($timestamp !== false) {
                        return date('d/m/Y H:i', $timestamp);
                    }
                }
            }
        }

        // Fallback al método simple
        return self::formatCustomDate($fechaString, 'd/m/Y H:i');
    }
}