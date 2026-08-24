<?php
$colores = $colores ?? [];
$siguienteOrden = $siguienteOrden ?? 1;
?>

<div class="service-detail">
    <div class="service-detail__content">
        <div class="service-detail__main">
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Nuevo Motivo</h3>
                                <p class="form-intro__description">Motivo de entrega a técnicos externos</p>
                            </div>
                            <div class="service-detail__header-actions">
                                <a href="<?= url('configuracion/motivos-externos') ?>" class="btn btn--outline">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn--primary" form="formMotivo">
                                    <i class="fas fa-save btn__icon"></i>Crear Motivo
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="formMotivo" class="service-info-grid">
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label" for="descripcion">
                                Descripción <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-tag service-info__icon"></i>
                                <input type="text" class="form__control" id="descripcion" name="descripcion"
                                       required maxlength="100"
                                       placeholder="Ej: Reparación, Garantía, Revisión">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-descripcion"></div>
                        </div>

                        <div class="service-info__field">
                            <label class="service-info__label" for="color">Color de la etiqueta</label>
                            <div class="service-info__input">
                                <i class="fas fa-palette service-info__icon"></i>
                                <select class="form__control" id="color" name="color">
                                    <?php foreach ($colores as $valor => $nombre): ?>
                                        <option value="<?= $valor ?>" <?= $valor === 'secondary' ? 'selected' : '' ?>>
                                            <?= $nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="service-info__field">
                            <label class="service-info__label" for="orden">Orden</label>
                            <div class="service-info__input">
                                <i class="fas fa-sort-numeric-up service-info__icon"></i>
                                <input type="number" class="form__control" id="orden" name="orden"
                                       value="<?= (int)$siguienteOrden ?>" min="0">
                            </div>
                            <div class="form__text">Posición en el formulario de órdenes.</div>
                        </div>

                        <div class="service-info__field">
                            <label class="service-info__label" for="activo">Estado</label>
                            <div class="service-info__input">
                                <i class="fas fa-power-off service-info__icon"></i>
                                <select class="form__control" id="activo" name="activo">
                                    <option value="1" selected>Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="service-info__field service-info__field--full-width">
                            <span class="service-info__label">Vista previa</span>
                            <div class="service-info__input">
                                <i class="fas fa-eye service-info__icon"></i>
                                <span class="status-badge status-badge--secondary" id="previewBadge">Nuevo motivo</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="service-detail__sidebar">
            <div class="service-info-card service-info-card--info">
                <div class="service-info-card__header">
                    <div class="service-info-card__header-left">
                        <h5 class="service-info-card__title">
                            <i class="fas fa-lightbulb"></i> Ejemplos
                        </h5>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="alert alert--info">
                        <ul class="mb-0">
                            <li>Reparación</li>
                            <li>Garantía</li>
                            <li>Revisión</li>
                            <li>Mantenimiento</li>
                            <li>Diagnóstico</li>
                        </ul>
                    </div>
                    <div class="alert alert--warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        Solo los motivos <strong>activos</strong> aparecen al crear una orden externa.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var descripcion = document.getElementById('descripcion');
    var color = document.getElementById('color');
    var preview = document.getElementById('previewBadge');

    var mapaColor = {
        primary: 'status-badge--info',
        success: 'status-badge--success',
        warning: 'status-badge--warning',
        danger: 'status-badge--danger',
        info: 'status-badge--info',
        secondary: 'status-badge--secondary',
        dark: 'status-badge--secondary'
    };

    function actualizarPreview() {
        preview.className = 'status-badge ' + (mapaColor[color.value] || 'status-badge--secondary');
        preview.textContent = descripcion.value.trim() || 'Nuevo motivo';
    }

    descripcion.addEventListener('input', actualizarPreview);
    color.addEventListener('change', actualizarPreview);

    document.getElementById('formMotivo').addEventListener('submit', function (e) {
        e.preventDefault();

        var payload = {
            descripcion: descripcion.value.trim(),
            color: color.value,
            orden: document.getElementById('orden').value,
            activo: document.getElementById('activo').value
        };

        fetch('<?= url('configuracion/create-motivo-externo') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                Swal.fire({ icon: 'success', title: data.message, timer: 1200, showConfirmButton: false })
                    .then(function () {
                        window.location.href = '<?= url('configuracion/motivos-externos') ?>';
                    });
            } else {
                Swal.fire('No se pudo guardar', data.message || 'Revisa los datos.', 'error');
            }
        })
        .catch(function () {
            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
        });
    });
})();
</script>
