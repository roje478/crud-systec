-- ============================================================================
-- Script de instalación: Módulo de Técnicos Externos y Órdenes Externas
-- ----------------------------------------------------------------------------
-- Crea:
--   1. motivo_orden_externa   -> catálogo de motivos (Reparación, Garantía...)
--   2. tecnico_externo        -> catálogo de técnicos externos
--   3. orden_tecnico_externo  -> órdenes entregadas a técnicos externos
--   4. Opciones de menú (TE, TE01, TE02, TE03, CF05) y permisos por perfil
--
-- El script es IDEMPOTENTE: se puede ejecutar varias veces sin duplicar datos.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Catálogo de motivos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `motivo_orden_externa` (
    `id`             INT NOT NULL AUTO_INCREMENT,
    `descripcion`    VARCHAR(100) NOT NULL,
    `color`          VARCHAR(20)  NOT NULL DEFAULT 'secondary',
    `orden`          INT          NOT NULL DEFAULT 0,
    `activo`         TINYINT(1)   NOT NULL DEFAULT 1,
    `fecha_registro` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_motivo_descripcion` (`descripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `motivo_orden_externa` (`descripcion`, `color`, `orden`, `activo`)
SELECT 'Reparación', 'primary', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM `motivo_orden_externa` WHERE `descripcion` = 'Reparación');

INSERT INTO `motivo_orden_externa` (`descripcion`, `color`, `orden`, `activo`)
SELECT 'Garantía', 'warning', 2, 1
WHERE NOT EXISTS (SELECT 1 FROM `motivo_orden_externa` WHERE `descripcion` = 'Garantía');

INSERT INTO `motivo_orden_externa` (`descripcion`, `color`, `orden`, `activo`)
SELECT 'Revisión', 'info', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM `motivo_orden_externa` WHERE `descripcion` = 'Revisión');

-- ----------------------------------------------------------------------------
-- 2. Catálogo de técnicos externos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tecnico_externo` (
    `id`             INT NOT NULL AUTO_INCREMENT,
    `nombre`         VARCHAR(150) NOT NULL,
    `documento`      VARCHAR(30)  DEFAULT NULL,
    `telefono`       VARCHAR(50)  DEFAULT NULL,
    `correo`         VARCHAR(120) DEFAULT NULL,
    `taller`         VARCHAR(150) DEFAULT NULL,
    `direccion`      VARCHAR(200) DEFAULT NULL,
    `especialidad`   VARCHAR(150) DEFAULT NULL,
    `observaciones`  VARCHAR(500) DEFAULT NULL,
    `activo`         TINYINT(1)   NOT NULL DEFAULT 1,
    `registrado_por` BIGINT       DEFAULT NULL,
    `fecha_registro` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_tecnico_documento` (`documento`),
    KEY `idx_tecnico_nombre` (`nombre`),
    KEY `idx_tecnico_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Órdenes entregadas a técnicos externos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orden_tecnico_externo` (
    `IdOrden`             INT NOT NULL AUTO_INCREMENT,
    `CodOrden`            VARCHAR(50)  NOT NULL,
    `Fecha`               DATE         NOT NULL,
    `IdTecnicoExterno`    INT          NOT NULL,
    `DetalleProducto`     VARCHAR(500) NOT NULL,
    `IdMotivo`            INT          NOT NULL,
    `QuienEntrega`        BIGINT       NOT NULL,
    `QuienRecibe`         BIGINT       DEFAULT NULL,
    `FechaRecibe`         DATE         DEFAULT NULL,
    `Observaciones`       VARCHAR(1000) DEFAULT NULL,
    `Precio`              DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `Estado`              ENUM('entregado','recibido','anulado') NOT NULL DEFAULT 'entregado',
    `IdServicio`          INT          DEFAULT NULL,
    `RegistradoPor`       BIGINT       DEFAULT NULL,
    `FechaRegistro`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `FechaActualizacion`  DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`IdOrden`),
    UNIQUE KEY `uk_orden_codigo` (`CodOrden`),
    KEY `idx_orden_tecnico` (`IdTecnicoExterno`),
    KEY `idx_orden_motivo` (`IdMotivo`),
    KEY `idx_orden_fecha` (`Fecha`),
    KEY `idx_orden_estado` (`Estado`),
    KEY `idx_orden_servicio` (`IdServicio`),
    KEY `idx_orden_entrega` (`QuienEntrega`),
    KEY `idx_orden_recibe` (`QuienRecibe`),
    CONSTRAINT `fk_orden_tecnico_externo`
        FOREIGN KEY (`IdTecnicoExterno`) REFERENCES `tecnico_externo` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_orden_motivo`
        FOREIGN KEY (`IdMotivo`) REFERENCES `motivo_orden_externa` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nota: QuienEntrega / QuienRecibe / RegistradoPor referencian
-- `cliente.no_identificacion` pero NO se declaran como claves foráneas a
-- propósito: esa tabla mezcla clientes y empleados, y una restricción RESTRICT
-- bloquearía su mantenimiento. Van indexadas dentro del CREATE TABLE.

-- ----------------------------------------------------------------------------
-- 4. Opciones de menú y permisos
-- ----------------------------------------------------------------------------
-- IMPORTANTE: PermisoHelper::generarMenu() agrupa las opciones tomando los dos
-- primeros caracteres del código como menú padre. Por eso el menú principal es
-- 'TE' y las subopciones son 'TE01', 'TE02', 'TE03'.

INSERT INTO `opciones` (`codigo`, `descripcion`, `url`, `icono`, `submenu`, `activo`)
SELECT 'TE', 'Técnicos Externos', '#', 'fas fa-truck', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM `opciones` WHERE `codigo` = 'TE');

INSERT INTO `opciones` (`codigo`, `descripcion`, `url`, `icono`, `submenu`, `activo`)
SELECT 'TE01', 'Órdenes Externas', 'ordenes-externas', 'fas fa-clipboard-list', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM `opciones` WHERE `codigo` = 'TE01');

INSERT INTO `opciones` (`codigo`, `descripcion`, `url`, `icono`, `submenu`, `activo`)
SELECT 'TE02', 'Nueva Orden Externa', 'ordenes-externas/create', 'fas fa-plus', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM `opciones` WHERE `codigo` = 'TE02');

INSERT INTO `opciones` (`codigo`, `descripcion`, `url`, `icono`, `submenu`, `activo`)
SELECT 'TE03', 'Gestionar Técnicos Externos', 'tecnicos-externos', 'fas fa-user-cog', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM `opciones` WHERE `codigo` = 'TE03');

-- Catálogo de motivos, dentro del menú de Configuración (CF)
INSERT INTO `opciones` (`codigo`, `descripcion`, `url`, `icono`, `submenu`, `activo`)
SELECT 'CF05', 'Motivos de Orden Externa', 'configuracion/motivos-externos', 'fas fa-tags', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM `opciones` WHERE `codigo` = 'CF05');

-- Asignar las opciones al perfil ADMINISTRADOR (1) y Técnico Administrador (10).
-- Los demás perfiles se gestionan desde: Permisos > Asignar > {perfil}
INSERT INTO `perfil_opciones` (`codigo_perfil`, `codigo_opcion`)
SELECT p.`codigo_perfil`, o.`codigo`
FROM `perfil` p
CROSS JOIN `opciones` o
WHERE p.`codigo_perfil` IN (1, 10)
  AND o.`codigo` IN ('TE', 'TE01', 'TE02', 'TE03', 'CF05')
  AND NOT EXISTS (
      SELECT 1 FROM `perfil_opciones` po
      WHERE po.`codigo_perfil` = p.`codigo_perfil`
        AND po.`codigo_opcion` = o.`codigo`
  );

-- ----------------------------------------------------------------------------
-- 5. Verificación
-- ----------------------------------------------------------------------------
SELECT o.`codigo`, o.`descripcion`, o.`url`, o.`submenu`, o.`activo`,
       COUNT(po.`codigo_perfil`) AS perfiles_con_acceso
FROM `opciones` o
LEFT JOIN `perfil_opciones` po ON po.`codigo_opcion` = o.`codigo`
WHERE o.`codigo` IN ('TE', 'TE01', 'TE02', 'TE03', 'CF05')
GROUP BY o.`codigo`, o.`descripcion`, o.`url`, o.`submenu`, o.`activo`
ORDER BY o.`codigo`;
