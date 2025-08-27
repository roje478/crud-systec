<?php
// Establecer breadcrumb
$breadcrumb = [
    ['text' => 'Servicios', 'url' => $this->url('servicios')],
    ['text' => 'Crear Nuevo']
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="h3 mb-0">
            <i class="fas fa-plus-circle me-2 text-primary"></i>
            Crear Nuevo Servicio
        </h1>
        <p class="text-muted mb-0">Registra un nuevo servicio técnico en el sistema</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="<?= $this->url('servicios') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
        </a>
    </div>
</div>

<!-- Formulario de creación -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>Información del Servicio
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= $this->url('servicios/create') ?>" id="createServiceForm">

                    <!-- Información del Cliente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-user me-2"></i>Información del Cliente
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label for="NoIdentificacionCliente" class="form-label">
                                Identificación del Cliente <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control <?= isset($errors['NoIdentificacionCliente']) ? 'is-invalid' : '' ?>"
                                   id="NoIdentificacionCliente"
                                   name="NoIdentificacionCliente"
                                   value="<?= htmlspecialchars($oldData['NoIdentificacionCliente'] ?? '') ?>"
                                   placeholder="Número de identificación del cliente"
                                   required>
                            <?php if (isset($errors['NoIdentificacionCliente'])): ?>
                                <div class="invalid-feedback">
                                    <?= implode('<br>', $errors['NoIdentificacionCliente']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="IdTipoServicio" class="form-label">
                                Tipo de Servicio <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= isset($errors['IdTipoServicio']) ? 'is-invalid' : '' ?>"
                                    id="IdTipoServicio"
                                    name="IdTipoServicio"
                                    required>
                                <option value="">Seleccionar tipo de servicio...</option>
                                <?php if (isset($tiposServicio)): ?>
                                    <?php foreach ($tiposServicio as $tipo): ?>
                                        <option value="<?= $tipo['IdTipoServicio'] ?>"
                                                <?= (isset($oldData['IdTipoServicio']) && $oldData['IdTipoServicio'] == $tipo['IdTipoServicio']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tipo['TipoServicio']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (isset($errors['IdTipoServicio'])): ?>
                                <div class="invalid-feedback">
                                    <?= implode('<br>', $errors['IdTipoServicio']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Información del Equipo -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-laptop me-2"></i>Información del Equipo
                            </h6>
                        </div>
                        <div class="col-12">
                            <label for="Equipo" class="form-label">
                                Descripción del Equipo <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control <?= isset($errors['Equipo']) ? 'is-invalid' : '' ?>"
                                      id="Equipo"
                                      name="Equipo"
                                      rows="3"
                                      placeholder="Describe detalladamente el equipo (marca, modelo, características, etc.)"
                                      required><?= htmlspecialchars($oldData['Equipo'] ?? '') ?></textarea>
                            <?php if (isset($errors['Equipo'])): ?>
                                <div class="invalid-feedback">
                                    <?= implode('<br>', $errors['Equipo']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Mínimo 5 caracteres. Incluye marca, modelo y características relevantes.</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="CondicionesEntrega" class="form-label">
                                Condiciones de Entrega
                            </label>
                            <textarea class="form-control <?= isset($errors['CondicionesEntrega']) ? 'is-invalid' : '' ?>"
                                      id="CondicionesEntrega"
                                      name="CondicionesEntrega"
                                      rows="2"
                                      placeholder="Estado físico del equipo, accesorios incluidos, etc."><?= htmlspecialchars($oldData['CondicionesEntrega'] ?? '') ?></textarea>
                            <?php if (isset($errors['CondicionesEntrega'])): ?>
                                <div class="invalid-feedback">
                                    <?= implode('<br>', $errors['CondicionesEntrega']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Información del Problema -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Descripción del Problema
                            </h6>
                        </div>
                        <div class="col-12">
                            <label for="Problema" class="form-label">
                                Problema Reportado <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control <?= isset($errors['Problema']) ? 'is-invalid' : '' ?>"
                                      id="Problema"
                                      name="Problema"
                                      rows="4"
                                      placeholder="Describe detalladamente el problema que presenta el equipo..."
                                      required><?= htmlspecialchars($oldData['Problema'] ?? '') ?></textarea>
                            <?php if (isset($errors['Problema'])): ?>
                                <div class="invalid-feedback">
                                    <?= implode('<br>', $errors['Problema']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Mínimo 10 caracteres. Sé específico sobre los síntomas y problemas observados.</div>
                        </div>
                    </div>

                    <!-- Estado y Costo -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-cogs me-2"></i>Estado y Costo
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label for="IdEstadoEnTaller" class="form-label">
                                Estado Inicial <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= isset($errors['IdEstadoEnTaller']) ? 'is-invalid' : '' ?>"
                                    id="IdEstadoEnTaller"
                                    name="IdEstadoEnTaller"
                                    required>
                                <option value="">Seleccionar estado...</option>
                                <?php if (isset($estados)): ?>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['IdEstadoEnTaller'] ?>"
                                                <?= (isset($oldData['IdEstadoEnTaller']) && $oldData['IdEstadoEnTaller'] == $estado['IdEstadoEnTaller']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['Descripcion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (isset($errors['IdEstadoEnTaller'])): ?>
                                <div class="invalid-feedback">
                                    <?= implode('<br>', $errors['IdEstadoEnTaller']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="Costo" class="form-label">
                                Costo Estimado (Opcional)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control <?= isset($errors['Costo']) ? 'is-invalid' : '' ?>"
                                       id="Costo"
                                       name="Costo"
                                       min="0"
                                       step="0.01"
                                       value="<?= htmlspecialchars($oldData['Costo'] ?? '') ?>"
                                       placeholder="0.00">
                            </div>
                            <?php if (isset($errors['Costo'])): ?>
                                <div class="invalid-feedback">
                                    <?= implode('<br>', $errors['Costo']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Puedes actualizar el costo después si no lo conoces ahora.</div>
                        </div>
                    </div>

                    <!-- Notas Internas -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="NotaInterna" class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>Notas Internas
                            </label>
                            <textarea class="form-control <?= isset($errors['NotaInterna']) ? 'is-invalid' : '' ?>"
                                      id="NotaInterna"
                                      name="NotaInterna"
                                      rows="3"
                                      placeholder="Notas internas para el equipo técnico (no visible para el cliente)..."><?= htmlspecialchars($oldData['NotaInterna'] ?? '') ?></textarea>
                            <?php if (isset($errors['NotaInterna'])): ?>
                                <div class="invalid-feedback">
                                    <?= implode('<br>', $errors['NotaInterna']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="row">
                        <div class="col-12">
                            <hr class="my-4">
                            <div class="d-flex justify-content-between">
                                <a href="<?= $this->url('servicios') ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>Crear Servicio
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Panel lateral con información -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Información Importante
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-lightbulb me-2"></i>Consejos
                    </h6>
                    <ul class="mb-0 small">
                        <li>Verifica que el cliente exista en el sistema antes de crear el servicio</li>
                        <li>Describe el equipo con el mayor detalle posible</li>
                        <li>El problema debe ser específico para facilitar el diagnóstico</li>
                        <li>La fecha de ingreso se asignará automáticamente</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6 class="alert-heading">
                        <i class="fas fa-exclamation-triangle me-2"></i>Recordatorio
                    </h6>
                    <p class="mb-0 small">
                        Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                        Asegúrate de completarlos correctamente.
                    </p>
                </div>

                <div class="alert alert-success">
                    <h6 class="alert-heading">
                        <i class="fas fa-check-circle me-2"></i>Después de Crear
                    </h6>
                    <p class="mb-0 small">
                        Una vez creado el servicio, podrás:
                    </p>
                    <ul class="mb-0 small">
                        <li>Actualizar el estado</li>
                        <li>Agregar la solución</li>
                        <li>Modificar el costo</li>
                        <li>Generar reportes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validación del formulario
    $('#createServiceForm').on('submit', function(e) {
        let isValid = true;

        // Limpiar errores previos
        $('.is-invalid').removeClass('is-invalid');

        // Validar campos requeridos
        $('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });

        // Validar longitud mínima de equipo
        const equipo = $('#Equipo').val().trim();
        if (equipo.length > 0 && equipo.length < 5) {
            $('#Equipo').addClass('is-invalid');
            isValid = false;
        }

        // Validar longitud mínima de problema
        const problema = $('#Problema').val().trim();
        if (problema.length > 0 && problema.length < 10) {
            $('#Problema').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            App.showError('Por favor corrige los errores en el formulario');
            return false;
        }

        // Mostrar loading
        App.showButtonLoading('#submitBtn', 'Creando...');
    });

    // Auto-resize textareas
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Formatear campo de costo
    $('#Costo').on('input', function() {
        let value = $(this).val();
        if (value && !isNaN(value)) {
            $(this).val(parseFloat(value).toFixed(2));
        }
    });
});
</script>