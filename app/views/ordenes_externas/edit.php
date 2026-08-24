<?php
$orden = $orden ?? [];
$estados = $estados ?? OrdenExterna::getEstados();
$esEdicion = true;

$claseEstado = [
    'entregado' => 'status-badge--warning',
    'recibido'  => 'status-badge--success',
    'anulado'   => 'status-badge--danger'
];
$estadoActual = $estados[$orden['Estado'] ?? 'entregado'] ?? ['label' => 'N/A'];
?>

<div class="service-detail">
    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Formulario -->
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">
                                    Editar Orden <?= htmlspecialchars($orden['CodOrden'] ?? '') ?>
                                    <span class="status-badge <?= $claseEstado[$orden['Estado'] ?? ''] ?? 'status-badge--secondary' ?>">
                                        <?= $estadoActual['label'] ?>
                                    </span>
                                </h3>
                                <p class="form-intro__description"><?= htmlspecialchars($orden['tecnico_nombre'] ?? '') ?></p>
                            </div>
                            <div class="service-detail__header-actions">
                                <a href="<?= url('ordenes-externas/view/' . ($orden['IdOrden'] ?? '')) ?>" class="btn btn--outline">
                                    <i class="fas fa-external-link-alt btn__icon"></i>Ver Detalle
                                </a>
                                <a href="<?= url('ordenes-externas') ?>" class="btn btn--outline">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn--primary" form="formOrdenExterna">
                                    <i class="fas fa-save btn__icon"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="formOrdenExterna" class="service-info-grid"
                          action="<?= url('ordenes-externas/update/' . ($orden['IdOrden'] ?? '')) ?>" method="POST">
                        <?php include __DIR__ . '/_form.php'; ?>

                        <!-- Estado -->
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label" for="Estado">Estado de la orden</label>
                            <div class="service-info__input">
                                <i class="fas fa-flag service-info__icon"></i>
                                <select class="form__control" id="Estado" name="Estado">
                                    <?php foreach ($estados as $clave => $estado): ?>
                                        <option value="<?= $clave ?>" <?= ($orden['Estado'] ?? '') === $clave ? 'selected' : '' ?>>
                                            <?= $estado['label'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form__text">
                                Entre <em>Entregado</em> y <em>Recibido</em> el estado se ajusta solo según
                                si registras quién recibe. Elige <em>Anulado</em> para cancelar la orden.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="service-detail__sidebar">
            <div class="service-info-card service-info-card--info">
                <div class="service-info-card__header">
                    <div class="service-info-card__header-left">
                        <h5 class="service-info-card__title">
                            <i class="fas fa-history"></i> Auditoría
                        </h5>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="additional-info">
                        <div class="additional-info__item">
                            <span class="additional-info__label">Registrado por</span>
                            <span class="additional-info__value"><?= htmlspecialchars($orden['registrado_nombre'] ?? '') ?: 'N/A' ?></span>
                        </div>
                        <div class="additional-info__item">
                            <span class="additional-info__label">Fecha registro</span>
                            <span class="additional-info__value">
                                <?= !empty($orden['FechaRegistro']) ? date('d/m/Y H:i', strtotime($orden['FechaRegistro'])) : 'N/A' ?>
                            </span>
                        </div>
                        <div class="additional-info__item">
                            <span class="additional-info__label">Actualizado</span>
                            <span class="additional-info__value">
                                <?= !empty($orden['FechaActualizacion']) ? date('d/m/Y H:i', strtotime($orden['FechaActualizacion'])) : 'N/A' ?>
                            </span>
                        </div>
                    </div>

                    <div class="alert alert--danger mt-3 mb-0">
                        <h6 class="alert__heading">
                            <i class="fas fa-exclamation-triangle"></i> Zona de riesgo
                        </h6>
                        <p>Eliminar la orden borra el registro de forma definitiva.
                           Si solo quieres cancelarla, usa el estado <em>Anulado</em>.</p>
                        <button type="button" class="btn btn--outline quick-actions__btn"
                                onclick="eliminarOrden(<?= (int)($orden['IdOrden'] ?? 0) ?>, '<?= htmlspecialchars(addslashes($orden['CodOrden'] ?? '')) ?>')">
                            <i class="fas fa-trash btn__icon"></i>Eliminar Orden
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/_modal_tecnico.php'; ?>

<script>
function eliminarOrden(id, codigo) {
    Swal.fire({
        title: '¿Eliminar la orden?',
        html: 'Se eliminará <strong>' + codigo + '</strong> de forma definitiva.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(function (result) {
        if (!result.isConfirmed) { return; }

        fetch('<?= url('ordenes-externas/delete/') ?>' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                window.location.href = '<?= url('ordenes-externas') ?>';
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
