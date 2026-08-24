<?php
/**
 * Diagnóstico 2: imprime el código REAL de los métodos de permisos del servidor,
 * para compararlo con el de desarrollo.
 *
 * BORRA ESTE ARCHIVO cuando termines.
 */

session_start();

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../app/models/BaseModel.php';
require_once __DIR__ . '/../app/models/Permiso.php';
require_once __DIR__ . '/../app/helpers/PermisoHelper.php';

header('Content-Type: text/plain; charset=utf-8');

// Exige sesión iniciada: este script imprime código fuente y no debe quedar
// accesible sin autenticación.
if (empty($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo "Debes iniciar sesión en el sistema para usar este diagnóstico.\n";
    exit;
}

function mostrarMetodo($clase, $metodo) {
    echo "-----------------------------------------------------------\n";
    echo " {$clase}::{$metodo}()\n";
    echo "-----------------------------------------------------------\n";

    if (!class_exists($clase) || !method_exists($clase, $metodo)) {
        echo " >>> NO EXISTE en este servidor\n\n";
        return;
    }

    $ref = new ReflectionMethod($clase, $metodo);
    $archivo = $ref->getFileName();
    $ini = $ref->getStartLine();
    $fin = $ref->getEndLine();

    echo " archivo: {$archivo}\n";
    echo " líneas : {$ini}-{$fin}\n\n";

    $lineas = file($archivo);
    for ($i = $ini - 1; $i < $fin; $i++) {
        echo $lineas[$i];
    }
    echo "\n";
}

echo "===========================================================\n";
echo " CÓDIGO DE PERMISOS EN ESTE SERVIDOR\n";
echo "===========================================================\n\n";

echo "PHP: " . PHP_VERSION . "\n";
echo "usuario_id en sesión: " . var_export($_SESSION['usuario_id'] ?? null, true) . "\n\n";

mostrarMetodo('PermisoHelper', 'tienePermiso');
mostrarMetodo('Permiso', 'tienePermiso');

echo "-----------------------------------------------------------\n";
echo " FECHAS DE LOS ARCHIVOS\n";
echo "-----------------------------------------------------------\n";
foreach ([
    __DIR__ . '/../app/helpers/PermisoHelper.php',
    __DIR__ . '/../app/models/Permiso.php',
] as $f) {
    if (file_exists($f)) {
        echo " " . basename($f) . " -> " . date('Y-m-d H:i:s', filemtime($f))
           . "  (" . filesize($f) . " bytes, md5 " . substr(md5_file($f), 0, 12) . ")\n";
    }
}

echo "\n===========================================================\n";
echo " Borra este archivo cuando termines.\n";
echo "===========================================================\n";
