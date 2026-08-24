<?php
/**
 * Remisión imprimible de una orden externa.
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
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 24px;
            background: #f5f5f5;
        }
        .hoja {
            background: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 32px;
            border: 1px solid #ddd;
        }
        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #333;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .encabezado__logo img { max-height: 70px; max-width: 220px; }
        .encabezado__empresa { font-size: 11px; line-height: 1.5; }
        .encabezado__empresa strong { font-size: 15px; display: block; margin-bottom: 4px; }
        .encabezado__orden { text-align: right; }
        .encabezado__orden h1 { font-size: 16px; margin: 0 0 6px; text-transform: uppercase; }
        .encabezado__orden .codigo { font-size: 20px; font-weight: bold; letter-spacing: 1px; }
        .estado {
            display: inline-block;
            padding: 3px 10px;
            border: 1px solid #333;
            border-radius: 3px;
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 6px;
        }
        table.datos { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        table.datos th, table.datos td {
            border: 1px solid #ccc;
            padding: 7px 9px;
            text-align: left;
            vertical-align: top;
        }
        table.datos th {
            background: #f0f0f0;
            width: 22%;
            font-size: 11px;
            text-transform: uppercase;
            color: #555;
        }
        .bloque-titulo {
            font-size: 11px;
            text-transform: uppercase;
            color: #555;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
            margin: 18px 0 8px;
            font-weight: bold;
        }
        .bloque-texto {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 60px;
            white-space: pre-wrap;
        }
        .firmas {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-top: 60px;
        }
        .firma { flex: 1; text-align: center; }
        .firma__linea { border-top: 1px solid #333; padding-top: 6px; font-size: 11px; }
        .pie {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
            color: #777;
            text-align: center;
        }
        .acciones { max-width: 800px; margin: 0 auto 16px; text-align: right; }
        .acciones button, .acciones a {
            font-size: 13px;
            padding: 8px 16px;
            border: 1px solid #333;
            background: #333;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .acciones a { background: #fff; color: #333; }
        @media print {
            body { background: #fff; padding: 0; }
            .hoja { border: none; max-width: none; padding: 0; }
            .acciones { display: none; }
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
    <div class="bloque-texto"><?= htmlspecialchars($orden['Observaciones'] ?? '') ?></div>

    <table class="datos" style="margin-top: 18px;">
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

</body>
</html>
