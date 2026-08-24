<?php
$tecnico = $tecnico ?? [];
$ordenes = $ordenes ?? [];
$estadosOrden = OrdenExterna::getEstados();

$claseEstadoOrden = [
    'entregado' => 'status-badge--warning',
    'recibido'  => 'status-badge--success',
    'anulado'   => 'status-badge--danger'
];
?>

<div class="service-detail">
    <!-- Encabezado -->
    <div class="service-detail__header">
        <div class="service-detail__header-left">
            <div class="service-detail__title-section">
                <h1 class="service-detail__title"><?= htmlspecialchars($tecnico['nombre'] ?? '') ?></h1>
                <?php if ((int)($tecnico['activo'] ?? 0) === 1): ?>
                    <span class="status-badge status-badge--success">Activo</span>
                <?php else: ?>
                    <span class="status-badge status-badge--secondary">Inactivo</span>
                <?php endif; ?>
            </div>
            <div class="service-detail__meta">
                <span class="service-detail__meta-item">
                    <i class="fas fa-store"></i>
                    <?= htmlspecialchars($tecnico['taller'] ?: 'Sin taller registrado') ?>
                </span>
                <span class="service-detail__meta-item">
                    <i class="fas fa-clipboard-list"></i>
                    <?= (int)($tecnico['total_ordenes'] ?? 0) ?> órdenes
                </span>
                <span class="service-detail__meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    Desde <?= !empty($tecnico['fecha_registro']) ? date('d/m/Y', strtotime($tecnico['fecha_registro'])) : 'N/A' ?>
                </span>
            </div>
        </div>
        <div class="service-detail__header-actions">
            <a href="<?= url('tecnicos-externos') ?>" class="btn btn--outline">
                <i class="fas fa-arrow-left btn__icon"></i>Volver
            </a>
            <a href="<?= url('tecnicos-externos/edit/' . $tecnico['id']) ?>" class="btn btn--primary">
                <i class="fas fa-edit btn__icon"></i>Editar
            </a>
        </div>
    </div>

    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Datos de contacto -->
            <div class="service-info-card">
                <div class="service-info-card__header">
                    <div class="service-info-card__header-left">
                        <div class="form-intro__icon">
                            <i class="fas fa-address-card"></i>
                        </div>
                        <h5 class="service-info-card__title">Datos de Contacto</h5>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="service-info-grid">
                        <div class="service-info__field">
                            <span class="service-info__label">Documento</span>
                            <div class="service-info__input">
                                <i class="fas fa-id-card service-info__icon"></i>
                                <?= htmlspecialchars($tecnico['documento'] ?: 'N/A') ?>
                            </div>
                        </div>
                        <div class="service-info__field">
                            <span class="service-info__label">Teléfono</span>
                            <div class="service-info__input">
                                <i class="fas fa-phone service-info__icon"></i>
                                <?php if (!empty($tecnico['telefono'])): ?>
                                    <a href="tel:<?= htmlspecialchars($tecnico['telefono']) ?>" class="btn--link">
                                        <?= htmlspecialchars($tecnico['telefono']) ?>
                                    </a>
                                <?php else: ?>N/A<?php endif; ?>
                            </div>
                        </div>
                        <div class="service-info__field">
                            <span class="service-info__label">Correo</span>
                            <div class="service-info__input">
                                <i class="fas fa-envelope service-info__icon"></i>
                                <?php if (!empty($tecnico['correo'])): ?>
                                    <a href="mailto:<?= htmlspecialchars($tecnico['correo']) ?>" class="btn--link">
                                        <?= htmlspecialchars($tecnico['correo']) ?>
                                    </a>
                                <?php else: ?>N/A<?php endif; ?>
                            </div>
                        </div>
                        <div class="service-info__field">
                            <span class="service-info__label">Especialidad</span>
                            <div class="service-info__input">
                                <i class="fas fa-tools service-info__icon"></i>
                                <?= htmlspecialchars($tecnico['especialidad'] ?: 'N/A') ?>
                            </div>
                        </div>
                        <div class="service-info__field service-info__field--two-columns">
                            <span class="service-info__label">Dirección</span>
                            <div class="service-info__input">
                                <i class="fas fa-map-marker-alt service-info__icon"></i>
                                <?= htmlspecialchars($tecnico['direccion'] ?: 'N/A') ?>
                            </div>
                        </div>
                        <?php if (!empty($tecnico['observaciones'])): ?>
                        <div class="service-info__field service-info__field--full-width">
                            <span class="service-info__label">Observaciones</span>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-sticky-note service-info__icon"></i>
                                <span><?= nl2br(htmlspecialchars($tecnico['observaciones'])) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Historial de órdenes -->
            <div class="service-content-card">
                <div class="service-content-card__header">
                    <h5 class="service-content-card__title">
                        <i class="fas fa-clipboard-list"></i> Historial de Órdenes
                    </h5>
                </div>
                <div class="service-content-card__body" style="padding: 0;">
                    <?php if (empty($ordenes)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">Este técnico aún no tiene órdenes registradas.</p>
                        </div>
                    <?php else: ?>
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cód. Orden</th>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Motivo</th>
                                    <th class="text-right">Precio</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ordenes as $orden): ?>
                                <tr>
                                    <td>
                                        <a href="<?= url('ordenes-externas/view/' . $orden['IdOrden']) ?>" class="btn--link">
                                            <strong><?= htmlspecialchars($orden['CodOrden']) ?></strong>
                                        </a>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($orden['Fecha'])) ?></td>
                                    <td title="<?= htmlspecialchars($orden['DetalleProducto']) ?>">
                                        <?= htmlspecialchars(mb_strimwidth($orden['DetalleProducto'], 0, 45, '...')) ?>
                                    </td>
                                    <td><?= htmlspecialchars($orden['motivo_descripcion']) ?></td>
                                    <td class="text-right">$<?= number_format((float)$orden['Precio'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="status-badge <?= $claseEstadoOrden[$orden['Estado']] ?? 'status-badge--secondary' ?>">
                                            <?= $estadosOrden[$orden['Estado']]['label'] ?? $orden['Estado'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="service-detail__sidebar">
            <div class="service-info-card service-info-card--info">
                <div class="service-info-card__header">
                    <div class="service-info-card__header-left">
                        <h5 class="service-info-card__title">
                            <i class="fas fa-chart-bar"></i> Resumen
                        </h5>
                    </div>
                    <div class="service-info-card__cost">
                        $<?= number_format((float)($tecnico['monto_total'] ?? 0), 0, ',', '.') ?>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="additional-info">
                        <div class="additional-info__item">
                            <span class="additional-info__label">Órdenes totales</span>
                            <span class="additional-info__value"><?= (int)($tecnico['total_ordenes'] ?? 0) ?></span>
                        </div>
                        <div class="additional-info__item">
                            <span class="additional-info__label">Pendientes de retorno</span>
                            <span class="additional-info__value"><?= (int)($tecnico['ordenes_pendientes'] ?? 0) ?></span>
                        </div>
                        <div class="additional-info__item">
                            <span class="additional-info__label">Recibidas</span>
                            <span class="additional-info__value"><?= (int)($tecnico['ordenes_recibidas'] ?? 0) ?></span>
                        </div>
                    </div>

                    <div class="quick-actions-compact mt-3">
                        <a href="<?= url('ordenes-externas') . '&tecnico=' . (int)$tecnico['id'] ?>"
                           class="btn btn--outline quick-actions__btn">
                            <i class="fas fa-list btn__icon"></i>Ver todas sus órdenes
                        </a>
                        <a href="<?= url('ordenes-externas/create') ?>" class="btn btn--primary quick-actions__btn">
                            <i class="fas fa-plus btn__icon"></i>Nueva orden
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
