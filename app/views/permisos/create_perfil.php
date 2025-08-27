<?php

/**
 * Vista para crear un nuevo perfil
 */
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
                                <i class="fas fa-user-tag"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Datos del Perfil</h3>
                                <p class="form-intro__description">Complete la información requerida para crear un nuevo perfil de usuario</p>
                            </div>

                            <div class="form-intro__actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <button type="button" class="btn btn--outline" id="cancelBtn">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="createPerfilForm">
                                    <i class="fas fa-save btn__icon"></i>Crear Perfil
                                </button>
                            </div>
                        </div>
                    </div>
                    <form id="createPerfilForm" class="service-info-grid" action="<?= url('permisos/store') ?>" method="POST">
                        <!-- Nombre del Perfil -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="descripcion">
                                Nombre del Perfil <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-tag service-info__icon"></i>
                                <input type="text" class="form__control" id="descripcion"
                                    name="descripcion" required
                                    placeholder="Ej: Técnico, Supervisor, Administrador"
                                    autocomplete="off">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-descripcion"></div>
                            <div class="form__help">
                                Ingrese un nombre descriptivo para el perfil
                            </div>
                        </div>

                        <!-- Estado del Perfil -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="activo">
                                Estado del Perfil
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-toggle-on service-info__icon"></i>
                                <select class="form__control" id="activo" name="activo">
                                    <option value="1" selected>Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-activo"></div>
                            <div class="form__help">
                                Los perfiles inactivos no pueden ser asignados a usuarios
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="service-info__field service-info__field--two-columns">
                            <div class="service-info-card service-info-card--info">
                                <div class="service-info-card__header">
                                    <h4 class="service-info-card__title">
                                        <i class="fas fa-info-circle service-info-card__icon"></i>
                                        Información Importante
                                    </h4>
                                </div>
                                <div class="service-info-card__body">
                                    <div class="service-info-grid">
                                        <div class="service-info__field">
                                            <div class="service-info__value">
                                                <i class="fas fa-shield-alt service-info__icon"></i>
                                                <span>Después de crear el perfil, podrá asignarle los permisos correspondientes</span>
                                            </div>
                                        </div>
                                        <div class="service-info__field">
                                            <div class="service-info__value">
                                                <i class="fas fa-users service-info__icon"></i>
                                                <span>Los usuarios con este perfil tendrán acceso a las funcionalidades asignadas</span>
                                            </div>
                                        </div>
                                        <div class="service-info__field">
                                            <div class="service-info__value">
                                                <i class="fas fa-cog service-info__icon"></i>
                                                <span>Puede modificar los permisos del perfil en cualquier momento</span>
                                            </div>
                                        </div>
                                    </div>
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
        const form = document.getElementById('createPerfilForm');
        const descripcionInput = document.getElementById('descripcion');
        const activoSelect = document.getElementById('activo');
        const discardBtn = document.getElementById('discardBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const submitBtn = document.getElementById('submitBtn');

        // Función para verificar cambios
        function checkForChanges() {
            const hasChanges = descripcionInput.value.trim() !== '' || activoSelect.value !== '1';
            discardBtn.disabled = !hasChanges;
        }

        // Event listeners para detectar cambios
        descripcionInput.addEventListener('input', checkForChanges);
        activoSelect.addEventListener('change', checkForChanges);

        // Botón descartar cambios
        discardBtn.addEventListener('click', function() {
            Swal.fire({
                title: '¿Descartar cambios?',
                text: 'Se perderán todos los cambios realizados',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, descartar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    descripcionInput.value = '';
                    activoSelect.value = '1';
                    checkForChanges();
                }
            });
        });

        // Botón cancelar
        cancelBtn.addEventListener('click', function() {
            window.location.href = '<?= url('permisos') ?>';
        });

        // Validación del formulario
        form.addEventListener('submit', function(e) {
            const descripcion = descripcionInput.value.trim();

            // Limpiar errores previos
            document.querySelectorAll('.form__feedback--invalid').forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });

            let hasErrors = false;

            // Validar descripción
            if (!descripcion) {
                const errorEl = document.getElementById('error-descripcion');
                errorEl.textContent = 'El nombre del perfil es requerido';
                errorEl.style.display = 'block';
                hasErrors = true;
            } else if (descripcion.length < 3) {
                const errorEl = document.getElementById('error-descripcion');
                errorEl.textContent = 'El nombre del perfil debe tener al menos 3 caracteres';
                errorEl.style.display = 'block';
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Validación',
                    text: 'Por favor corrija los errores antes de continuar',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Mostrar loading
            Swal.fire({
                title: 'Creando perfil...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        // Validación en tiempo real
        descripcionInput.addEventListener('input', function() {
            const value = this.value.trim();
            const errorEl = document.getElementById('error-descripcion');

            if (value.length === 0) {
                errorEl.textContent = 'El nombre del perfil es requerido';
                errorEl.style.display = 'block';
                this.classList.add('form__control--invalid');
            } else if (value.length < 3) {
                errorEl.textContent = 'El nombre del perfil debe tener al menos 3 caracteres';
                errorEl.style.display = 'block';
                this.classList.add('form__control--invalid');
            } else {
                errorEl.style.display = 'none';
                this.classList.remove('form__control--invalid');
                this.classList.add('form__control--valid');
            }
        });
    });
</script>