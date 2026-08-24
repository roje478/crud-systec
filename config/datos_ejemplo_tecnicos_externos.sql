-- ============================================================================
-- DATOS DE EJEMPLO - Módulo de Técnicos Externos
-- ----------------------------------------------------------------------------
-- Crea 4 técnicos externos y 5 órdenes que cubren los tres estados
-- (entregado / recibido / anulado) y los tres motivos por defecto.
--
-- Ejecutar:  mysql -u USUARIO -p BASE < config/datos_ejemplo_tecnicos_externos.sql
--
-- PARA BORRARLOS después, ejecuta solo estas dos líneas:
--     DELETE FROM orden_tecnico_externo WHERE CodOrden LIKE 'OE-000%';
--     DELETE FROM tecnico_externo WHERE documento IN ('900123456','52814907','1098765432','800345112');
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Técnicos externos
-- ----------------------------------------------------------------------------
INSERT INTO `tecnico_externo`
    (`nombre`, `documento`, `telefono`, `correo`, `taller`, `direccion`, `especialidad`, `observaciones`, `activo`)
SELECT 'Carlos Ramírez', '900123456', '3159876543', 'contacto@electrofix.com',
       'ElectroFix', 'Carrera 12 # 8-45', 'Tarjetas electrónicas y fuentes de poder',
       'Responde el mismo día. Garantía de 30 días sobre la reparación.', 1
WHERE NOT EXISTS (SELECT 1 FROM `tecnico_externo` WHERE `documento` = '900123456');

INSERT INTO `tecnico_externo`
    (`nombre`, `documento`, `telefono`, `correo`, `taller`, `direccion`, `especialidad`, `observaciones`, `activo`)
SELECT 'Marta Gutiérrez', '52814907', '3204451188', 'refrimundo@correo.com',
       'Refrimundo', 'Calle 30 # 15-22', 'Refrigeración y aire acondicionado',
       'Solo recibe equipos de lunes a viernes antes de las 4 p.m.', 1
WHERE NOT EXISTS (SELECT 1 FROM `tecnico_externo` WHERE `documento` = '52814907');

INSERT INTO `tecnico_externo`
    (`nombre`, `documento`, `telefono`, `correo`, `taller`, `direccion`, `especialidad`, `observaciones`, `activo`)
SELECT 'Andrés Peña', '1098765432', '3012239087', 'andres.pena@tecnodisplay.co',
       'TecnoDisplay', 'Avenida 5 # 40-10 Local 3', 'Pantallas, displays y táctiles',
       'Cobra diagnóstico aparte si el equipo no se repara.', 1
WHERE NOT EXISTS (SELECT 1 FROM `tecnico_externo` WHERE `documento` = '1098765432');

INSERT INTO `tecnico_externo`
    (`nombre`, `documento`, `telefono`, `correo`, `taller`, `direccion`, `especialidad`, `observaciones`, `activo`)
SELECT 'Servicios JR', '800345112', '3181144207', NULL,
       'Servicios JR', 'Calle 8 # 3-19', 'Electrodomésticos de línea blanca',
       'Inactivo desde julio de 2026: no volvió a responder.', 0
WHERE NOT EXISTS (SELECT 1 FROM `tecnico_externo` WHERE `documento` = '800345112');

-- ----------------------------------------------------------------------------
-- 2. Órdenes de ejemplo
-- ----------------------------------------------------------------------------
-- Los técnicos, motivos y responsables se referencian por sus datos, no por id,
-- para que el script funcione sin importar los autoincrementales de cada base.
-- Los responsables internos se toman de los usuarios activos del sistema.

SET @tec_electrofix   = (SELECT id FROM tecnico_externo WHERE documento = '900123456');
SET @tec_refrimundo   = (SELECT id FROM tecnico_externo WHERE documento = '52814907');
SET @tec_tecnodisplay = (SELECT id FROM tecnico_externo WHERE documento = '1098765432');

SET @mot_reparacion = (SELECT id FROM motivo_orden_externa WHERE descripcion = 'Reparación');
SET @mot_garantia   = (SELECT id FROM motivo_orden_externa WHERE descripcion = 'Garantía');
SET @mot_revision   = (SELECT id FROM motivo_orden_externa WHERE descripcion = 'Revisión');

-- Primer y segundo usuario activo del sistema, como quien entrega y quien recibe
SET @user_a = (SELECT no_identificacion FROM usuario WHERE activo = 1 ORDER BY codigo_perfil, no_identificacion LIMIT 1);
SET @user_b = (SELECT no_identificacion FROM usuario WHERE activo = 1 AND no_identificacion <> @user_a
               ORDER BY codigo_perfil, no_identificacion LIMIT 1);

-- Un servicio interno real, para mostrar la columna "Servicio relacionado"
SET @servicio_demo = (SELECT IdServicio FROM servicio ORDER BY IdServicio DESC LIMIT 1);

-- Orden 1: ENTREGADA, pendiente de retorno
INSERT INTO `orden_tecnico_externo`
    (`CodOrden`, `Fecha`, `IdTecnicoExterno`, `DetalleProducto`, `IdMotivo`,
     `QuienEntrega`, `QuienRecibe`, `FechaRecibe`, `Observaciones`, `Precio`, `Estado`, `RegistradoPor`, `IdServicio`)
SELECT 'OE-0001', DATE_SUB(CURDATE(), INTERVAL 3 DAY), @tec_electrofix,
       'Portátil HP 245 G8, serial 5CD1234ABX. Entra con cargador original y funda negra. No enciende, se sospecha daño en la tarjeta madre.',
       @mot_reparacion, @user_a, NULL, NULL,
       'El técnico confirma diagnóstico en 48 horas antes de autorizar la reparación.',
       85000.00, 'entregado', @user_a, @servicio_demo
WHERE NOT EXISTS (SELECT 1 FROM `orden_tecnico_externo` WHERE `CodOrden` = 'OE-0001');

-- Orden 2: RECIBIDA, por garantía (sin costo)
INSERT INTO `orden_tecnico_externo`
    (`CodOrden`, `Fecha`, `IdTecnicoExterno`, `DetalleProducto`, `IdMotivo`,
     `QuienEntrega`, `QuienRecibe`, `FechaRecibe`, `Observaciones`, `Precio`, `Estado`, `RegistradoPor`)
SELECT 'OE-0002', DATE_SUB(CURDATE(), INTERVAL 12 DAY), @tec_refrimundo,
       'Nevera Samsung RT38K5982SL. Reingresa por la misma falla de enfriamiento reparada el mes pasado.',
       @mot_garantia, @user_a, @user_b, DATE_SUB(CURDATE(), INTERVAL 5 DAY),
       'Cubierto por la garantía de la reparación anterior. Sin costo para el cliente.',
       0.00, 'recibido', @user_a
WHERE NOT EXISTS (SELECT 1 FROM `orden_tecnico_externo` WHERE `CodOrden` = 'OE-0002');

-- Orden 3: RECIBIDA, revisión con costo
INSERT INTO `orden_tecnico_externo`
    (`CodOrden`, `Fecha`, `IdTecnicoExterno`, `DetalleProducto`, `IdMotivo`,
     `QuienEntrega`, `QuienRecibe`, `FechaRecibe`, `Observaciones`, `Precio`, `Estado`, `RegistradoPor`)
SELECT 'OE-0003', DATE_SUB(CURDATE(), INTERVAL 20 DAY), @tec_tecnodisplay,
       'Pantalla LG 24MK430H de 24 pulgadas. Presenta líneas verticales en el costado derecho. Se envía sin cable HDMI.',
       @mot_revision, @user_b, @user_b, DATE_SUB(CURDATE(), INTERVAL 14 DAY),
       'Revisión sin reparación: el panel no tiene repuesto disponible. Se cobra solo el diagnóstico.',
       45000.00, 'recibido', @user_b
WHERE NOT EXISTS (SELECT 1 FROM `orden_tecnico_externo` WHERE `CodOrden` = 'OE-0003');

-- Orden 4: ENTREGADA, valor alto
INSERT INTO `orden_tecnico_externo`
    (`CodOrden`, `Fecha`, `IdTecnicoExterno`, `DetalleProducto`, `IdMotivo`,
     `QuienEntrega`, `QuienRecibe`, `FechaRecibe`, `Observaciones`, `Precio`, `Estado`, `RegistradoPor`)
SELECT 'OE-0004', DATE_SUB(CURDATE(), INTERVAL 1 DAY), @tec_electrofix,
       'Impresora Epson L3250 multifuncional. Atasca el papel y no reconoce el cartucho de tinta negra. Incluye cable de poder y USB.',
       @mot_reparacion, @user_b, NULL, NULL,
       'Cliente autoriza hasta $150.000. Prometido para el viernes.',
       120000.00, 'entregado', @user_b
WHERE NOT EXISTS (SELECT 1 FROM `orden_tecnico_externo` WHERE `CodOrden` = 'OE-0004');

-- Orden 5: ANULADA
INSERT INTO `orden_tecnico_externo`
    (`CodOrden`, `Fecha`, `IdTecnicoExterno`, `DetalleProducto`, `IdMotivo`,
     `QuienEntrega`, `QuienRecibe`, `FechaRecibe`, `Observaciones`, `Precio`, `Estado`, `RegistradoPor`)
SELECT 'OE-0005', DATE_SUB(CURDATE(), INTERVAL 8 DAY), @tec_refrimundo,
       'Aire acondicionado minisplit LG 12000 BTU. Unidad exterior con ruido excesivo.',
       @mot_reparacion, @user_a, NULL, NULL,
       'Anulada: el cliente retiró el equipo antes de llevarlo al taller externo.',
       200000.00, 'anulado', @user_a
WHERE NOT EXISTS (SELECT 1 FROM `orden_tecnico_externo` WHERE `CodOrden` = 'OE-0005');

-- ----------------------------------------------------------------------------
-- 3. Verificación
-- ----------------------------------------------------------------------------
SELECT o.CodOrden, o.Fecha, t.nombre AS tecnico, m.descripcion AS motivo,
       o.Estado, o.Precio
FROM orden_tecnico_externo o
JOIN tecnico_externo t      ON t.id = o.IdTecnicoExterno
JOIN motivo_orden_externa m ON m.id = o.IdMotivo
ORDER BY o.CodOrden;
