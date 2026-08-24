<?php
$tecnico = [];
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
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Nuevo Técnico Externo</h3>
                                <p class="form-intro__description">Registra un taller o técnico de terceros</p>
                            </div>
                            <div class="service-detail__header-actions">
                                <a href="<?= url('tecnicos-externos') ?>" class="btn btn--outline">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn--primary" form="formTecnico">
                                    <i class="fas fa-save btn__icon"></i>Guardar Técnico
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="formTecnico" class="service-info-grid"
                          action="<?= url('tecnicos-externos/store') ?>" method="POST">
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
                            <i class="fas fa-info-circle"></i> Ten en cuenta
                        </h5>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="alert alert--info">
                        <ul class="mb-0">
                            <li>El <strong>nombre</strong> es obligatorio y no puede repetirse.</li>
                            <li>El <strong>documento</strong> es opcional, pero si lo registras debe ser único.</li>
                            <li>Solo los técnicos <strong>activos</strong> aparecen al crear una orden externa.</li>
                        </ul>
                    </div>
                    <div class="alert alert--warning mb-0">
                        <i class="fas fa-lock"></i>
                        Un técnico con órdenes registradas no se puede eliminar: se desactiva.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
