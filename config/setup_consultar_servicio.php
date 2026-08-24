<?php
/**
 * Script de Configuración Automática - Consultar Servicios
 * Este script configura automáticamente la opción "Consultar Servicios" en el sistema de permisos
 * 
 * IMPORTANTE: Ejecutar este script solo una vez o cuando se necesite reconfigurar los permisos
 */

// Incluir configuración de base de datos
require_once __DIR__ . '/Database.php';

// Configuración
$setupConfig = [
    'codigo_opcion' => '0205',
    'descripcion' => 'Consultar Servicios',
    'url' => 'servicios/consultar',
    'icono' => 'fas fa-search',
    'submenu' => 0, // 0 = submenú del menú principal 02 (Servicios)
    'activo' => 1,
    'perfiles_con_acceso' => [1, 2, 3, 4] // Administrador, Técnico, Asesor, Técnico Admin
];

// Función para mostrar resultados
function mostrarResultado($titulo, $mensaje, $tipo = 'info') {
    $iconos = [
        'success' => '✅',
        'error' => '❌',
        'warning' => '⚠️',
        'info' => 'ℹ️'
    ];
    
    $icono = $iconos[$tipo] ?? 'ℹ️';
    echo "\n{$icono} {$titulo}\n";
    echo "   {$mensaje}\n";
}

try {
    // Conectar a la base de datos
    $db = Database::getInstance()->getConnection();
    
    echo "\n╔══════════════════════════════════════════════════════════════╗\n";
    echo "║  CONFIGURACIÓN AUTOMÁTICA - CONSULTAR SERVICIOS             ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n";
    
    // Paso 1: Verificar si la opción ya existe
    echo "\n[Paso 1] Verificando si la opción ya existe...\n";
    $sql = "SELECT * FROM opciones WHERE codigo = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$setupConfig['codigo_opcion']]);
    $opcionExiste = $stmt->fetch();
    
    if ($opcionExiste) {
        mostrarResultado(
            'Opción Existente',
            "La opción '{$setupConfig['descripcion']}' ya existe en el sistema.",
            'warning'
        );
        
        // Actualizar la opción existente
        $sql = "UPDATE opciones 
                SET descripcion = ?, url = ?, icono = ?, submenu = ?, activo = ?
                WHERE codigo = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $setupConfig['descripcion'],
            $setupConfig['url'],
            $setupConfig['icono'],
            $setupConfig['submenu'],
            $setupConfig['activo'],
            $setupConfig['codigo_opcion']
        ]);
        
        mostrarResultado(
            'Actualización',
            'Opción actualizada correctamente.',
            'success'
        );
    } else {
        // Insertar nueva opción
        $sql = "INSERT INTO opciones (codigo, descripcion, url, icono, submenu, activo)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $setupConfig['codigo_opcion'],
            $setupConfig['descripcion'],
            $setupConfig['url'],
            $setupConfig['icono'],
            $setupConfig['submenu'],
            $setupConfig['activo']
        ]);
        
        mostrarResultado(
            'Nueva Opción',
            "Opción '{$setupConfig['descripcion']}' creada exitosamente.",
            'success'
        );
    }
    
    // Paso 2: Asignar permisos a perfiles
    echo "\n[Paso 2] Asignando permisos a perfiles...\n";
    
    // Obtener perfiles disponibles
    $sql = "SELECT codigo_perfil, descripcion FROM perfil WHERE activo = 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $perfilesDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($setupConfig['perfiles_con_acceso'] as $codigoPerfil) {
        // Buscar información del perfil
        $perfilInfo = array_filter($perfilesDisponibles, function($p) use ($codigoPerfil) {
            return $p['codigo_perfil'] == $codigoPerfil;
        });
        $perfilInfo = reset($perfilInfo);
        
        if (!$perfilInfo) {
            mostrarResultado(
                "Perfil {$codigoPerfil}",
                "No existe o está inactivo. Omitiendo...",
                'warning'
            );
            continue;
        }
        
        // Verificar si ya tiene el permiso asignado
        $sql = "SELECT * FROM perfil_opciones 
                WHERE codigo_perfil = ? AND codigo_opcion = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$codigoPerfil, $setupConfig['codigo_opcion']]);
        $permisoExiste = $stmt->fetch();
        
        if ($permisoExiste) {
            mostrarResultado(
                $perfilInfo['descripcion'],
                'Ya tiene el permiso asignado.',
                'info'
            );
        } else {
            // Asignar permiso
            $sql = "INSERT INTO perfil_opciones (codigo_perfil, codigo_opcion)
                    VALUES (?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$codigoPerfil, $setupConfig['codigo_opcion']]);
            
            mostrarResultado(
                $perfilInfo['descripcion'],
                'Permiso asignado correctamente.',
                'success'
            );
        }
    }
    
    // Paso 3: Verificar configuración
    echo "\n[Paso 3] Verificando configuración final...\n";
    
    $sql = "SELECT 
                o.codigo,
                o.descripcion as opcion,
                o.url,
                o.icono,
                COUNT(po.codigo_perfil) as perfiles_asignados
            FROM opciones o
            LEFT JOIN perfil_opciones po ON o.codigo = po.codigo_opcion
            WHERE o.codigo = ?
            GROUP BY o.codigo, o.descripcion, o.url, o.icono";
    $stmt = $db->prepare($sql);
    $stmt->execute([$setupConfig['codigo_opcion']]);
    $verificacion = $stmt->fetch();
    
    if ($verificacion) {
        echo "\n┌──────────────────────────────────────────────────────────┐\n";
        echo "│ RESUMEN DE CONFIGURACIÓN                                 │\n";
        echo "├──────────────────────────────────────────────────────────┤\n";
        echo "│ Código:              {$verificacion['codigo']}                                   │\n";
        echo "│ Opción:              {$verificacion['opcion']}                  │\n";
        echo "│ URL:                 {$verificacion['url']}                  │\n";
        echo "│ Icono:               {$verificacion['icono']}                      │\n";
        echo "│ Perfiles Asignados:  {$verificacion['perfiles_asignados']}                                        │\n";
        echo "└──────────────────────────────────────────────────────────┘\n";
        
        // Mostrar perfiles con acceso
        echo "\n[Perfiles con Acceso]\n";
        $sql = "SELECT 
                    p.codigo_perfil,
                    p.descripcion as perfil
                FROM perfil p
                INNER JOIN perfil_opciones po ON p.codigo_perfil = po.codigo_perfil
                WHERE po.codigo_opcion = ?
                ORDER BY p.codigo_perfil";
        $stmt = $db->prepare($sql);
        $stmt->execute([$setupConfig['codigo_opcion']]);
        $perfilesConAcceso = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($perfilesConAcceso as $perfil) {
            echo "  • [{$perfil['codigo_perfil']}] {$perfil['perfil']}\n";
        }
    }
    
    echo "\n╔══════════════════════════════════════════════════════════════╗\n";
    echo "║  ✅ CONFIGURACIÓN COMPLETADA EXITOSAMENTE                    ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n";
    
    echo "\n📝 Próximos pasos:\n";
    echo "   1. Accede al sistema con un usuario que tenga permisos\n";
    echo "   2. Verifica que aparezca la opción en el menú: Servicios > Consultar Servicios\n";
    echo "   3. Prueba la funcionalidad con diferentes filtros\n";
    echo "   4. Verifica la exportación a CSV\n";
    
    echo "\n🔗 URLs de acceso:\n";
    echo "   • Directa: index.php?route=servicios/consultar\n";
    echo "   • Desde menú: Servicios > Consultar Servicios\n\n";
    
} catch (PDOException $e) {
    echo "\n╔══════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ ERROR EN LA CONFIGURACIÓN                                ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n\n";
    
    mostrarResultado(
        'Error de Base de Datos',
        $e->getMessage(),
        'error'
    );
    
    echo "\n💡 Sugerencias:\n";
    echo "   1. Verifica que la base de datos esté accesible\n";
    echo "   2. Confirma que las credenciales en config/Database.php sean correctas\n";
    echo "   3. Verifica que las tablas 'opciones' y 'perfil_opciones' existan\n\n";
    
    exit(1);
} catch (Exception $e) {
    echo "\n╔══════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ ERROR GENERAL                                             ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n\n";
    
    mostrarResultado(
        'Error',
        $e->getMessage(),
        'error'
    );
    
    exit(1);
}
?>

