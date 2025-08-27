<?php

/**
 * Vista Editar Usuario
 */

// Verificar que las variables estén definidas
$usuario = $usuario ?? null;
$perfiles = $perfiles ?? [];

if (!$usuario) {
    echo '<div class="alert alert-danger">Usuario no encontrado</div>';
    return;
}
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
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Editar Usuario</h3>
                                <p class="form-intro__description">Modifique la información del usuario según sea necesario</p>
                            </div>
                            <div class="service-detail__header-actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <button type="button" class="btn btn--outline" id="cancelBtn">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="editUsuarioForm">
                                    <i class="fas fa-save btn__icon"></i>Actualizar Usuario
                                </button>
                            </div>
                        </div>
                    </div>
                    <form id="editUsuarioForm" class="service-info-grid" method="post" action="#">

                        <div class="service-info__field service-info__field">
                            <label for="nombres" class="service-info__label">Nombres</label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <input type="text"
                                    name="nombres"
                                    id="nombres"
                                    class="form__control"
                                    value="<?= htmlspecialchars($usuario['nombres']) ?>"
                                    required>
                            </div>
                        </div>

                        <div class="service-info__field service-info__field">
                            <label for="apellidos" class="service-info__label">Apellidos</label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <input type="text"
                                    name="apellidos"
                                    id="apellidos"
                                    class="form__control"
                                    value="<?= htmlspecialchars($usuario['apellidos']) ?>"
                                    required>
                            </div>
                        </div>

                        <!-- Número de Identificación (Solo lectura) -->
                        <div class="service-info__field">
                            <label class="service-info__label">
                                Número de Identificación
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-id-card service-info__icon"></i>
                                <input type="text"
                                    class="form__control"
                                    value="<?= htmlspecialchars($usuario['no_identificacion']) ?>"
                                    readonly>
                            </div>
                        </div>

                        <div class="service-info__field service-info__field">
                            <label for="telefono" class="service-info__label">Teléfono</label>
                            <div class="service-info__input">
                                <i class="fas fa-phone service-info__icon"></i>
                                <input type="text"
                                    name="telefono"
                                    id="telefono"
                                    class="form__control"
                                    value="<?= htmlspecialchars($usuario['telefono'] ?: '') ?>"
                                    placeholder="Ingrese el teléfono">
                            </div>
                        </div>

                        <div class="service-info__field service-info__field">
                            <label for="direccion" class="service-info__label">Dirección</label>
                            <div class="service-info__input">
                                <i class="fas fa-map-marker-alt service-info__icon"></i>
                                <input type="text"
                                    name="direccion"
                                    id="direccion"
                                    class="form__control"
                                    value="<?= htmlspecialchars($usuario['direccion'] ?: '') ?>"
                                    placeholder="Ingrese la dirección">
                            </div>
                        </div>

                        <!-- Perfil -->
                        <div class="service-info__field">
                            <label for="codigo_perfil" class="service-info__label">
                                Perfil <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user-tag service-info__icon"></i>
                                <select name="codigo_perfil" id="codigo_perfil" class="form__control" required>
                                    <option value="">Seleccione un perfil...</option>
                                    <?php foreach ($perfiles as $perfil): ?>
                                        <option value="<?= $perfil['id'] ?>"
                                            <?= $usuario['codigo_perfil'] == $perfil['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($perfil['descripcion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-codigo_perfil"></div>
                        </div>

                        <!-- Nueva Contraseña (Opcional) -->
                        <div class="service-info__field">
                            <label for="contrasenia" class="service-info__label">
                                Nueva Contraseña
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-lock service-info__icon"></i>
                                <input type="password"
                                    name="contrasenia"
                                    id="contrasenia"
                                    class="form__control"
                                    minlength="6"
                                    placeholder="Dejar vacío para mantener la actual">
                                <button class="btn btn--outline"
                                    type="button"
                                    id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form__feedback">
                                Dejar vacío para mantener la contraseña actual. Mínimo 6 caracteres si se cambia.
                            </div>
                        </div>

                        <!-- Confirmar Nueva Contraseña -->
                        <div class="service-info__field">
                            <label for="confirmar_contrasenia" class="service-info__label">
                                Confirmar Nueva Contraseña
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-lock service-info__icon"></i>
                                <input type="password"
                                    name="confirmar_contrasenia"
                                    id="confirmar_contrasenia"
                                    class="form__control"
                                    minlength="6"
                                    placeholder="Repita la nueva contraseña">
                                <button class="btn btn--outline"
                                    type="button"
                                    id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-confirmar_contrasenia"></div>
                        </div>

                        <!-- Estado -->
                        <div class="service-info__field">
                            <label class="service-info__label">
                                Estado
                            </label>
                            <div class="service-info__input">
                                <div class="form-check">
                                    <input type="checkbox"
                                        name="activo"
                                        id="activo"
                                        class="form__control"
                                        value="1"
                                        <?= $usuario['activo'] ? 'checked' : '' ?>>
                                    <label for="activo" class="form-check-label">
                                        Usuario activo
                                    </label>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editUsuarioForm');
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const passwordInput = document.getElementById('contrasenia');
        const confirmPasswordInput = document.getElementById('confirmar_contrasenia');
        const discardBtn = document.getElementById('discardBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const submitBtn = document.getElementById('submitBtn');

        // Toggle password visibility
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

        // Form validation
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validar contraseñas si se proporcionan
            if (passwordInput.value || confirmPasswordInput.value) {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    showErrorMessage('Las contraseñas no coinciden');
                    return;
                }

                if (passwordInput.value.length < 6) {
                    showErrorMessage('La contraseña debe tener al menos 6 caracteres');
                    return;
                }
            }

            // Enviar formulario
            submitForm();
        });

        // Cancel button
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('¿Está seguro de que desea cancelar? Los cambios no guardados se perderán.')) {
                window.location.href = '<?= url('usuarios/view/' . $usuario['no_identificacion']) ?>';
            }
        });

        // Discard changes button (disabled by default)
        discardBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('¿Está seguro de que desea descartar todos los cambios?')) {
                window.location.reload();
            }
        });

        function submitForm() {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Convertir checkbox a boolean
            data.activo = formData.has('activo') ? 1 : 0;

            // Remover contraseña vacía
            if (!data.contrasenia) {
                delete data.contrasenia;
                delete data.confirmar_contrasenia;
            }

            // Mostrar loading
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin btn__icon"></i>Actualizando...';
            submitBtn.disabled = true;

            fetch('<?= url('usuarios/update/' . $usuario['no_identificacion']) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage(data.message);
                        setTimeout(() => {
                            window.location.href = '<?= url('usuarios/view/' . $usuario['no_identificacion']) ?>';
                        }, 1500);
                    } else {
                        showErrorMessage(data.message || 'Error al actualizar el usuario');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('Error de conexión');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

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
    });
</script>