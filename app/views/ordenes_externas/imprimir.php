<?php
/**
 * Remisión imprimible de una orden externa.
 *
 * Formato MEDIA CARTA: 5.5in x 8.5in (14 x 21,6 cm), vertical.
 * Se renderiza SIN el layout del sistema (sidebar, menú, etc.).
 */
$orden = $orden ?? [];
$estados = $estados ?? OrdenExterna::getEstados();
$estadoActual = $estados[$orden['Estado'] ?? 'entregado'] ?? ['label' => '—'];

$empresa = EmpresaHelper::getEmpresaInfo();
$logoUrl = EmpresaHelper::getLogoUrl();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remisión <?= htmlspecialchars($orden['CodOrden'] ?? '') ?></title>
    <style>
        /* ---------------------------------------------------------------
           MEDIA CARTA: 5.5in x 8.5in = 14cm x 21,6cm (carta partida a la mitad)
           --------------------------------------------------------------- */
        @page {
            size: 5.5in 8.5in;
            margin: 0.5cm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        /* La hoja en pantalla imita el tamaño real de media carta */
        .hoja {
            width: 14cm;
            background: #fff;
            margin: 0 auto;
            padding: 0.5cm;
            border: 1px solid #ddd;
        }

        /* Encabezado --------------------------------------------------- */
        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            border-bottom: 1.5px solid #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .encabezado__logo img { max-height: 34px; max-width: 110px; }
        .encabezado__empresa { font-size: 8px; line-height: 1.35; }
        .encabezado__empresa strong {
            font-size: 11.5px;
            display: block;
            margin-bottom: 1px;
        }
        .encabezado__orden { text-align: right; white-space: nowrap; }
        .encabezado__orden h1 {
            font-size: 9.5px;
            margin: 0 0 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .encabezado__orden .codigo {
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .estado {
            display: inline-block;
            padding: 1px 6px;
            border: 1px solid #000;
            border-radius: 2px;
            font-size: 7px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Tablas de datos ---------------------------------------------- */
        table.datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.datos th,
        table.datos td {
            border: 1px solid #999;
            padding: 4px 5px;
            text-align: left;
            vertical-align: top;
            font-size: 9px;
        }
        table.datos th {
            background: #eee;
            width: 22%;
            font-size: 7.5px;
            text-transform: uppercase;
            color: #333;
            font-weight: bold;
        }

        /* Bloques de texto --------------------------------------------- */
        .bloque-titulo {
            font-size: 7px;
            text-transform: uppercase;
            color: #333;
            border-bottom: 1px solid #999;
            padding-bottom: 2px;
            margin: 8px 0 3px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .bloque-texto {
            border: 1px solid #999;
            padding: 5px 6px;
            min-height: 42px;
            font-size: 9px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .bloque-texto--corto { min-height: 30px; }

        /* Firmas ------------------------------------------------------- */
        .firmas {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 26px;
        }
        .firma { flex: 1; text-align: center; }
        .firma__linea {
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 8.5px;
        }

        .pie {
            margin-top: 8px;
            border-top: 1px solid #ccc;
            padding-top: 4px;
            font-size: 7px;
            color: #666;
            text-align: center;
        }

        /* Barra de acciones (solo pantalla) ---------------------------- */
        .acciones { width: 14cm; margin: 0 auto 12px; text-align: right; }
        .acciones button,
        .acciones a {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            padding: 7px 14px;
            border: 1px solid #333;
            background: #333;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .acciones a { background: #fff; color: #333; }

        /* Impresión ----------------------------------------------------- */
        @media print {
            body {
                background: #fff;
                padding: 0;
                font-size: 9.5px;
            }
            .hoja {
                width: auto;
                margin: 0;
                padding: 0;
                border: none;
            }
            .acciones { display: none; }
            .encabezado,
            .firmas,
            table.datos { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="acciones">
    <a href="<?= url('ordenes-externas/view/' . ($orden['IdOrden'] ?? '')) ?>">Volver</a>
    <button onclick="window.print()">Imprimir</button>
</div>

<div class="hoja">
    <div class="encabezado">
        <div>
            <?php if ($logoUrl): ?>
                <div class="encabezado__logo">
                    <img src="<?= $logoUrl ?>" alt="<?= htmlspecialchars($empresa['nombreempresa'] ?? '') ?>">
                </div>
            <?php endif; ?>
            <div class="encabezado__empresa">
                <strong><?= htmlspecialchars($empresa['nombreempresa'] ?? '') ?></strong>
                <?php if (!empty($empresa['nit'])): ?>NIT: <?= htmlspecialchars($empresa['nit']) ?><br><?php endif; ?>
                <?= htmlspecialchars($empresa['direccion'] ?? '') ?><br>
                <?= htmlspecialchars($empresa['telefono'] ?? '') ?><br>
                <?= htmlspecialchars($empresa['correo'] ?? '') ?>
            </div>
        </div>
        <div class="encabezado__orden">
            <h1>Remisión a técnico externo</h1>
            <div class="codigo"><?= htmlspecialchars($orden['CodOrden'] ?? '') ?></div>
            <div class="estado"><?= $estadoActual['label'] ?></div>
        </div>
    </div>

    <table class="datos">
        <tr>
            <th>Fecha de entrega</th>
            <td><?= date('d/m/Y', strtotime($orden['Fecha'])) ?></td>
            <th>Motivo</th>
            <td><?= htmlspecialchars($orden['motivo_descripcion'] ?? '') ?></td>
        </tr>
        <tr>
            <th>Técnico externo</th>
            <td><?= htmlspecialchars($orden['tecnico_nombre'] ?? '') ?></td>
            <th>Taller / Empresa</th>
            <td><?= htmlspecialchars($orden['tecnico_taller'] ?? '—') ?></td>
        </tr>
        <tr>
            <th>Documento</th>
            <td><?= htmlspecialchars($orden['tecnico_documento'] ?? '—') ?></td>
            <th>Teléfono</th>
            <td><?= htmlspecialchars($orden['tecnico_telefono'] ?? '—') ?></td>
        </tr>
        <tr>
            <th>Precio acordado</th>
            <td>$<?= number_format((float)($orden['Precio'] ?? 0), 0, ',', '.') ?></td>
            <th>Servicio relacionado</th>
            <td><?= !empty($orden['IdServicio']) ? '#' . (int)$orden['IdServicio'] : '—' ?></td>
        </tr>
    </table>

    <div class="bloque-titulo">Detalle del producto</div>
    <div class="bloque-texto"><?= htmlspecialchars($orden['DetalleProducto'] ?? '') ?></div>

    <div class="bloque-titulo">Observaciones</div>
    <div class="bloque-texto bloque-texto--corto"><?= htmlspecialchars($orden['Observaciones'] ?? '') ?></div>

    <table class="datos" style="margin-top: 8px;">
        <tr>
            <th>Entrega</th>
            <td><?= htmlspecialchars($orden['entrega_nombre'] ?? '—') ?></td>
            <th>Recibe / recoge</th>
            <td>
                <?= htmlspecialchars($orden['recibe_nombre'] ?? '') ?: 'Pendiente' ?>
                <?php if (!empty($orden['FechaRecibe'])): ?>
                    (<?= date('d/m/Y', strtotime($orden['FechaRecibe'])) ?>)
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <div class="cierre">
        <div class="firmas">
            <div class="firma">
                <div class="firma__linea">
                    Firma de quien entrega<br>
                    <?= htmlspecialchars($orden['entrega_nombre'] ?? '') ?>
                </div>
            </div>
            <div class="firma">
                <div class="firma__linea">
                    Firma del técnico externo<br>
                    <?= htmlspecialchars($orden['tecnico_nombre'] ?? '') ?>
                </div>
            </div>
        </div>

        <div class="pie">
            Documento generado el <?= date('d/m/Y H:i') ?> &middot;
            <?= htmlspecialchars($empresa['nombreempresa'] ?? '') ?>
        </div>
    </div>
</div>

</body>
</html>
