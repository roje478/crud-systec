<?php
/**
 * Campos compartidos por los formularios de crear y editar orden externa.
 * Espera: $orden, $tecnicos, $motivos, $responsables, $esEdicion
 */
$orden = $orden ?? [];
$tecnicos = $tecnicos ?? [];
$motivos = $motivos ?? [];
$responsables = $responsables ?? [];
$esEdicion = $esEdicion ?? false;

$valor = function ($campo, $defecto = '') use ($orden) {
    return htmlspecialchars((string)($orden[$campo] ?? $defecto));
};
$seleccionado = function ($campo, $opcion) use ($orden) {
    return (string)($orden[$campo] ?? '') === (string)$opcion ? 'selected' : '';
};
?>

<!-- Cód. Orden (lo asigna el sistema, no se edita) -->
<div class="service-info__field">
    <span class="service-info__label">Cód. Orden</span>
    <div class="service-info__input">
        <i class="fas fa-hashtag service-info__icon"></i>
        <strong id="CodOrden"><?= $valor('CodOrden') ?></strong>
        <i class="fas fa-lock service-info__icon" style="margin-left: auto;"
           title="Lo asigna el sistema"></i>
    </div>
    <div class="form__text">
        <?= $esEdicion ? 'El código no se puede modificar.' : 'Se asigna automáticamente al guardar.' ?>
    </div>
</div>

<!-- Fecha -->
<div class="service-info__field">
    <label class="service-info__label" for="Fecha">
        Fecha de entrega <span class="form__required">*</span>
    </label>
    <div class="service-info__input">
        <i class="fas fa-calendar-day service-info__icon"></i>
        <input type="date" class="form__control" id="Fecha" name="Fecha"
               value="<?= $valor('Fecha', date('Y-m-d')) ?>" required>
    </div>
</div>

<!-- Técnico externo -->
<div class="service-info__field">
    <label class="service-info__label" for="IdTecnicoExterno">
        Técnico externo <span class="form__required">*</span>
    </label>
    <div class="service-info__input">
        <i class="fas fa-user-cog service-info__icon"></i>
        <select class="form__control" id="IdTecnicoExterno" name="IdTecnicoExterno" required>
            <option value="">Seleccionar técnico...</option>
            <?php foreach ($tecnicos as $tecnico): ?>
                <option value="<?= (int)$tecnico['id'] ?>" <?= $seleccionado('IdTecnicoExterno', $tecnico['id']) ?>>
                    <?= htmlspecialchars($tecnico['nombre']) ?><?= !empty($tecnico['taller']) ? ' — ' . htmlspecialchars($tecnico['taller']) : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="btn btn--outline btn--sm" id="btnAbrirModalTecnico"
                title="Registrar un técnico nuevo">
            <i class="fas fa-plus"></i>
        </button>
    </div>
    <div class="form__feedback form__feedback--invalid" id="error-IdTecnicoExterno"></div>
    <div class="form__text">Solo aparecen los técnicos activos.</div>
</div>

<!-- Detalle del producto -->
<div class="service-info__field service-info__field--full-width">
    <label class="service-info__label" for="DetalleProducto">
        Detalle del producto <span class="form__required">*</span>
    </label>
    <div class="service-info__input service-info__input--textarea">
        <i class="fas fa-box service-info__icon"></i>
        <textarea class="form__control" id="DetalleProducto" name="DetalleProducto" rows="4" required
                  maxlength="500"
                  placeholder="Marca, modelo, serial, accesorios entregados, estado físico..."><?= $valor('DetalleProducto') ?></textarea>
    </div>
    <div class="form__feedback form__feedback--invalid" id="error-DetalleProducto"></div>
    <div class="form__text">Describe el equipo con el mayor detalle posible. <span id="contadorDetalle">0</span>/500</div>
</div>

<!-- Motivo -->
<div class="service-info__field service-info__field--span-3">
    <label class="service-info__label">
        Motivo <span class="form__required">*</span>
    </label>
    <?php if (empty($motivos)): ?>
        <div class="alert alert--warning mb-0">
            No hay motivos activos.
            <a href="<?= url('configuracion/motivos-externos') ?>">Configúralos aquí</a>.
        </div>
    <?php else: ?>
        <div class="service-info__input" style="gap: 1.5rem; flex-wrap: wrap;">
            <i class="fas fa-tags service-info__icon"></i>
            <?php foreach ($motivos as $motivo): ?>
                <label style="display: inline-flex; align-items: center; gap: 0.4rem; margin: 0; cursor: pointer;">
                    <input type="radio" name="IdMotivo" value="<?= (int)$motivo['id'] ?>" required
                           <?= (string)($orden['IdMotivo'] ?? '') === (string)$motivo['id'] ? 'checked' : '' ?>>
                    <span class="status-badge status-badge--<?= $motivo['color'] === 'primary' ? 'info' : ($motivo['color'] === 'warning' ? 'warning' : ($motivo['color'] === 'success' ? 'success' : ($motivo['color'] === 'danger' ? 'danger' : 'secondary'))) ?>">
                        <?= htmlspecialchars($motivo['descripcion']) ?>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Quién entrega -->
<div class="service-info__field">
    <label class="service-info__label" for="QuienEntrega">
        Quién entrega <span class="form__required">*</span>
    </label>
    <div class="service-info__input">
        <i class="fas fa-hand-holding service-info__icon"></i>
        <select class="form__control" id="QuienEntrega" name="QuienEntrega" required>
            <option value="">Seleccionar...</option>
            <?php foreach ($responsables as $responsable): ?>
                <option value="<?= htmlspecialchars($responsable['id']) ?>" <?= $seleccionado('QuienEntrega', $responsable['id']) ?>>
                    <?= htmlspecialchars($responsable['nombre']) ?> (<?= htmlspecialchars($responsable['perfil']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form__text">Usuario o técnico interno que entrega.</div>
</div>

<!-- Quién recibe -->
<div class="service-info__field">
    <label class="service-info__label" for="QuienRecibe">Quién recibe o recoge</label>
    <div class="service-info__input">
        <i class="fas fa-hands-helping service-info__icon"></i>
        <select class="form__control" id="QuienRecibe" name="QuienRecibe">
            <option value="">Pendiente de retorno</option>
            <?php foreach ($responsables as $responsable): ?>
                <option value="<?= htmlspecialchars($responsable['id']) ?>" <?= $seleccionado('QuienRecibe', $responsable['id']) ?>>
                    <?= htmlspecialchars($responsable['nombre']) ?> (<?= htmlspecialchars($responsable['perfil']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form__text">Al indicarlo, la orden pasa a <strong>Recibido</strong>.</div>
</div>

<!-- Fecha de recibido -->
<div class="service-info__field">
    <label class="service-info__label" for="FechaRecibe">Fecha de recibido</label>
    <div class="service-info__input">
        <i class="fas fa-calendar-check service-info__icon"></i>
        <input type="date" class="form__control" id="FechaRecibe" name="FechaRecibe"
               value="<?= $valor('FechaRecibe') ?>">
    </div>
</div>

<!-- Precio -->
<div class="service-info__field">
    <label class="service-info__label" for="Precio">Precio</label>
    <div class="service-info__input">
        <i class="fas fa-dollar-sign service-info__icon"></i>
        <input type="number" class="form__control" id="Precio" name="Precio"
               value="<?= $valor('Precio', '0') ?>" min="0" step="1">
    </div>
    <div class="form__text">Valor cobrado por el técnico externo.</div>
</div>

<!-- Servicio relacionado -->
<div class="service-info__field">
    <label class="service-info__label" for="IdServicio">Servicio relacionado</label>
    <div class="service-info__input">
        <i class="fas fa-link service-info__icon"></i>
        <input type="number" class="form__control" id="IdServicio" name="IdServicio"
               value="<?= $valor('IdServicio') ?>" min="1" placeholder="N.º de servicio interno">
    </div>
    <div class="form__text">Opcional.</div>
</div>

<!-- Observaciones -->
<div class="service-info__field service-info__field--full-width">
    <label class="service-info__label" for="Observaciones">Observaciones</label>
    <div class="service-info__input service-info__input--textarea">
        <i class="fas fa-comment-dots service-info__icon"></i>
        <textarea class="form__control" id="Observaciones" name="Observaciones" rows="3" maxlength="1000"
                  placeholder="Acuerdos, tiempos de entrega, condiciones..."><?= $valor('Observaciones') ?></textarea>
    </div>
</div>
