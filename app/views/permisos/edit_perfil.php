<?php
// Vista para editar un perfil existente
?>

<div class="service-detail">
    <!-- Contenido principal -->
    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Formulario de información del perfil -->
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Editar Perfil</h3>
                                <p class="form-intro__description">Modifica la información del perfil "<?= htmlspecialchars($perfil['descripcion']) ?>"</p>
                            </div>

                            <div class="form-intro__actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <a href="<?= url('permisos') ?>" class="btn btn--outline">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="editPerfilForm">
                                    <i class="fas fa-save btn__icon"></i>Actualizar Perfil
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="editPerfilForm" class="service-info-grid">
                        <!-- Descripción del Perfil -->
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label" for="descripcion">
                                <i class="fas fa-tag"></i>
                                Descripción del Perfil <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <input type="text"
                                       class="form__control form__control--modern"
                                       id="descripcion"
                                       name="descripcion"
                                       value="<?= htmlspecialchars($perfil['descripcion']) ?>"
                                       placeholder="Ej: Administrador, Usuario Cliente, etc."
                                       required
                                       maxlength="100">
                                <div class="form__control-focus-border"></div>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-descripcion"></div>
                            <div class="form__text">
                                <i class="fas fa-info-circle"></i>
                                Nombre descriptivo del perfil (máximo 100 caracteres).
                            </div>
                        </div>

                        <!-- Estado del Perfil -->
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label">
                                <i class="fas fa-toggle-on"></i>
                                Estado del Perfil
                            </label>
                            <div class="service-info__input">
                                <div class="form__radio-group form__radio-group--modern">
                                    <label class="form__radio form__radio--modern">
                                        <input type="radio"
                                               name="activo"
                                               value="1"
                                               <?= $perfil['activo'] ? 'checked' : '' ?>>
                                        <span class="form__radio-custom form__radio-custom--modern"></span>
                                        <span class="form__radio-label">
                                            <i class="fas fa-check-circle form__radio-icon form__radio-icon--success"></i>
                                            <span class="form__radio-text">Activo</span>
                                        </span>
                                    </label>
                                    <label class="form__radio form__radio--modern">
                                        <input type="radio"
                                               name="activo"
                                               value="0"
                                               <?= !$perfil['activo'] ? 'checked' : '' ?>>
                                        <span class="form__radio-custom form__radio-custom--modern"></span>
                                        <span class="form__radio-label">
                                            <i class="fas fa-times-circle form__radio-icon form__radio-icon--danger"></i>
                                            <span class="form__radio-text">Inactivo</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="form__text">
                                <i class="fas fa-exclamation-triangle"></i>
                                Los perfiles inactivos no aparecerán en las listas de selección.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* Estilos mejorados para la vista de editar perfil */
.form__control--modern {
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #ffffff;
}

.form__control--modern:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    outline: none;
}

.form__control-focus-border {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: #007bff;
    transition: width 0.3s ease;
}

.form__control--modern:focus + .form__control-focus-border {
    width: 100%;
}

.form__radio-group--modern {
    display: flex;
    gap: 20px;
    margin-top: 8px;
}

.form__radio--modern {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 12px 16px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: #ffffff;
    min-width: 120px;
}

.form__radio--modern:hover {
    border-color: #007bff;
    background: #f8f9fa;
}

.form__radio--modern input[type="radio"] {
    display: none;
}

.form__radio-custom--modern {
    width: 20px;
    height: 20px;
    border: 2px solid #e1e5e9;
    border-radius: 50%;
    margin-right: 12px;
    position: relative;
    transition: all 0.3s ease;
}

.form__radio--modern input[type="radio"]:checked + .form__radio-custom--modern {
    border-color: #007bff;
    background: #007bff;
}

.form__radio--modern input[type="radio"]:checked + .form__radio-custom--modern::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: #ffffff;
    border-radius: 50%;
}

.form__radio-label {
    display: flex;
    align-items: center;
    flex: 1;
}

.form__radio-icon {
    margin-right: 8px;
    font-size: 16px;
}

.form__radio-icon--success {
    color: #28a745;
}

.form__radio-icon--danger {
    color: #dc3545;
}

.form__radio-text {
    font-weight: 500;
    color: #495057;
}

.service-info--enhanced {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.service-info__field--enhanced {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
}

.service-info__field--enhanced:last-child {
    border-bottom: none;
}

.service-info__label {
    display: flex;
    align-items: center;
    font-weight: 500;
    color: #495057;
}

.service-info__label i {
    margin-right: 8px;
    color: #6c757d;
}

.service-info__input--highlight {
    font-weight: 600;
}

.badge--modern {
    padding: 8px 12px;
    border-radius: 6px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.alert--enhanced {
    border: none;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    display: flex;
    align-items: flex-start;
}

.alert__icon {
    margin-right: 12px;
    font-size: 18px;
    margin-top: 2px;
}

.alert__content {
    flex: 1;
}

.alert__heading {
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: 600;
}

.alert__text {
    margin: 0;
    font-size: 13px;
    line-height: 1.4;
}

.btn--modern {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    padding: 8px 16px;
}

.btn--modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.form__text {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6c757d;
    font-size: 12px;
    margin-top: 6px;
}

.form__text i {
    color: #007bff;
}

/* Mejoras en el formulario */
.service-info__field {
    position: relative;
    margin-bottom: 24px;
}

.service-info__input {
    position: relative;
}

/* Animaciones suaves */
.service-info-card {
    transition: all 0.3s ease;
}

.service-info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}
</style>

<script>
// Variables para detectar cambios
let formInitialState = {};
let hasChanges = false;

// Función para obtener el estado actual del formulario
function getFormState() {
    return {
        descripcion: document.getElementById('descripcion').value,
        activo: document.querySelector('input[name="activo"]:checked').value
    };
}

// Función para verificar si hay cambios
function checkForChanges() {
    const currentState = getFormState();
    hasChanges = JSON.stringify(currentState) !== JSON.stringify(formInitialState);

    const discardBtn = document.getElementById('discardBtn');
    if (discardBtn) {
        discardBtn.disabled = !hasChanges;
        discardBtn.style.opacity = hasChanges ? '1' : '0.5';
    }
}

// Inicializar estado del formulario
document.addEventListener('DOMContentLoaded', function() {
    formInitialState = getFormState();

    // Agregar listeners para detectar cambios
    document.getElementById('descripcion').addEventListener('input', checkForChanges);
    document.querySelectorAll('input[name="activo"]').forEach(radio => {
        radio.addEventListener('change', checkForChanges);
    });

    // Botón descartar cambios
    document.getElementById('discardBtn').addEventListener('click', function() {
        if (confirm('¿Está seguro de que desea descartar los cambios?')) {
            document.getElementById('descripcion').value = formInitialState.descripcion;
            document.querySelector(`input[name="activo"][value="${formInitialState.activo}"]`).checked = true;
            checkForChanges();

            // Limpiar errores
            document.querySelectorAll('.form__feedback--invalid').forEach(feedback => {
                feedback.textContent = '';
                feedback.style.display = 'none';
            });
        }
    });
});

// Validación del formulario
document.getElementById('editPerfilForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Limpiar errores anteriores
    document.querySelectorAll('.form__feedback--invalid').forEach(feedback => {
        feedback.textContent = '';
        feedback.style.display = 'none';
    });

    const descripcion = document.getElementById('descripcion').value.trim();
    let hasErrors = false;

    // Validar descripción
    if (descripcion === '') {
        showError('descripcion', 'Por favor ingrese la descripción del perfil');
        hasErrors = true;
    } else if (descripcion.length < 3) {
        showError('descripcion', 'La descripción del perfil debe tener al menos 3 caracteres');
        hasErrors = true;
    }

    if (hasErrors) {
        return false;
    }

    // Mostrar loading
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin btn__icon"></i>Actualizando...';
    submitBtn.disabled = true;

    // Enviar formulario
    const formData = new FormData(this);

    fetch('<?= url('permisos/update/' . $perfil['id']) ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Debug: Log de la respuesta
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);

        // Intentar parsear JSON sin importar el status
        return response.text().then(text => {
            console.log('Response text:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                throw new Error('Respuesta inválida del servidor');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showSuccess('Perfil actualizado correctamente');
            setTimeout(() => {
                window.location.href = '<?= url('permisos') ?>';
            }, 1500);
        } else {
            showError('descripcion', data.message || 'Error al actualizar el perfil');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('descripcion', 'Error de conexión al actualizar el perfil');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Función para mostrar errores
function showError(fieldId, message) {
    const errorElement = document.getElementById(`error-${fieldId}`);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        errorElement.style.color = '#dc3545';
        errorElement.style.fontSize = '12px';
        errorElement.style.marginTop = '4px';
    }
}

// Función para mostrar éxito
function showSuccess(message) {
    // Crear notificación de éxito
    const notification = document.createElement('div');
    notification.className = 'alert alert--success';
    notification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        ${message}
    `;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.padding = '16px 20px';
    notification.style.borderRadius = '8px';
    notification.style.backgroundColor = '#d4edda';
    notification.style.color = '#155724';
    notification.style.border = '1px solid #c3e6cb';
    notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Función para confirmar eliminación
function confirmarEliminacion(perfilId, descripcion) {
    if (confirm(`¿Está seguro de que desea eliminar el perfil "${descripcion}"?\n\nEsta acción no se puede deshacer.`)) {
        // Realizar petición AJAX para eliminar
        fetch(`<?= url('permisos/delete/') ?>${perfilId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Perfil eliminado correctamente');
                setTimeout(() => {
                    window.location.href = '<?= url('permisos') ?>';
                }, 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el perfil');
        });
    }
}

// Prevenir envío del formulario al presionar Enter en campos de texto
document.getElementById('descripcion').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('editPerfilForm').dispatchEvent(new Event('submit'));
    }
});
</script>