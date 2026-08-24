<?php
$tecnico = $tecnico ?? [];
$totalOrdenes = $totalOrdenes ?? 0;
$esEdicion = true;
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
                                <h3 class="form-intro__title">Editar Técnico Externo</h3>
                                <p class="form-intro__description"><?= htmlspecialchars($tecnico['nombre'] ?? '') ?></p>
                            </div>
                            <div class="service-detail__header-actions">
                                <a href="<?= url('tecnicos-externos/view/' . ($tecnico['id'] ?? '')) ?>" class="btn btn--outline">
                                    <i class="fas fa-external-link-alt btn__icon"></i>Ver Ficha
                                </a>
                                <a href="<?= url('tecnicos-externos') ?>" class="btn btn--outline">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn--primary" form="formTecnico">
                                    <i class="fas fa-save btn__icon"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="formTecnico" class="service-info-grid"
                          action="<?= url('tecnicos-externos/update/' . ($tecnico['id'] ?? '')) ?>" method="POST">
                        <?php include __DIR__ . '/_form.php'; ?>
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
                            <i class="fas fa-clipboard-list"></i> Actividad
                        </h5>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="additional-info">
                        <div class="additional-info__item">
                            <span class="additional-info__label">Órdenes registradas</span>
                            <span class="additional-info__value"><?= (int)$totalOrdenes ?></span>
                        </div>
                        <div class="additional-info__item">
                            <span class="additional-info__label">Registrado</span>
                            <span class="additional-info__value">
                                <?= !empty($tecnico['fecha_registro']) ? date('d/m/Y', strtotime($tecnico['fecha_registro'])) : 'N/A' ?>
                            </span>
                        </div>
                        <div class="additional-info__item">
                            <span class="additional-info__label">Actualizado</span>
                            <span class="additional-info__value">
                                <?= !empty($tecnico['fecha_actualizacion']) ? date('d/m/Y', strtotime($tecnico['fecha_actualizacion'])) : 'N/A' ?>
                            </span>
                        </div>
                    </div>

                    <?php if ((int)$totalOrdenes > 0): ?>
                        <div class="alert alert--warning mt-3 mb-0">
                            <i class="fas fa-lock"></i>
                            Este técnico no se puede eliminar porque tiene órdenes asociadas.
                            Cámbialo a <strong>Inactivo</strong> si ya no trabajas con él.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
