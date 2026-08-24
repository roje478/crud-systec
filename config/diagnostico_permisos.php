<?php
/**
 * Diagnóstico de permisos del módulo de Técnicos Externos.
 *
 * Uso: entra al sistema normalmente y luego abre este archivo en el navegador:
 *      https://TU-DOMINIO/app/config/diagnostico_permisos.php
 *
 * BORRA ESTE ARCHIVO cuando termines: expone información de permisos.
 */

session_start();

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../app/models/BaseModel.php';
require_once __DIR__ . '/../app/models/Permiso.php';
require_once __DIR__ . '/../app/helpers/PermisoHelper.php';

header('Content-Type: text/html; charset=utf-8');
echo '<pre style="font-family: monospace; font-size: 14px; padding: 20px; line-height: 1.6;">';

echo "=========================================\n";
echo " DIAGNÓSTICO DE PERMISOS\n";
echo "=========================================\n\n";

// ---------------------------------------------------------------
// 1. Qué hay en la sesión
// ---------------------------------------------------------------
$usuarioId = $_SESSION['usuario_id'] ?? null;

echo "[1] SESIÓN\n";
echo "    usuario_id .......... " . var_export($usuarioId, true) . "\n";
echo "    tipo ................ " . gettype($usuarioId) . "\n";
echo "    usuario_perfil ...... " . var_export($_SESSION['usuario_perfil'] ?? null, true) . "\n";
echo "    usuario_perfil_nombre " . var_export($_SESSION['usuario_perfil_nombre'] ?? null, true) . "\n";
echo "    nombre completo ..... " . var_export($_SESSION['usuario_nombre_completo'] ?? null, true) . "\n\n";

if (!$usuarioId) {
    echo "    >>> NO HAY SESIÓN. Entra al sistema primero y recarga esta página.\n";
    echo '</pre>';
    exit;
}

$db = Database::getInstance()->getConnection();

// ---------------------------------------------------------------
// 2. Filas de este usuario en `usuario`
// ---------------------------------------------------------------
echo "[2] PERFILES DE ESTE USUARIO\n";
$stmt = $db->prepare("SELECT u.no_identificacion, u.codigo_perfil, p.descripcion, u.activo+0 AS activo
                      FROM usuario u
                      LEFT JOIN perfil p ON p.codigo_perfil = u.codigo_perfil
                      WHERE u.no_identificacion = ?");
$stmt->execute([$usuarioId]);
$filas = $stmt->fetchAll();

if (empty($filas)) {
    echo "    >>> El usuario de la sesión NO existe en la tabla `usuario`.\n\n";
} else {
    foreach ($filas as $f) {
        echo "    perfil {$f['codigo_perfil']} ({$f['descripcion']}) - activo: {$f['activo']}\n";
    }
    echo "\n";
}

// ---------------------------------------------------------------
// 3. Resultado real de la guarda, opción por opción
// ---------------------------------------------------------------
echo "[3] PermisoHelper::tienePermiso()  <-- esto es lo que evalúa la guarda\n";
foreach (['TE', 'TE01', 'TE02', 'TE03', 'CF05'] as $codigo) {
    $tiene = PermisoHelper::tienePermiso($codigo);
    echo "    $codigo " . str_repeat('.', 8 - strlen($codigo)) . " " . ($tiene ? 'SÍ' : 'NO') . "\n";
}
echo "\n";

// ---------------------------------------------------------------
// 4. La misma consulta, en crudo
// ---------------------------------------------------------------
echo "[4] CONSULTA EN CRUDO\n";
$stmt = $db->prepare("SELECT COUNT(*) AS c FROM perfil_opciones po
                      INNER JOIN usuario u ON po.codigo_perfil = u.codigo_perfil
                      INNER JOIN opciones o ON po.codigo_opcion = o.codigo
                      WHERE u.no_identificacion = ? AND o.codigo = ?
                        AND u.activo = 1 AND o.activo = 1");
$stmt->execute([$usuarioId, 'TE03']);
$r = $stmt->fetch();
echo "    TE03 para el usuario de la sesión: " . (int)$r['c'] . "\n\n";

// ---------------------------------------------------------------
// 5. Opciones que sí tiene asignadas
// ---------------------------------------------------------------
echo "[5] OPCIONES ASIGNADAS A ESTE USUARIO\n";
$stmt = $db->prepare("SELECT DISTINCT o.codigo, o.descripcion
                      FROM opciones o
                      INNER JOIN perfil_opciones po ON o.codigo = po.codigo_opcion
                      INNER JOIN usuario u ON po.codigo_perfil = u.codigo_perfil
                      WHERE u.no_identificacion = ? AND u.activo = 1 AND o.activo = 1
                      ORDER BY o.codigo");
$stmt->execute([$usuarioId]);
foreach ($stmt->fetchAll() as $o) {
    echo "    {$o['codigo']}  {$o['descripcion']}\n";
}

echo "\n=========================================\n";
echo " Borra este archivo cuando termines.\n";
echo "=========================================\n";
echo '</pre>';
