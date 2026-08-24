<?php
$motivo = $motivo ?? [];
$colores = $colores ?? [];
$totalOrdenes = $totalOrdenes ?? 0;

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

<div class="service-detail">
    <div class="service-detail__content">
        <div class="service-detail__main">
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Editar Motivo</h3>
                                <p class="form-intro__description"><?= htmlspecialchars($motivo['descripcion'] ?? '') ?></p>
                            </div>
                            <div class="service-detail__header-actions">
                                <a href="<?= url('configuracion/motivos-externos') ?>" class="btn btn--outline">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn--primary" form="formMotivo">
                                    <i class="fas fa-save btn__icon"></i>Guardar Cambios
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
                                       value="<?= htmlspecialchars($motivo['descripcion'] ?? '') ?>"
                                       required maxlength="100">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-descripcion"></div>
                        </div>

                        <div class="service-info__field">
                            <label class="service-info__label" for="color">Color de la etiqueta</label>
                            <div class="service-info__input">
                                <i class="fas fa-palette service-info__icon"></i>
                                <select class="form__control" id="color" name="color">
                                    <?php foreach ($colores as $valor => $nombre): ?>
                                        <option value="<?= $valor ?>" <?= ($motivo['color'] ?? '') === $valor ? 'selected' : '' ?>>
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
                                       value="<?= (int)($motivo['orden'] ?? 0) ?>" min="0">
                            </div>
                        </div>

                        <div class="service-info__field">
                            <label class="service-info__label" for="activo">Estado</label>
                            <div class="service-info__input">
                                <i class="fas fa-power-off service-info__icon"></i>
                                <select class="form__control" id="activo" name="activo">
                                    <option value="1" <?= (int)($motivo['activo'] ?? 0) === 1 ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= (int)($motivo['activo'] ?? 0) === 0 ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="service-info__field service-info__field--full-width">
                            <span class="service-info__label">Vista previa</span>
                            <div class="service-info__input">
                                <i class="fas fa-eye service-info__icon"></i>
                                <span class="status-badge <?= $claseBadge($motivo['color'] ?? 'secondary') ?>" id="previewBadge">
                                    <?= htmlspecialchars($motivo['descripcion'] ?? '') ?>
                                </span>
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
                            <i class="fas fa-chart-bar"></i> Uso
                        </h5>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="additional-info">
                        <div class="additional-info__item">
                            <span class="additional-info__label">Órdenes con este motivo</span>
                            <span class="additional-info__value"><?= (int)$totalOrdenes ?></span>
                        </div>
                    </div>

                    <?php if ((int)$totalOrdenes > 0): ?>
                        <div class="alert alert--warning mt-3 mb-0">
                            <i class="fas fa-lock"></i>
                            No se puede eliminar porque ya se usó en órdenes.
                            Cámbialo a <strong>Inactivo</strong> para que deje de aparecer en el formulario.
                        </div>
                    <?php endif; ?>
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
        preview.textContent = descripcion.value.trim() || 'Motivo';
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

        fetch('<?= url('configuracion/edit-motivo-externo/' . ($motivo['id'] ?? '')) ?>', {
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
