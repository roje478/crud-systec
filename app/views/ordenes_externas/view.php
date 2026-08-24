<?php
$orden = $orden ?? [];
$responsables = $responsables ?? [];
$estados = $estados ?? OrdenExterna::getEstados();

$claseEstado = [
    'entregado' => 'status-badge--warning',
    'recibido'  => 'status-badge--success',
    'anulado'   => 'status-badge--danger'
];
$estadoActual = $estados[$orden['Estado'] ?? 'entregado'] ?? ['label' => 'N/A'];
$recibida = !empty($orden['QuienRecibe']);
?>

<div class="service-detail">
    <!-- Encabezado -->
    <div class="service-detail__header">
        <div class="service-detail__header-left">
            <div class="service-detail__title-section">
                <h1 class="service-detail__title"><?= htmlspecialchars($orden['CodOrden'] ?? '') ?></h1>
                <span class="status-badge <?= $claseEstado[$orden['Estado'] ?? ''] ?? 'status-badge--secondary' ?>">
                    <?= $estadoActual['label'] ?>
                </span>
            </div>
            <div class="service-detail__meta">
                <span class="service-detail__meta-item">
                    <i class="fas fa-calendar-day"></i>
                    <?= date('d/m/Y', strtotime($orden['Fecha'])) ?>
                </span>
                <span class="service-detail__meta-item">
                    <i class="fas fa-user-cog"></i>
                    <?= htmlspecialchars($orden['tecnico_nombre'] ?? '') ?>
                </span>
                <span class="service-detail__meta-item">
                    <i class="fas fa-tags"></i>
                    <?= htmlspecialchars($orden['motivo_descripcion'] ?? '') ?>
                </span>
                <?php if (!empty($orden['registrado_nombre'])): ?>
                <span class="service-detail__meta-item">
                    <i class="fas fa-user-edit"></i>
                    Registró <?= htmlspecialchars($orden['registrado_nombre']) ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="service-detail__header-actions">
            <a href="<?= url('ordenes-externas') ?>" class="btn btn--outline">
                <i class="fas fa-arrow-left btn__icon"></i>Volver
            </a>
            <a href="<?= url('ordenes-externas/imprimir/' . $orden['IdOrden']) ?>" target="_blank" class="btn btn--outline">
                <i class="fas fa-print btn__icon"></i>Imprimir
            </a>
            <?php if (($orden['Estado'] ?? '') === 'entregado'): ?>
                <button type="button" class="btn btn--outline" onclick="marcarRecibidaDetalle(<?= (int)$orden['IdOrden'] ?>)">
                    <i class="fas fa-check-circle btn__icon"></i>Registrar Retorno
                </button>
            <?php endif; ?>
            <a href="<?= url('ordenes-externas/edit/' . $orden['IdOrden']) ?>" class="btn btn--primary">
                <i class="fas fa-edit btn__icon"></i>Editar
            </a>
        </div>
    </div>

    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Datos de la orden -->
            <div class="service-info-card">
                <div class="service-info-card__header">
                    <div class="service-info-card__header-left">
                        <div class="form-intro__icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h5 class="service-info-card__title">Datos de la Orden</h5>
                    </div>
                    <div class="service-info-card__cost">
                        $<?= number_format((float)$orden['Precio'], 0, ',', '.') ?>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="service-info-grid">
                        <div class="service-info__field">
                            <span class="service-info__label">Fecha de entrega</span>
                            <div class="service-info__input">
                                <i class="fas fa-calendar-day service-info__icon"></i>
                                <?= date('d/m/Y', strtotime($orden['Fecha'])) ?>
                            </div>
                        </div>
                        <div class="service-info__field">
                            <span class="service-info__label">Motivo</span>
                            <div class="service-info__input">
                                <i class="fas fa-tags service-info__icon"></i>
                                <?= htmlspecialchars($orden['motivo_descripcion']) ?>
                            </div>
                        </div>
                        <div class="service-info__field">
                            <span class="service-info__label">Servicio relacionado</span>
                            <div class="service-info__input">
                                <i class="fas fa-link service-info__icon"></i>
                                <?php if (!empty($orden['IdServicio'])): ?>
                                    <a href="<?= url('servicios/view/' . (int)$orden['IdServicio']) ?>" class="btn--link">
                                        #<?= (int)$orden['IdServicio'] ?>
                                    </a>
                                <?php else: ?>N/A<?php endif; ?>
                            </div>
                        </div>

                        <div class="service-info__field service-info__field--full-width">
                            <span class="service-info__label">Detalle del producto</span>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-box service-info__icon"></i>
                                <span><?= nl2br(htmlspecialchars($orden['DetalleProducto'])) ?></span>
                            </div>
                        </div>

                        <?php if (!empty($orden['Observaciones'])): ?>
                        <div class="service-info__field service-info__field--full-width">
                            <span class="service-info__label">Observaciones</span>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-comment-dots service-info__icon"></i>
                                <span><?= nl2br(htmlspecialchars($orden['Observaciones'])) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Trazabilidad -->
            <div class="service-content-card">
                <div class="service-content-card__header">
                    <h5 class="service-content-card__title">
                        <i class="fas fa-stream"></i> Trazabilidad
                    </h5>
                </div>
                <div class="service-content-card__body">
                    <div class="service-timeline">
                        <div class="service-timeline__item service-timeline__item--completed">
                            <div class="service-timeline__dot"></div>
                            <div class="service-timeline__content">
                                <div class="service-timeline__title">Entregado al técnico externo</div>
                                <div class="service-timeline__description">
                                    Por <?= htmlspecialchars($orden['entrega_nombre']) ?: 'sin registrar' ?>
                                    &middot; <?= date('d/m/Y', strtotime($orden['Fecha'])) ?>
                                </div>
                            </div>
                        </div>

                        <div class="service-timeline__item <?= $recibida ? 'service-timeline__item--completed' : 'service-timeline__item--current' ?>">
                            <div class="service-timeline__dot"></div>
                            <div class="service-timeline__content">
                                <?php if ($recibida): ?>
                                    <div class="service-timeline__title">Producto recibido</div>
                                    <div class="service-timeline__description">
                                        Por <?= htmlspecialchars($orden['recibe_nombre']) ?>
                                        <?php if (!empty($orden['FechaRecibe'])): ?>
                                            &middot; <?= date('d/m/Y', strtotime($orden['FechaRecibe'])) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="service-timeline__title">Pendiente de retorno</div>
                                    <div class="service-timeline__description">
                                        Aún no se ha registrado quién recibe el producto.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="service-detail__sidebar">
            <div class="service-info-card service-info-card--info">
                <div class="service-info-card__header">
                    <div class="service-info-card__header-left">
                        <h5 class="service-info-card__title">
                            <i class="fas fa-user-cog"></i> Técnico Externo
                        </h5>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <h5 class="mb-2">
                        <a href="<?= url('tecnicos-externos/view/' . $orden['IdTecnicoExterno']) ?>" class="btn--link">
                            <?= htmlspecialchars($orden['tecnico_nombre']) ?>
                        </a>
                    </h5>

                    <div class="additional-info">
                        <div class="additional-info__item">
                            <span class="additional-info__label">Taller</span>
                            <span class="additional-info__value"><?= htmlspecialchars($orden['tecnico_taller'] ?: 'N/A') ?></span>
                        </div>
                        <div class="additional-info__item">
                            <span class="additional-info__label">Documento</span>
                            <span class="additional-info__value"><?= htmlspecialchars($orden['tecnico_documento'] ?: 'N/A') ?></span>
                        </div>
                        <div class="additional-info__item">
                            <span class="additional-info__label">Teléfono</span>
                            <span class="additional-info__value">
                                <?php if (!empty($orden['tecnico_telefono'])): ?>
                                    <a href="tel:<?= htmlspecialchars($orden['tecnico_telefono']) ?>" class="btn--link">
                                        <?= htmlspecialchars($orden['tecnico_telefono']) ?>
                                    </a>
                                <?php else: ?>N/A<?php endif; ?>
                            </span>
                        </div>
                        <div class="additional-info__item">
                            <span class="additional-info__label">Correo</span>
                            <span class="additional-info__value">
                                <?php if (!empty($orden['tecnico_correo'])): ?>
                                    <a href="mailto:<?= htmlspecialchars($orden['tecnico_correo']) ?>" class="btn--link">
                                        <?= htmlspecialchars($orden['tecnico_correo']) ?>
                                    </a>
                                <?php else: ?>N/A<?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <div class="quick-actions-compact mt-3">
                        <a href="<?= url('tecnicos-externos/view/' . $orden['IdTecnicoExterno']) ?>"
                           class="btn btn--outline quick-actions__btn">
                            <i class="fas fa-external-link-alt btn__icon"></i>Ver ficha del técnico
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function marcarRecibidaDetalle(id) {
    Swal.fire({
        title: 'Registrar retorno',
        icon: 'question',
        input: 'select',
        inputOptions: {
            <?php foreach ($responsables as $responsable): ?>
            '<?= htmlspecialchars($responsable['id']) ?>': '<?= htmlspecialchars(addslashes($responsable['nombre'])) ?>',
            <?php endforeach; ?>
        },
        inputPlaceholder: '¿Quién recibe el producto?',
        inputValue: '<?= htmlspecialchars($_SESSION['usuario_id'] ?? '') ?>',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        inputValidator: function (value) {
            if (!value) { return 'Debes indicar quién recibe el producto.'; }
        }
    }).then(function (result) {
        if (!result.isConfirmed) { return; }

        fetch('<?= url('ordenes-externas/recibir/') ?>' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ QuienRecibe: result.value })
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
