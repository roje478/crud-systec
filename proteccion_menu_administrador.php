<?php
/**
 * Script de protección automática para el menú del administrador
 * Ejecutar después de cualquier cambio en permisos
 */

require_once 'config/database.php';
require_once 'app/models/BaseModel.php';
require_once 'app/models/Permiso.php';

echo "=== PROTECCIÓN AUTOMÁTICA DEL MENÚ DEL ADMINISTRADOR ===\n\n";

try {
    $database = Database::getInstance();
    $db = $database->getConnection();

    // Verificar permisos del administrador
    $sql = "SELECT po.codigo_opcion, o.descripcion, o.submenu
             FROM perfil_opciones po
             INNER JOIN opciones o ON po.codigo_opcion = o.codigo
             WHERE po.codigo_perfil = 1 AND o.activo = 1 AND o.submenu = 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $menusPrincipales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Menús principales actuales del administrador: " . count($menusPrincipales) . "\n";

    // Si faltan menús principales, restaurarlos
    $menusRequeridos = ['01', '02', '03', '04'];
    $menusAsignados = array_column($menusPrincipales, 'codigo_opcion');
    $menusFaltantes = array_diff($menusRequeridos, $menusAsignados);

    if (!empty($menusFaltantes)) {
        echo "🔧 Restaurando menús principales faltantes...\n";

        $sql = "INSERT INTO perfil_opciones (codigo_perfil, codigo_opcion) VALUES (?, ?)";
        $stmt = $db->prepare($sql);

        foreach ($menusFaltantes as $menu) {
            $stmt->execute([1, $menu]);
            echo "   ✅ Restaurado: {$menu}\n";
        }

        echo "✅ Menús principales restaurados correctamente\n";
    } else {
        echo "✅ Todos los menús principales están asignados\n";
    }

    echo "\n✅ Protección automática completada\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA PROTECCIÓN ===\n";