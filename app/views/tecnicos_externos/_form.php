<?php
/**
 * Campos compartidos por los formularios de crear y editar técnico externo.
 * Espera: $tecnico (array o vacío), $esEdicion (bool)
 */
$tecnico = $tecnico ?? [];
$esEdicion = $esEdicion ?? false;

$valor = function ($campo) use ($tecnico) {
    return htmlspecialchars($tecnico[$campo] ?? '');
};
?>

<!-- Nombre -->
<div class="service-info__field service-info__field--two-columns">
    <label class="service-info__label" for="nombre">
        Nombre del técnico <span class="form__required">*</span>
    </label>
    <div class="service-info__input">
        <i class="fas fa-user service-info__icon"></i>
        <input type="text" class="form__control" id="nombre" name="nombre"
               value="<?= $valor('nombre') ?>" required maxlength="150"
               placeholder="Ej: Juan Pérez">
    </div>
    <div class="form__feedback form__feedback--invalid" id="error-nombre"></div>
    <div class="form__text">Persona responsable del taller externo.</div>
</div>

<!-- Documento -->
<div class="service-info__field">
    <label class="service-info__label" for="documento">Documento / NIT</label>
    <div class="service-info__input">
        <i class="fas fa-id-card service-info__icon"></i>
        <input type="text" class="form__control" id="documento" name="documento"
               value="<?= $valor('documento') ?>" maxlength="30"
               placeholder="Ej: 1098765432">
    </div>
    <div class="form__text">Opcional, pero no puede repetirse.</div>
</div>

<!-- Taller -->
<div class="service-info__field">
    <label class="service-info__label" for="taller">Taller / Empresa</label>
    <div class="service-info__input">
        <i class="fas fa-store service-info__icon"></i>
        <input type="text" class="form__control" id="taller" name="taller"
               value="<?= $valor('taller') ?>" maxlength="150"
               placeholder="Ej: Servitec Refrigeración">
    </div>
</div>

<!-- Especialidad -->
<div class="service-info__field service-info__field--two-columns">
    <label class="service-info__label" for="especialidad">Especialidad</label>
    <div class="service-info__input">
        <i class="fas fa-tools service-info__icon"></i>
        <input type="text" class="form__control" id="especialidad" name="especialidad"
               value="<?= $valor('especialidad') ?>" maxlength="150"
               placeholder="Ej: Tarjetas electrónicas, refrigeración">
    </div>
</div>

<!-- Teléfono -->
<div class="service-info__field">
    <label class="service-info__label" for="telefono">Teléfono</label>
    <div class="service-info__input">
        <i class="fas fa-phone service-info__icon"></i>
        <input type="text" class="form__control" id="telefono" name="telefono"
               value="<?= $valor('telefono') ?>" maxlength="50"
               placeholder="Ej: 3001234567">
    </div>
</div>

<!-- Correo -->
<div class="service-info__field service-info__field--two-columns">
    <label class="service-info__label" for="correo">Correo electrónico</label>
    <div class="service-info__input">
        <i class="fas fa-envelope service-info__icon"></i>
        <input type="email" class="form__control" id="correo" name="correo"
               value="<?= $valor('correo') ?>" maxlength="120"
               placeholder="Ej: taller@correo.com">
    </div>
    <div class="form__feedback form__feedback--invalid" id="error-correo"></div>
</div>

<!-- Dirección -->
<div class="service-info__field service-info__field--full-width">
    <label class="service-info__label" for="direccion">Dirección</label>
    <div class="service-info__input">
        <i class="fas fa-map-marker-alt service-info__icon"></i>
        <input type="text" class="form__control" id="direccion" name="direccion"
               value="<?= $valor('direccion') ?>" maxlength="200"
               placeholder="Ej: Calle 10 # 5-32">
    </div>
</div>

<!-- Observaciones -->
<div class="service-info__field service-info__field--full-width">
    <label class="service-info__label" for="observaciones">Observaciones</label>
    <div class="service-info__input service-info__input--textarea">
        <i class="fas fa-sticky-note service-info__icon"></i>
        <textarea class="form__control" id="observaciones" name="observaciones" rows="3"
                  maxlength="500" placeholder="Notas internas sobre este técnico..."><?= $valor('observaciones') ?></textarea>
    </div>
</div>

<?php if ($esEdicion): ?>
<!-- Estado -->
<div class="service-info__field service-info__field--full-width">
    <label class="service-info__label" for="activo">Estado del técnico</label>
    <div class="service-info__input">
        <i class="fas fa-power-off service-info__icon"></i>
        <select class="form__control" id="activo" name="activo">
            <option value="1" <?= (int)($tecnico['activo'] ?? 1) === 1 ? 'selected' : '' ?>>Activo</option>
            <option value="0" <?= (int)($tecnico['activo'] ?? 1) === 0 ? 'selected' : '' ?>>Inactivo</option>
        </select>
    </div>
    <div class="form__text">Los inactivos no aparecen al crear órdenes nuevas.</div>
</div>
<?php endif; ?>
