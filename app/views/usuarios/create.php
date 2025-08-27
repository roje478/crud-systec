<?php

/**
 * Vista Crear Usuario
 */

// Verificar que las variables estén definidas
$perfiles = $perfiles ?? [];
?>

<div class="service-detail">

    <!-- Contenido principal -->
    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Formulario de información del usuario -->
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Datos del Usuario</h3>
                                <p class="form-intro__description">Complete la información requerida para crear un nuevo usuario del sistema</p>
                            </div>
                            <div class="service-detail__header-actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <button type="button" class="btn btn--outline" id="cancelBtn">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="createUsuarioForm">
                                    <i class="fas fa-save btn__icon"></i>Crear Usuario
                                </button>
                            </div>
                        </div>
                    </div>
                    <form id="createUsuarioForm" class="service-info-grid">
                        <!-- Nombres -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="nombres">
                                Nombres <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <input type="text" class="form__control" id="nombres"
                                    name="nombres" required
                                    placeholder="Ej: Juan Carlos"
                                    value=""
                                    autocomplete="off">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-nombres"></div>
                            <div class="form__help">
                                Ingrese los nombres del usuario
                            </div>
                        </div>

                        <!-- Apellidos -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="apellidos">
                                Apellidos <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <input type="text" class="form__control" id="apellidos"
                                    name="apellidos" required
                                    placeholder="Ej: Pérez García"
                                    value=""
                                    autocomplete="off">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-apellidos"></div>
                            <div class="form__help">
                                Ingrese los apellidos del usuario
                            </div>
                        </div>

                        <!-- Identificación -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="no_identificacion">
                                Identificación <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-id-card service-info__icon"></i>
                                <input type="text" class="form__control" id="no_identificacion"
                                    name="no_identificacion" required
                                    placeholder="Ej: 1234567890"
                                    value=""
                                    autocomplete="off">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-no_identificacion"></div>
                            <div class="form__help">
                                Ingrese el número de identificación del usuario
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="direccion">
                                Dirección
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-map-marker-alt service-info__icon"></i>
                                <input type="text" class="form__control" id="direccion"
                                    name="direccion"
                                    placeholder="Ej: Calle 123 #45-67, Ciudad"
                                    value=""
                                    autocomplete="new-password"
                                    data-lpignore="true"
                                    data-form-type="other">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-direccion"></div>
                            <div class="form__help">
                                Ingrese la dirección del usuario (opcional)
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
                                    name="telefono"
                                    placeholder="Ej: 300-123-4567"
                                    value=""
                                    autocomplete="new-password"
                                    data-lpignore="true"
                                    data-form-type="other">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-telefono"></div>
                            <div class="form__help">
                                Ingrese el número de teléfono del usuario (opcional)
                            </div>
                        </div>

                        <!-- Perfil -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="codigo_perfil">
                                Perfil <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user-tag service-info__icon"></i>
                                <select class="form__control" id="codigo_perfil" name="codigo_perfil" required>
                                    <option value="">Seleccione un perfil...</option>
                                    <?php foreach ($perfiles as $perfil): ?>
                                        <option value="<?= $perfil['id'] ?>">
                                            <?= htmlspecialchars($perfil['descripcion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-codigo_perfil"></div>
                        </div>

                        <!-- Contraseña -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="contrasenia">
                                Contraseña <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-lock service-info__icon"></i>
                                <input type="password" class="form__control" id="contrasenia"
                                    name="contrasenia" required minlength="6"
                                    placeholder="Mínimo 6 caracteres">
                                <button type="button" class="service-info__toggle" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-contrasenia"></div>
                            <div class="form__help">
                                La contraseña debe tener al menos 6 caracteres
                            </div>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="confirmar_contrasenia">
                                Confirmar Contraseña <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-lock service-info__icon"></i>
                                <input type="password" class="form__control" id="confirmar_contrasenia"
                                    name="confirmar_contrasenia" required minlength="6"
                                    placeholder="Repita la contraseña">
                                <button type="button" class="service-info__toggle" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-confirmar_contrasenia"></div>
                        </div>

                        <!-- Estado -->
                        <div class="service-info__field">
                            <label class="service-info__label">
                                Estado del Usuario
                            </label>
                            <div class="service-info__input service-info__input--checkbox">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="activo"
                                        name="activo" value="1" checked>
                                    <label class="form-check-label" for="activo">
                                        <i class="fas fa-toggle-on service-info__icon"></i>
                                        Usuario activo
                                    </label>
                                </div>
                            </div>
                            <div class="form__help">
                                Los usuarios inactivos no pueden acceder al sistema
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
        const form = document.getElementById('createUsuarioForm');
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
        if (confirm('¿Está seguro de que desea limpiar todos los campos?')) {
            document.getElementById('createUsuarioForm').reset();
            originalFormData = {};
            formChanged = false;
            updateDiscardButton();
        }
    }

    // Inicializar datos originales del formulario
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('createUsuarioForm');
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
                    window.location.href = '<?= url('usuarios') ?>';
                }
            } else {
                window.location.href = '<?= url('usuarios') ?>';
            }
        });

        // Botón descartar cambios
        document.getElementById('discardBtn').addEventListener('click', function() {
            if (confirm('¿Está seguro de que desea descartar todos los cambios?')) {
                resetForm();
            }
        });

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const passwordInput = document.getElementById('contrasenia');
        const confirmPasswordInput = document.getElementById('confirmar_contrasenia');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });

    // Envío del formulario
    document.getElementById('createUsuarioForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validar campos requeridos
        const requiredFields = ['nombres', 'apellidos', 'no_identificacion', 'codigo_perfil', 'contrasenia', 'confirmar_contrasenia'];
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

        // Validar contraseñas
        const password = document.getElementById('contrasenia').value;
        const confirmPassword = document.getElementById('confirmar_contrasenia').value;

        if (password !== confirmPassword) {
            document.getElementById('confirmar_contrasenia').classList.add('is-invalid');
            showErrorMessage('Las contraseñas no coinciden');
            return;
        } else {
            document.getElementById('confirmar_contrasenia').classList.remove('is-invalid');
        }

        if (password.length < 6) {
            document.getElementById('contrasenia').classList.add('is-invalid');
            showErrorMessage('La contraseña debe tener al menos 6 caracteres');
            return;
        } else {
            document.getElementById('contrasenia').classList.remove('is-invalid');
        }

        if (!isValid) {
            showErrorMessage('Por favor complete todos los campos requeridos');
            return;
        }

        // Mostrar indicador de carga
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin btn__icon"></i>Creando...';
        submitBtn.disabled = true;

        // Recopilar datos del formulario
        const formData = {
            nombres: document.getElementById('nombres').value,
            apellidos: document.getElementById('apellidos').value,
            no_identificacion: document.getElementById('no_identificacion').value,
            direccion: document.getElementById('direccion').value,
            telefono: document.getElementById('telefono').value,
            codigo_perfil: document.getElementById('codigo_perfil').value,
            contrasenia: password,
            confirmar_contrasenia: confirmPassword,
            activo: document.getElementById('activo').checked ? 1 : 0
        };

        // Enviar datos
        fetch('<?= url('usuarios/store') ?>', {
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

                    // Redirigir a la lista de usuarios
                    setTimeout(() => {
                        window.location.href = '<?= url('usuarios') ?>';
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
                    showErrorMessage(data.message || 'Error al crear el usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Error de conexión al crear el usuario');
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