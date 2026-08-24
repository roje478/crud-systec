<?php
/**
 * Instalador del módulo de Técnicos Externos
 *
 * Ejecuta config/setup_tecnicos_externos.sql contra la base de datos configurada
 * en config/Database.php. Es idempotente: se puede correr varias veces.
 *
 * Uso desde terminal:
 *     php config/setup_tecnicos_externos.php
 *
 * Uso desde el navegador:
 *     http://localhost/.../config/setup_tecnicos_externos.php
 */

require_once __DIR__ . '/Database.php';

$esCli = (php_sapi_name() === 'cli');
$salto = $esCli ? "\n" : "<br>\n";

function mostrar($mensaje, $tipo = 'info') {
    global $salto;
    $iconos = ['success' => '[OK]', 'error' => '[ERROR]', 'warning' => '[AVISO]', 'info' => '[INFO]'];
    echo ($iconos[$tipo] ?? '[INFO]') . ' ' . $mensaje . $salto;
}

if (!$esCli) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<pre style="font-family: monospace; font-size: 14px; padding: 20px;">';
}

echo '=============================================' . $salto;
echo ' INSTALACIÓN - MÓDULO DE TÉCNICOS EXTERNOS' . $salto;
echo '=============================================' . $salto . $salto;

$rutaSql = __DIR__ . '/setup_tecnicos_externos.sql';

if (!file_exists($rutaSql)) {
    mostrar("No se encontró el archivo setup_tecnicos_externos.sql", 'error');
    exit(1);
}

try {
    $db = Database::getInstance()->getConnection();
    mostrar('Conexión a la base de datos establecida.', 'success');

    $sql = file_get_contents($rutaSql);

    // Separar por sentencias: el script no contiene procedimientos ni delimitadores
    $sentencias = array_filter(
        array_map('trim', explode(';', $sql)),
        function ($sentencia) {
            if ($sentencia === '') {
                return false;
            }
            // Descartar bloques que solo son comentarios
            $lineas = array_filter(
                array_map('trim', explode("\n", $sentencia)),
                function ($linea) {
                    return $linea !== '' && strpos($linea, '--') !== 0;
                }
            );
            return !empty($lineas);
        }
    );

    $ejecutadas = 0;
    $fallidas = 0;

    foreach ($sentencias as $sentencia) {
        try {
            $stmt = $db->query($sentencia);

            // Mostrar el resultado de las consultas de verificación
            if (stripos(ltrim($sentencia), 'SELECT') === 0) {
                $filas = $stmt->fetchAll();
                if (!empty($filas)) {
                    echo $salto . '--- Verificación ---' . $salto;
                    foreach ($filas as $fila) {
                        echo '  ' . implode(' | ', array_map('strval', $fila)) . $salto;
                    }
                    echo $salto;
                }
            }

            $ejecutadas++;
        } catch (PDOException $e) {
            $fallidas++;
            mostrar('Sentencia con error: ' . $e->getMessage(), 'error');
            echo '  > ' . substr(preg_replace('/\s+/', ' ', $sentencia), 0, 120) . '...' . $salto;
        }
    }

    echo $salto;
    mostrar("Sentencias ejecutadas: {$ejecutadas}", 'success');

    if ($fallidas > 0) {
        mostrar("Sentencias con error: {$fallidas}", 'warning');
    }

    // Resumen final
    echo $salto . 'Tablas del módulo:' . $salto;
    foreach (['motivo_orden_externa', 'tecnico_externo', 'orden_tecnico_externo'] as $tabla) {
        $stmt = $db->query("SHOW TABLES LIKE '{$tabla}'");
        $existe = $stmt->fetch();
        mostrar("  {$tabla}: " . ($existe ? 'creada' : 'NO existe'), $existe ? 'success' : 'error');
    }

    echo $salto . 'Siguiente paso:' . $salto;
    echo '  Ve a Permisos > Asignar y marca las opciones "Técnicos Externos"' . $salto;
    echo '  para los perfiles que deban ver el módulo.' . $salto;

} catch (Exception $e) {
    mostrar('Error general: ' . $e->getMessage(), 'error');
    if (!$esCli) { echo '</pre>'; }
    exit(1);
}

if (!$esCli) {
    echo '</pre>';
}
