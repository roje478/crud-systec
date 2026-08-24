<?php
$tecnicos = $tecnicos ?? [];
$resumen = $resumen ?? ['total' => 0, 'activos' => 0, 'inactivos' => 0];
$busqueda = $busqueda ?? '';
$filtroEstado = $filtroEstado ?? '';
?>

<!-- Acciones de la página -->
<div class="page-actions">
    <form method="GET" action="<?= BASE_URL ?>/index.php" class="d-flex" style="gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <input type="hidden" name="route" value="tecnicos-externos">
        <div class="page-actions__search">
            <i class="fas fa-search page-actions__search-icon"></i>
            <input type="text" class="page-actions__search-input" name="q"
                   value="<?= htmlspecialchars($busqueda) ?>"
                   placeholder="Nombre, documento, taller o teléfono">
        </div>
        <select class="form__control" name="estado" style="width: auto;">
            <option value="">Todos los estados</option>
            <option value="activos" <?= $filtroEstado === 'activos' ? 'selected' : '' ?>>Solo activos</option>
            <option value="inactivos" <?= $filtroEstado === 'inactivos' ? 'selected' : '' ?>>Solo inactivos</option>
        </select>
        <button type="submit" class="btn btn--outline btn--sm">
            <i class="fas fa-filter btn__icon"></i>Filtrar
        </button>
        <?php if ($busqueda !== '' || $filtroEstado !== ''): ?>
            <a href="<?= url('tecnicos-externos') ?>" class="btn btn--outline btn--sm" title="Limpiar filtros">
                <i class="fas fa-eraser btn__icon"></i>
            </a>
        <?php endif; ?>
    </form>

    <div class="service-detail__header-actions">
        <a href="<?= url('ordenes-externas') ?>" class="btn btn--outline">
            <i class="fas fa-clipboard-list btn__icon"></i>Órdenes
        </a>
        <a href="<?= url('tecnicos-externos/create') ?>" class="btn btn--primary">
            <i class="fas fa-plus btn__icon"></i>Nuevo Técnico Externo
        </a>
    </div>
</div>

<!-- Resumen -->
<div class="service-info-grid mb-4">
    <div class="service-info__field">
        <span class="service-info__label">Total de técnicos</span>
        <div class="service-info__input">
            <i class="fas fa-users service-info__icon"></i>
            <?= (int)$resumen['total'] ?>
        </div>
    </div>
    <div class="service-info__field">
        <span class="service-info__label">Activos</span>
        <div class="service-info__input">
            <i class="fas fa-check-circle service-info__icon"></i>
            <?= (int)$resumen['activos'] ?>
        </div>
    </div>
    <div class="service-info__field">
        <span class="service-info__label">Inactivos</span>
        <div class="service-info__input">
            <i class="fas fa-ban service-info__icon"></i>
            <?= (int)$resumen['inactivos'] ?>
        </div>
    </div>
</div>

<!-- Table Container -->
<div class="table-container">
    <table class="table table-striped table-hover" id="tecnicosExternosTable">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Taller / Empresa</th>
                <th>Teléfono</th>
                <th>Especialidad</th>
                <th class="text-center">Órdenes</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tecnicos)): ?>
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="fas fa-user-cog fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">No hay técnicos externos registrados.</p>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($tecnicos as $tecnico): ?>
            <tr>
                <td>
                    <a href="<?= url('tecnicos-externos/view/' . $tecnico['id']) ?>" class="btn--link">
                        <strong><?= htmlspecialchars($tecnico['nombre']) ?></strong>
                    </a>
                </td>
                <td><?= htmlspecialchars($tecnico['documento'] ?: 'N/A') ?></td>
                <td><?= htmlspecialchars($tecnico['taller'] ?: 'N/A') ?></td>
                <td>
                    <?php if (!empty($tecnico['telefono'])): ?>
                        <a href="tel:<?= htmlspecialchars($tecnico['telefono']) ?>" class="btn--link">
                            <?= htmlspecialchars($tecnico['telefono']) ?>
                        </a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($tecnico['especialidad'] ?: 'N/A') ?></td>
                <td class="text-center">
                    <a href="<?= url('ordenes-externas') . '&tecnico=' . (int)$tecnico['id'] ?>"
                       class="status-badge status-badge--info" title="Ver órdenes de este técnico">
                        <?= (int)$tecnico['total_ordenes'] ?>
                    </a>
                    <?php if ((int)$tecnico['ordenes_pendientes'] > 0): ?>
                        <span class="status-badge status-badge--warning" title="Pendientes de retorno">
                            <?= (int)$tecnico['ordenes_pendientes'] ?> pend.
                        </span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if ((int)$tecnico['activo'] === 1): ?>
                        <span class="status-badge status-badge--success">Activo</span>
                    <?php else: ?>
                        <span class="status-badge status-badge--secondary">Inactivo</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <div class="table__actions" style="justify-content: center;">
                        <a href="<?= url('tecnicos-externos/view/' . $tecnico['id']) ?>"
                           class="table__action-btn table__action-btn--info" title="Ver ficha">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="<?= url('tecnicos-externos/edit/' . $tecnico['id']) ?>"
                           class="table__action-btn table__action-btn--primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="table__action-btn table__action-btn--warning"
                                onclick="toggleEstadoTecnico(<?= (int)$tecnico['id'] ?>)"
                                title="<?= (int)$tecnico['activo'] === 1 ? 'Desactivar' : 'Activar' ?>">
                            <i class="fas fa-power-off"></i>
                        </button>
                        <?php if ((int)$tecnico['total_ordenes'] === 0): ?>
                            <button type="button" class="table__action-btn table__action-btn--danger"
                                    onclick="eliminarTecnico(<?= (int)$tecnico['id'] ?>, '<?= htmlspecialchars(addslashes($tecnico['nombre'])) ?>')"
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        <?php else: ?>
                            <button type="button" class="table__action-btn" disabled
                                    title="Tiene órdenes registradas: solo se puede desactivar">
                                <i class="fas fa-lock"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function toggleEstadoTecnico(id) {
    fetch('<?= url('tecnicos-externos/toggle-estado/') ?>' + id, {
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
}

function eliminarTecnico(id, nombre) {
    Swal.fire({
        title: '¿Eliminar técnico externo?',
        html: 'Se eliminará <strong>' + nombre + '</strong> de forma definitiva.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(function (result) {
        if (!result.isConfirmed) { return; }

        fetch('<?= url('tecnicos-externos/delete/') ?>' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                Swal.fire({ icon: 'success', title: data.message, timer: 1200, showConfirmButton: false })
                    .then(function () { location.reload(); });
            } else {
                Swal.fire('No se pudo eliminar', data.message, 'error');
            }
        })
        .catch(function () {
            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
        });
    });
}
</script>
