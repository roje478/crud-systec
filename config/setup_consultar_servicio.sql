-- Script SQL para configurar la funcionalidad de Consultar Servicios
-- Este script agrega la opción en el sistema de permisos

-- 1. Verificar si la opción ya existe, si no, agregarla
INSERT INTO opciones (codigo, descripcion, url, icono, submenu, activo)
SELECT '0205', 'Consultar Servicios', 'servicios/consultar', 'fas fa-search', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM opciones WHERE codigo = '0205');

-- 2. Asignar la opción al perfil de Administrador (codigo_perfil = 1)
INSERT INTO perfil_opciones (codigo_perfil, codigo_opcion)
SELECT 1, '0205'
WHERE NOT EXISTS (
    SELECT 1 FROM perfil_opciones 
    WHERE codigo_perfil = 1 AND codigo_opcion = '0205'
);

-- 3. Asignar la opción al perfil de Técnico (codigo_perfil = 2) - si existe
INSERT INTO perfil_opciones (codigo_perfil, codigo_opcion)
SELECT 2, '0205'
WHERE EXISTS (SELECT 1 FROM perfil WHERE codigo_perfil = 2)
AND NOT EXISTS (
    SELECT 1 FROM perfil_opciones 
    WHERE codigo_perfil = 2 AND codigo_opcion = '0205'
);

-- 4. Asignar la opción al perfil de Asesor (codigo_perfil = 3) - si existe
INSERT INTO perfil_opciones (codigo_perfil, codigo_opcion)
SELECT 3, '0205'
WHERE EXISTS (SELECT 1 FROM perfil WHERE codigo_perfil = 3)
AND NOT EXISTS (
    SELECT 1 FROM perfil_opciones 
    WHERE codigo_perfil = 3 AND codigo_opcion = '0205'
);

-- 5. Asignar la opción al perfil de Técnico Administrador (codigo_perfil = 4) - si existe
INSERT INTO perfil_opciones (codigo_perfil, codigo_opcion)
SELECT 4, '0205'
WHERE EXISTS (SELECT 1 FROM perfil WHERE codigo_perfil = 4)
AND NOT EXISTS (
    SELECT 1 FROM perfil_opciones 
    WHERE codigo_perfil = 4 AND codigo_opcion = '0205'
);

-- Verificar que se haya agregado correctamente
SELECT 
    o.codigo,
    o.descripcion,
    o.url,
    o.icono,
    o.submenu,
    o.activo,
    COUNT(po.codigo_perfil) as perfiles_asignados
FROM opciones o
LEFT JOIN perfil_opciones po ON o.codigo = po.codigo_opcion
WHERE o.codigo = '0205'
GROUP BY o.codigo, o.descripcion, o.url, o.icono, o.submenu, o.activo;

-- Mostrar los perfiles que tienen acceso a esta opción
SELECT 
    p.codigo_perfil,
    p.descripcion as perfil,
    o.descripcion as opcion,
    o.url
FROM perfil p
INNER JOIN perfil_opciones po ON p.codigo_perfil = po.codigo_perfil
INNER JOIN opciones o ON po.codigo_opcion = o.codigo
WHERE o.codigo = '0205'
ORDER BY p.codigo_perfil;

