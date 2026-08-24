<?php
$ordenes    = $ordenes ?? [];
$paginacion = $paginacion ?? ['current_page' => 1, 'total_pages' => 1, 'total' => 0, 'start_record' => 0, 'end_record' => 0, 'has_previous' => false, 'has_next' => false];
$resumen    = $resumen ?? ['total' => 0, 'entregadas' => 0, 'recibidas' => 0, 'anuladas' => 0, 'monto_total' => 0];
$tecnicos   = $tecnicos ?? [];
$motivos    = $motivos ?? [];
$estados    = $estados ?? [];
$filtros    = $filtros ?? [];

// El buscador viaja como "q", no como "busqueda"
$paramsFiltros = [
    'q'           => $filtros['busqueda'] ?? '',
    'tecnico'     => $filtros['tecnico'] ?? '',
    'motivo'      => $filtros['motivo'] ?? '',
    'estado'      => $filtros['estado'] ?? '',
    'fecha_desde' => $filtros['fecha_desde'] ?? '',
    'fecha_hasta' => $filtros['fecha_hasta'] ?? ''
];
$queryFiltros = http_build_query(array_filter($paramsFiltros, function ($v) { return $v !== '' && $v !== null; }));
$hayFiltros = $queryFiltros !== '';

$claseEstado = [
    'entregado' => 'status-badge--warning',
    'recibido'  => 'status-badge--success',
    'anulado'   => 'status-badge--danger'
];

// Los textos de la tabla se muestran en minúsculas: buena parte de los nombres
// vienen en MAYÚSCULAS desde la tabla `cliente` y rompían la lectura.
$min = function ($texto) {
    return mb_strtolower(trim((string)$texto), 'UTF-8');
};

// El técnico externo guarda nombre y apellido en un solo campo, así que para
// mostrar solo el nombre se toma la primera palabra.
$primerNombre = function ($texto) use ($min) {
    $partes = preg_split('/\s+/', trim((string)$texto), -1, PREG_SPLIT_NO_EMPTY);
    return $min($partes[0] ?? '');
};
?>

<style>
/* Los textos de la tabla van en minúsculas. El badge de estado tiene
   text-transform: capitalize por defecto, así que se anula solo aquí. */
#ordenesExternasTable .status-badge {
    text-transform: lowercase;
}
</style>

<!-- Acciones de la página -->
<div class="page-actions">
    <div class="service-detail__meta">
        <span class="service-detail__meta-item">
            <i class="fas fa-clipboard-list"></i> <?= (int)$resumen['total'] ?> órdenes
        </span>
        <span class="service-detail__meta-item">
            <i class="fas fa-truck"></i> <?= (int)$resumen['entregadas'] ?> pendientes de retorno
        </span>
        <span class="service-detail__meta-item">
            <i class="fas fa-check-circle"></i> <?= (int)$resumen['recibidas'] ?> recibidas
        </span>
        <span class="service-detail__meta-item">
            <i class="fas fa-dollar-sign"></i> $<?= number_format((float)$resumen['monto_total'], 0, ',', '.') ?>
        </span>
    </div>

    <div class="service-detail__header-actions">
        <a href="<?= url('tecnicos-externos') ?>" class="btn btn--outline">
            <i class="fas fa-user-cog btn__icon"></i>Técnicos
        </a>
        <a href="<?= url('ordenes-externas/exportar') . ($hayFiltros ? '&' . $queryFiltros : '') ?>"
           class="btn btn--outline">
            <i class="fas fa-file-csv btn__icon"></i>Exportar
        </a>
        <a href="<?= url('ordenes-externas/create') ?>" class="btn btn--primary">
            <i class="fas fa-plus btn__icon"></i>Nueva Orden
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="service-info-card mb-4">
    <div class="service-info-card__header">
        <div class="service-info-card__header-left">
            <h5 class="service-info-card__title">
                <i class="fas fa-filter"></i> Filtros
            </h5>
        </div>
        <?php if ($hayFiltros): ?>
            <a href="<?= url('ordenes-externas') ?>" class="btn btn--outline btn--sm">
                <i class="fas fa-eraser btn__icon"></i>Limpiar
            </a>
        <?php endif; ?>
    </div>
    <div class="service-info-card__body">
        <form method="GET" action="<?= BASE_URL ?>/index.php" class="service-info-grid">
            <input type="hidden" name="route" value="ordenes-externas">

            <div class="service-info__field">
                <label class="service-info__label" for="q">Buscar</label>
                <div class="service-info__input">
                    <i class="fas fa-search service-info__icon"></i>
                    <input type="text" class="form__control" id="q" name="q"
                           value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>"
                           placeholder="Cód. orden o producto">
                </div>
            </div>

            <div class="service-info__field">
                <label class="service-info__label" for="tecnico">Técnico externo</label>
                <div class="service-info__input">
                    <i class="fas fa-user-cog service-info__icon"></i>
                    <select class="form__control" id="tecnico" name="tecnico">
                        <option value="">Todos</option>
                        <?php foreach ($tecnicos as $tecnico): ?>
                            <option value="<?= (int)$tecnico['id'] ?>"
                                <?= (string)($filtros['tecnico'] ?? '') === (string)$tecnico['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tecnico['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="service-info__field">
                <label class="service-info__label" for="motivo">Motivo</label>
                <div class="service-info__input">
                    <i class="fas fa-tags service-info__icon"></i>
                    <select class="form__control" id="motivo" name="motivo">
                        <option value="">Todos</option>
                        <?php foreach ($motivos as $motivo): ?>
                            <option value="<?= (int)$motivo['id'] ?>"
                                <?= (string)($filtros['motivo'] ?? '') === (string)$motivo['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($motivo['descripcion']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="service-info__field">
                <label class="service-info__label" for="estado">Estado</label>
                <div class="service-info__input">
                    <i class="fas fa-flag service-info__icon"></i>
                    <select class="form__control" id="estado" name="estado">
                        <option value="">Todos</option>
                        <?php foreach ($estados as $clave => $estado): ?>
                            <option value="<?= $clave ?>" <?= ($filtros['estado'] ?? '') === $clave ? 'selected' : '' ?>>
                                <?= $estado['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="service-info__field">
                <label class="service-info__label" for="fecha_desde">Desde</label>
                <div class="service-info__input">
                    <i class="fas fa-calendar-day service-info__icon"></i>
                    <input type="date" class="form__control" id="fecha_desde" name="fecha_desde"
                           value="<?= htmlspecialchars($filtros['fecha_desde'] ?? '') ?>">
                </div>
            </div>

            <div class="service-info__field">
                <label class="service-info__label" for="fecha_hasta">Hasta</label>
                <div class="service-info__input">
                    <i class="fas fa-calendar-check service-info__icon"></i>
                    <input type="date" class="form__control" id="fecha_hasta" name="fecha_hasta"
                           value="<?= htmlspecialchars($filtros['fecha_hasta'] ?? '') ?>">
                </div>
            </div>

            <div class="service-info__field service-info__field--full-width">
                <button type="submit" class="btn btn--primary">
                    <i class="fas fa-filter btn__icon"></i>Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table Container -->
<div class="table-container">
    <table class="table table-striped table-hover" id="ordenesExternasTable">
        <thead class="table-dark">
            <tr>
                <th>Cód. Orden</th>
                <th>Fecha</th>
                <th>Técnico Externo</th>
                <th>Empresa</th>
                <th>Detalle del Producto</th>
                <th>Servicio Relacionado</th>
                <th>Motivo</th>
                <th>Entrega</th>
                <th>Recibe</th>
                <th class="text-right">Precio</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ordenes)): ?>
            <tr>
                <td colspan="12" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">No hay órdenes que coincidan con los filtros.</p>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($ordenes as $orden): ?>
            <tr>
                <td>
                    <a href="<?= url('ordenes-externas/view/' . $orden['IdOrden']) ?>" class="btn--link">
                        <strong><?= htmlspecialchars($orden['CodOrden']) ?></strong>
                    </a>
                </td>
                <td><?= date('d/m/Y', strtotime($orden['Fecha'])) ?></td>
                <td>
                    <a href="<?= url('tecnicos-externos/view/' . $orden['IdTecnicoExterno']) ?>"
                       class="btn--link" title="<?= htmlspecialchars($orden['tecnico_nombre']) ?>">
                        <?= htmlspecialchars($primerNombre($orden['tecnico_nombre'])) ?>
                    </a>
                </td>
                <td>
                    <?php if (!empty($orden['tecnico_taller'])): ?>
                        <?= htmlspecialchars($min($orden['tecnico_taller'])) ?>
                    <?php else: ?>
                        <span class="text-muted">n/a</span>
                    <?php endif; ?>
                </td>
                <td title="<?= htmlspecialchars($min($orden['DetalleProducto'])) ?>">
                    <?= htmlspecialchars(mb_strimwidth($min($orden['DetalleProducto']), 0, 45, '...')) ?>
                </td>
                <td>
                    <?php if (!empty($orden['IdServicio'])): ?>
                        <a href="<?= url('servicios/view/' . (int)$orden['IdServicio']) ?>" class="btn--link">
                            #<?= (int)$orden['IdServicio'] ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">n/a</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($min($orden['motivo_descripcion'])) ?></td>
                <td title="<?= htmlspecialchars($orden['entrega_nombre']) ?>">
                    <?= htmlspecialchars($min($orden['entrega_nombres'])) ?: 'n/a' ?>
                </td>
                <td title="<?= htmlspecialchars($orden['recibe_nombre']) ?>">
                    <?php if (!empty($orden['recibe_nombres'])): ?>
                        <?= htmlspecialchars($min($orden['recibe_nombres'])) ?>
                    <?php else: ?>
                        <span class="text-muted">pendiente</span>
                    <?php endif; ?>
                </td>
                <td class="text-right">$<?= number_format((float)$orden['Precio'], 0, ',', '.') ?></td>
                <td class="text-center">
                    <span class="status-badge <?= $claseEstado[$orden['Estado']] ?? 'status-badge--secondary' ?>">
                        <?= $min($estados[$orden['Estado']]['label'] ?? $orden['Estado']) ?>
                    </span>
                </td>
                <td class="text-center">
                    <div class="dropdown d-inline-block">
                        <button class="btn btn--outline btn--sm dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                title="Acciones de la orden">
                            Acciones
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?= url('ordenes-externas/view/' . $orden['IdOrden']) ?>">
                                    <i class="fas fa-external-link-alt text-info me-2"></i>Ver detalle
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url('ordenes-externas/edit/' . $orden['IdOrden']) ?>">
                                    <i class="fas fa-edit text-primary me-2"></i>Editar
                                </a>
                            </li>
                            <?php if ($orden['Estado'] === 'entregado'): ?>
                            <li>
                                <button type="button" class="dropdown-item"
                                        onclick="marcarRecibida(<?= (int)$orden['IdOrden'] ?>, '<?= htmlspecialchars(addslashes($orden['CodOrden'])) ?>')">
                                    <i class="fas fa-check-circle text-success me-2"></i>Marcar como recibida
                                </button>
                            </li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" target="_blank"
                                   href="<?= url('ordenes-externas/imprimir/' . $orden['IdOrden']) ?>">
                                    <i class="fas fa-print text-secondary me-2"></i>Imprimir remisión
                                </a>
                            </li>
                            <?php if ($orden['Estado'] !== 'anulado'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button type="button" class="dropdown-item text-danger"
                                        onclick="anularOrden(<?= (int)$orden['IdOrden'] ?>, '<?= htmlspecialchars(addslashes($orden['CodOrden'])) ?>')">
                                    <i class="fas fa-ban me-2"></i>Anular orden
                                </button>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <?php if ((int)$paginacion['total_pages'] > 1): ?>
        <?php $baseUrl = url('ordenes-externas') . ($hayFiltros ? '&' . $queryFiltros : '') . '&page='; ?>
        <div class="pagination">
            <div class="pagination__info">
                Mostrando <?= (int)$paginacion['start_record'] ?>–<?= (int)$paginacion['end_record'] ?>
                de <?= (int)$paginacion['total'] ?> órdenes
            </div>
            <div class="pagination__controls">
                <?php if ($paginacion['has_previous']): ?>
                    <a href="<?= $baseUrl . ((int)$paginacion['current_page'] - 1) ?>" class="pagination__btn">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                $inicio = max(1, (int)$paginacion['current_page'] - 2);
                $fin = min((int)$paginacion['total_pages'], (int)$paginacion['current_page'] + 2);
                ?>
                <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                    <a href="<?= $baseUrl . $i ?>"
                       class="pagination__btn <?= $i === (int)$paginacion['current_page'] ? 'pagination__btn--active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($paginacion['has_next']): ?>
                    <a href="<?= $baseUrl . ((int)$paginacion['current_page'] + 1) ?>" class="pagination__btn">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Los menús se posicionan con estrategia "fixed" para que no los recorte
// el overflow:hidden de .table-container
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#ordenesExternasTable [data-bs-toggle="dropdown"]')
        .forEach(function (el) {
            new bootstrap.Dropdown(el, { popperConfig: { strategy: 'fixed' } });
        });
});

function marcarRecibida(id, codigo) {
    Swal.fire({
        title: 'Registrar retorno',
        html: 'Se marcará <strong>' + codigo + '</strong> como recibida.',
        icon: 'question',
        input: 'date',
        inputValue: new Date().toISOString().slice(0, 10),
        inputLabel: 'Fecha de recibido',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) { return; }

        fetch('<?= url('ordenes-externas/recibir/') ?>' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ FechaRecibe: result.value })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                Swal.fire({ icon: 'success', title: data.message, timer: 1200, showConfirmButton: false })
                    .then(function () { location.reload(); });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(function () {
            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
        });
    });
}

function anularOrden(id, codigo) {
    Swal.fire({
        title: '¿Anular la orden?',
        html: '<strong>' + codigo + '</strong> quedará anulada, pero se conserva el registro.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(function (result) {
        if (!result.isConfirmed) { return; }

        fetch('<?= url('ordenes-externas/anular/') ?>' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                Swal.fire({ icon: 'success', title: data.message, timer: 1200, showConfirmButton: false })
                    .then(function () { location.reload(); });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(function () {
            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
        });
    });
}
</script>
