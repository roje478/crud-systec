<?php
// Verificar que las variables estén definidas
$estado = $estado ?? null;

if (!$estado) {
    echo '<div class="alert alert--danger">Estado no encontrado</div>';
    return;
}
?>

<div class="service-detail">
    <!-- Contenido principal -->
    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Formulario de información del estado -->
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Editar Estado</h3>
                                <p class="form-intro__description">Modifica la información del estado del sistema</p>
                            </div>

                            <div class="form-intro__actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <button type="button" class="btn btn--outline" id="cancelBtn">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="editEstadoForm">
                                    <i class="fas fa-save btn__icon"></i>Actualizar Estado
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="editEstadoForm" class="service-info-grid">
                        <!-- Descripción del Estado -->
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label" for="Descripcion">
                                Descripción del Estado <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-tag service-info__icon"></i>
                                <input type="text"
                                       class="form__control"
                                       id="Descripcion"
                                       name="Descripcion"
                                       value="<?= htmlspecialchars($estado['Descripcion']) ?>"
                                       placeholder="Ej: En mantenimiento, Pendiente de revisión, etc."
                                       required
                                       maxlength="100">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-Descripcion"></div>
                            <div class="form__text">
                                Describe el estado de manera clara y concisa (máximo 100 caracteres).
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel lateral con información -->
        <div class="service-detail__sidebar">
            <div class="service-info-card service-info-card--info">
                <div class="service-info-card__header">
                    <h5 class="service-info-card__title">
                        <i class="fas fa-info-circle"></i> Información del Estado
                    </h5>
                </div>
                <div class="service-info-card__body">
                    <div class="tech-info-compact">
                        <div class="tech-info__row">
                            <div class="tech-info__label">
                                <i class="fas fa-hashtag"></i> ID del Estado
                            </div>
                            <div class="tech-info__value">
                                <?= $estado['IdEstadoEnTaller'] ?>
                            </div>
                        </div>

                        <div class="tech-info__row">
                            <div class="tech-info__label">
                                <i class="fas fa-calendar-plus"></i> Fecha de Creación
                            </div>
                            <div class="tech-info__value">
                                <?= date('d/m/Y H:i', strtotime($estado['created_at'] ?? 'now')) ?>
                            </div>
                        </div>

                        <div class="tech-info__row">
                            <div class="tech-info__label">
                                <i class="fas fa-calendar-edit"></i> Última Modificación
                            </div>
                            <div class="tech-info__value">
                                <?= date('d/m/Y H:i', strtotime($estado['updated_at'] ?? 'now')) ?>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert--warning">
                        <h6 class="alert__heading">
                            <i class="fas fa-exclamation-triangle"></i> Importante
                        </h6>
                        <p class="mb-0">
                            Los cambios en el estado afectarán a todos los servicios que lo utilicen.
                            Asegúrate de que la nueva descripción sea clara y apropiada.
                        </p>
                    </div>

                    <div class="alert alert--info">
                        <h6 class="alert__heading">
                            <i class="fas fa-lightbulb"></i> Consejos
                        </h6>
                        <ul class="mb-0">
                            <li>Mantén descripciones claras y concisas</li>
                            <li>Evita cambios drásticos que confundan a los usuarios</li>
                            <li>Considera el impacto en el flujo de trabajo</li>
                        </ul>
                    </div>

                    <div class="alert alert--success">
                        <h6 class="alert__heading">
                            <i class="fas fa-check-circle"></i> Después de Actualizar
                        </h6>
                        <p class="mb-0">
                            Una vez actualizado el estado:
                        </p>
                        <ul class="mb-0">
                            <li>Los cambios se reflejarán inmediatamente</li>
                            <li>Los servicios existentes mostrarán la nueva descripción</li>
                            <li>Puedes ver el historial de cambios</li>
                        </ul>
                    </div>
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
        const form = document.getElementById('editEstadoForm');
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
            document.getElementById('editEstadoForm').reset();
            // Restaurar valores originales
            document.getElementById('Descripcion').value = '<?= htmlspecialchars($estado['Descripcion']) ?>';
            originalFormData = {};
            formChanged = false;
            updateDiscardButton();
        }
    }

    // Inicializar datos originales del formulario
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editEstadoForm');
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
                    window.location.href = '<?= url('configuracion/estados') ?>';
                }
            } else {
                window.location.href = '<?= url('configuracion/estados') ?>';
            }
        });

        // Botón descartar cambios
        document.getElementById('discardBtn').addEventListener('click', function() {
            if (confirm('¿Está seguro de que desea descartar todos los cambios?')) {
                resetForm();
            }
        });
    });

    // Envío del formulario
    document.getElementById('editEstadoForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validar campos requeridos
        const requiredFields = ['Descripcion'];
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

        // Validar longitud mínima
        const descripcion = document.getElementById('Descripcion').value.trim();
        if (descripcion.length > 0 && descripcion.length < 3) {
            document.getElementById('Descripcion').classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            showErrorMessage('Por favor complete todos los campos requeridos correctamente');
            return;
        }

        // Mostrar indicador de carga
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin btn__icon"></i>Actualizando...';
        submitBtn.disabled = true;

        // Recopilar datos del formulario
        const formData = {
            Descripcion: descripcion
        };

        // Enviar datos
        fetch('<?= url('configuracion/edit-estado/' . $estado['IdEstadoEnTaller']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage(data.message || 'Estado actualizado exitosamente');

                // Redirigir a la lista de estados
                setTimeout(() => {
                    window.location.href = '<?= url('configuracion/estados') ?>';
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
                showErrorMessage(data.message || 'Error al actualizar el estado');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('Error de conexión al actualizar el estado');
        })
        .finally(() => {
            // Restaurar botón
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Validación en tiempo real
    document.getElementById('Descripcion').addEventListener('input', function() {
        const value = this.value.trim();
        const feedback = this.parentNode.querySelector('.form__feedback--invalid');

        if (value.length > 0 && value.length < 3) {
            this.classList.add('is-invalid');
            if (!feedback) {
                const div = document.createElement('div');
                div.className = 'form__feedback form__feedback--invalid';
                div.textContent = 'La descripción debe tener al menos 3 caracteres';
                this.parentNode.appendChild(div);
            }
        } else {
            this.classList.remove('is-invalid');
            if (feedback) {
                feedback.remove();
            }
        }
    });

    // Funciones de mensajes
    function showSuccessMessage(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert--success alert-dismissible fade show position-fixed';
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
        alertDiv.className = 'alert alert--danger alert-dismissible fade show position-fixed';
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