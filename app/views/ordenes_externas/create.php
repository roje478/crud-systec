<?php
$orden = $orden ?? [];
$esEdicion = false;
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
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Nueva Orden Externa</h3>
                                <p class="form-intro__description">Registra la entrega de un producto a un técnico externo</p>
                            </div>
                            <div class="service-detail__header-actions">
                                <a href="<?= url('ordenes-externas') ?>" class="btn btn--outline">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </a>
                                <button type="submit" name="guardar_y_nuevo" value="1"
                                        class="btn btn--outline" form="formOrdenExterna">
                                    <i class="fas fa-plus btn__icon"></i>Guardar y Crear Otra
                                </button>
                                <button type="submit" class="btn btn--primary" form="formOrdenExterna">
                                    <i class="fas fa-save btn__icon"></i>Guardar Orden
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="formOrdenExterna" class="service-info-grid"
                          action="<?= url('ordenes-externas/store') ?>" method="POST">
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
                            <i class="fas fa-info-circle"></i> Información Importante
                        </h5>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="alert alert--warning">
                        <h6 class="alert__heading">
                            <i class="fas fa-star"></i> Datos obligatorios
                        </h6>
                        <ul class="mb-0">
                            <li><strong>Técnico externo</strong></li>
                            <li><strong>Cód. orden</strong> (único)</li>
                            <li><strong>Detalle del producto</strong></li>
                            <li>Fecha, motivo y quién entrega</li>
                        </ul>
                    </div>

                    <div class="alert alert--info mb-0">
                        <h6 class="alert__heading">
                            <i class="fas fa-route"></i> Cómo funciona
                        </h6>
                        <p class="mb-0">
                            La orden queda en estado <strong>Entregado</strong> hasta que registres
                            quién recibe el producto de vuelta. Puedes cerrarla después desde el
                            listado con el botón de confirmación.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/_modal_tecnico.php'; ?>
