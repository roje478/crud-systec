<?php
$motivos = $motivos ?? [];
$stats = $stats ?? [];
$colores = $colores ?? [];

$statsPorId = [];
foreach ($stats as $stat) {
    $statsPorId[$stat['id']] = $stat;
}

// Mapear el color del catálogo a las clases status-badge del sistema
$claseBadge = function ($color) {
    $mapa = [
        'primary'   => 'status-badge--info',
        'success'   => 'status-badge--success',
        'warning'   => 'status-badge--warning',
        'danger'    => 'status-badge--danger',
        'info'      => 'status-badge--info',
        'secondary' => 'status-badge--secondary',
        'dark'      => 'status-badge--secondary'
    ];
    return $mapa[$color] ?? 'status-badge--secondary';
};
?>

<!-- Acciones de la página -->
<div class="page-actions">
    <div>
        <h2 class="page-header__title" style="font-size: 1.5rem;">Motivos de Orden Externa</h2>
        <p class="page-header__breadcrumb mb-0">
            Catálogo usado al entregar productos a técnicos externos
        </p>
    </div>
    <div class="service-detail__header-actions">
        <a href="<?= url('configuracion') ?>" class="btn btn--outline">
            <i class="fas fa-arrow-left btn__icon"></i>Volver
        </a>
        <a href="<?= url('configuracion/create-motivo-externo') ?>" class="btn btn--primary">
            <i class="fas fa-plus btn__icon"></i>Nuevo Motivo
        </a>
    </div>
</div>

<!-- Table Container -->
<div class="table-container">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th style="width: 80px;">Orden</th>
                <th>Descripción</th>
                <th>Vista previa</th>
                <th class="text-center">Órdenes</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($motivos)): ?>
            <tr>
                <td colspan="6" class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">
                        No hay motivos configurados. Sin motivos activos no se pueden crear órdenes externas.
                    </p>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($motivos as $motivo): ?>
                <?php $usos = (int)($statsPorId[$motivo['id']]['total_ordenes'] ?? 0); ?>
                <tr>
                    <td><?= (int)$motivo['orden'] ?></td>
                    <td><strong><?= htmlspecialchars($motivo['descripcion']) ?></strong></td>
                    <td>
                        <span class="status-badge <?= $claseBadge($motivo['color']) ?>">
                            <?= htmlspecialchars($motivo['descripcion']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="status-badge status-badge--info"><?= $usos ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ((int)$motivo['activo'] === 1): ?>
                            <span class="status-badge status-badge--success">Activo</span>
                        <?php else: ?>
                            <span class="status-badge status-badge--secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="table__actions" style="justify-content: center;">
                            <a href="<?= url('configuracion/edit-motivo-externo/' . $motivo['id']) ?>"
                               class="table__action-btn table__action-btn--primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="table__action-btn table__action-btn--warning"
                                    onclick="toggleMotivo(<?= (int)$motivo['id'] ?>)"
                                    title="<?= (int)$motivo['activo'] === 1 ? 'Desactivar' : 'Activar' ?>">
                                <i class="fas fa-power-off"></i>
                            </button>
                            <?php if ($usos === 0): ?>
                                <button type="button" class="table__action-btn table__action-btn--danger"
                                        onclick="eliminarMotivo(<?= (int)$motivo['id'] ?>, '<?= htmlspecialchars(addslashes($motivo['descripcion'])) ?>')"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                <button type="button" class="table__action-btn" disabled
                                        title="Tiene órdenes asociadas">
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

<div class="alert alert--info mt-4">
    <i class="fas fa-info-circle"></i>
    Solo los motivos <strong>activos</strong> aparecen en el formulario de
    <a href="<?= url('ordenes-externas/create') ?>">nueva orden externa</a>.
    Un motivo con órdenes asociadas no se puede eliminar, pero sí desactivar.
</div>

<script>
function toggleMotivo(id) {
    fetch('<?= url('configuracion/toggle-motivo-externo/') ?>' + id, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            Swal.fire({ icon: 'success', title: data.message, timer: 1100, showConfirmButton: false })
                .then(function () { location.reload(); });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(function () {
        Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
    });
}

function eliminarMotivo(id, descripcion) {
    Swal.fire({
        title: '¿Eliminar el motivo?',
        html: 'Se eliminará <strong>' + descripcion + '</strong>.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(function (result) {
        if (!result.isConfirmed) { return; }

        fetch('<?= url('configuracion/delete-motivo-externo/') ?>' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                Swal.fire({ icon: 'success', title: data.message, timer: 1100, showConfirmButton: false })
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
