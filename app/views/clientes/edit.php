<?php

/**
 * Vista Editar Cliente
 */

// Verificar que las variables estén definidas
$cliente = $cliente ?? [];
$tiposIdentificacion = $tiposIdentificacion ?? [];
$generos = $generos ?? [];
?>

<div class="service-detail">

    <!-- Contenido principal -->
    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Formulario de información del cliente -->
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Editar Cliente</h3>
                                <p class="form-intro__description">Modifique la información del cliente según sea necesario</p>
                            </div>

                            <div class="form-intro__actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <button type="button" class="btn btn--outline" id="cancelBtn">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="editClienteForm">
                                    <i class="fas fa-save btn__icon"></i>Actualizar Cliente
                                </button>
                            </div>
                        </div>
                    </div>
                    <form id="editClienteForm" class="service-info-grid">
                        <!-- Número de Identificación -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="no_identificacion">
                                Número de Identificación <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-id-card service-info__icon"></i>
                                <input type="number" class="form__control" id="no_identificacion"
                                    name="no_identificacion" value="<?= htmlspecialchars($cliente['no_identificacion']) ?>" required>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-no_identificacion"></div>
                        </div>

                        <!-- Tipo de Identificación -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="tipo_id">
                                Tipo de Identificación <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-credit-card service-info__icon"></i>
                                <select class="form__control" id="tipo_id" name="tipo_id" required>
                                    <option value="">Seleccionar tipo</option>
                                    <?php foreach ($tiposIdentificacion as $tipo): ?>
                                        <option value="<?= $tipo['id'] ?>" <?= $tipo['id'] == $cliente['tipo_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tipo['descripcion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-tipo_id"></div>
                        </div>

                        <!-- Nombres -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="nombres">
                                Nombres <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <input type="text" class="form__control" id="nombres"
                                    name="nombres" value="<?= htmlspecialchars($cliente['nombres']) ?>" required>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-nombres"></div>
                        </div>

                        <!-- Apellidos -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="apellidos">
                                Apellidos <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <input type="text" class="form__control" id="apellidos"
                                    name="apellidos" value="<?= htmlspecialchars($cliente['apellidos']) ?>" required>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-apellidos"></div>
                        </div>

                        <!-- Género -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="genero">
                                Género
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-venus-mars service-info__icon"></i>
                                <select class="form__control" id="genero" name="genero">
                                    <option value="O" <?= $cliente['genero'] == 'O' ? 'selected' : '' ?>>No especificado</option>
                                    <?php foreach ($generos as $genero): ?>
                                        <option value="<?= $genero['id'] ?>" <?= $genero['id'] == $cliente['genero'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($genero['descripcion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="telefono">
                                Teléfono
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-phone service-info__icon"></i>
                                <input type="tel" class="form__control" id="telefono"
                                    name="telefono" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>"
                                    placeholder="Ej: 3001234567">
                            </div>
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="fecha_nacimiento">
                                Fecha de Nacimiento
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-calendar-alt service-info__icon"></i>
                                <input type="date" class="form__control" id="fecha_nacimiento"
                                    name="fecha_nacimiento" value="<?= $cliente['fecha_nacimiento'] ?? '' ?>">
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label" for="direccion">
                                Dirección
                            </label>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-map-marker-alt service-info__icon"></i>
                                <textarea class="form__control" id="direccion" name="direccion"
                                    rows="3" placeholder="Ingrese la dirección completa"><?= htmlspecialchars($cliente['direccion'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables para el formulario
    let originalFormData = {};
    let formChanged = false;

    // Función para verificar cambios en el formulario
    function checkFormChanges() {
        const form = document.getElementById('editClienteForm');
        const formData = new FormData(form);
        let hasChanges = false;

        for (let [key, value] of formData.entries()) {
            if (originalFormData[key] !== value) {
                hasChanges = true;
                break;
            }
        }

        formChanged = hasChanges;
        updateDiscardButton();
        return hasChanges;
    }

    // Función para actualizar el botón de descartar
    function updateDiscardButton() {
        const discardBtn = document.getElementById('discardBtn');
        if (discardBtn) {
            discardBtn.disabled = !formChanged;
        }
    }

    // Función para resetear formulario
    function resetForm() {
        if (confirm('¿Está seguro de que desea descartar todos los cambios?')) {
            // Restaurar valores originales
            Object.keys(originalFormData).forEach(key => {
                const element = document.getElementById(key);
                if (element) {
                    if (element.type === 'select-one') {
                        element.value = originalFormData[key];
                    } else {
                        element.value = originalFormData[key];
                    }
                }
            });

            formChanged = false;
            updateDiscardButton();
        }
    }

    // Inicializar datos originales del formulario
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editClienteForm');
        const formData = new FormData(form);

        for (let [key, value] of formData.entries()) {
            originalFormData[key] = value;
        }

        // Escuchar cambios en el formulario
        form.addEventListener('change', checkFormChanges);
        form.addEventListener('input', checkFormChanges);

        // Botón cancelar
        document.getElementById('cancelBtn').addEventListener('click', function() {
            if (formChanged) {
                if (confirm('¿Está seguro de que desea cancelar? Los cambios se perderán.')) {
                    window.location.href = '<?= url('clientes') ?>';
                }
            } else {
                window.location.href = '<?= url('clientes') ?>';
            }
        });

        // Botón descartar cambios
        document.getElementById('discardBtn').addEventListener('click', function() {
            resetForm();
        });
    });

    // Envío del formulario
    document.getElementById('editClienteForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validar campos requeridos
        const requiredFields = ['no_identificacion', 'tipo_id', 'nombres', 'apellidos'];
        let isValid = true;

        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                element.classList.add('is-invalid');
                isValid = false;
            } else {
                element.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            showErrorMessage('Por favor complete todos los campos requeridos');
            return;
        }

        // Mostrar indicador de carga
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin btn__icon"></i>Actualizando...';
        submitBtn.disabled = true;

        // Recopilar datos del formulario
        const formData = {
            no_identificacion: document.getElementById('no_identificacion').value,
            tipo_id: document.getElementById('tipo_id').value,
            nombres: document.getElementById('nombres').value,
            apellidos: document.getElementById('apellidos').value,
            genero: document.getElementById('genero').value,
            telefono: document.getElementById('telefono').value,
            direccion: document.getElementById('direccion').value,
            fecha_nacimiento: document.getElementById('fecha_nacimiento').value || null
        };

        // Enviar datos
        fetch('<?= url('clientes/update/' . $cliente['no_identificacion']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage(data.message);

                    // Redirigir a la lista de clientes
                    setTimeout(() => {
                        window.location.href = '<?= url('clientes') ?>';
                    }, 1500);
                } else {
                    // Mostrar errores
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const element = document.getElementById(field);
                            if (element) {
                                element.classList.add('is-invalid');
                                const feedback = document.getElementById('error-' + field);
                                if (feedback) {
                                    feedback.textContent = data.errors[field];
                                }
                            }
                        });
                    }
                    showErrorMessage(data.message || 'Error al actualizar el cliente');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Error de conexión. Verifique su conexión a internet e intente nuevamente.');
            })
            .finally(() => {
                // Restaurar botón
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    });

    // Funciones de mensajes
    function showSuccessMessage(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 3000);
    }

    function showErrorMessage(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>