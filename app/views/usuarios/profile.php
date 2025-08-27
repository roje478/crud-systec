<?php
// Verificar que las variables estén definidas
$usuario = $usuario ?? null;

if (!$usuario) {
    echo '<div class="alert alert-danger">Usuario no encontrado</div>';
    return;
}
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
                                <h3 class="form-intro__title">Mi Perfil</h3>
                                <p class="form-intro__description">Actualice su información personal y contraseña</p>
                            </div>
                            <div class="service-detail__header-actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <button type="button" class="btn btn--outline" id="cancelBtn">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="profileForm">
                                    <i class="fas fa-save btn__icon"></i>Actualizar Perfil
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Información del usuario -->
                    <div class="service-info-card service-info-card--info">
                        <div class="service-info-card__header">
                            <h4 class="service-info-card__title">
                                <i class="fas fa-user service-info-card__icon"></i>
                                Información del Usuario
                            </h4>
                        </div>
                        <div class="service-info-card__body">
                            <div class="service-info-grid">
                                <div class="service-info__field">
                                    <label class="service-info__label">Nombre Completo</label>
                                    <div class="service-info__input">
                                        <i class="fas fa-user service-info__icon"></i>
                                        <span><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></span>
                                    </div>
                                </div>
                                <div class="service-info__field">
                                    <label class="service-info__label">Número de Identificación</label>
                                    <div class="service-info__input">
                                        <i class="fas fa-id-card service-info__icon"></i>
                                        <span><?= htmlspecialchars($usuario['no_identificacion']) ?></span>
                                    </div>
                                </div>
                                <div class="service-info__field">
                                    <label class="service-info__label">Perfil</label>
                                    <div class="service-info__input">
                                        <i class="fas fa-user-tag service-info__icon"></i>
                                        <span><?= htmlspecialchars($usuario['perfil_descripcion'] ?? 'Sin perfil') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="profileForm" class="service-info-grid" method="post" action="#">
                        <!-- Información personal -->
                        <div class="service-info__field">
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

                        <div class="service-info__field">
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

                        <div class="service-info__field">
                            <label for="telefono" class="service-info__label">Teléfono</label>
                            <div class="service-info__input">
                                <i class="fas fa-phone service-info__icon"></i>
                                <input type="tel"
                                    name="telefono"
                                    id="telefono"
                                    class="form__control"
                                    value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                                    required>
                            </div>
                        </div>

                        <div class="service-info__field">
                            <label for="direccion" class="service-info__label">Dirección</label>
                            <div class="service-info__input">
                                <i class="fas fa-map-marker-alt service-info__icon"></i>
                                <input type="text"
                                    name="direccion"
                                    id="direccion"
                                    class="form__control"
                                    value="<?= htmlspecialchars($usuario['direccion'] ?? '') ?>"
                                    placeholder="Ingrese su dirección">
                            </div>
                        </div>

                        <!-- Cambio de contraseña -->
                        <div class="service-info__field service-info__field--full-width">
                            <div class="service-info-card service-info-card--info">
                                <div class="service-info-card__header">
                                    <h4 class="service-info-card__title">
                                        <i class="fas fa-lock service-info-card__icon"></i>
                                        Cambiar Contraseña
                                    </h4>
                                </div>
                                <div class="service-info-card__body">
                                    <div class="service-info-grid">
                                        <div class="service-info__field">
                                            <label for="contrasenia" class="service-info__label">Nueva Contraseña</label>
                                            <div class="service-info__input">
                                                <i class="fas fa-lock service-info__icon"></i>
                                                <input type="password"
                                                    name="contrasenia"
                                                    id="contrasenia"
                                                    class="form__control"
                                                    minlength="6"
                                                    placeholder="Deje vacío si no desea cambiar">
                                            </div>
                                        </div>
                                        <div class="service-info__field">
                                            <label for="confirmar_contrasenia" class="service-info__label">Confirmar Contraseña</label>
                                            <div class="service-info__input">
                                                <i class="fas fa-lock service-info__icon"></i>
                                                <input type="password"
                                                    name="confirmar_contrasenia"
                                                    id="confirmar_contrasenia"
                                                    class="form__control"
                                                    minlength="6"
                                                    placeholder="Confirme la nueva contraseña">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="service-info__field">
                                        <div class="service-info__value">
                                            <i class="fas fa-info-circle service-info__icon"></i>
                                            <span>Deje los campos de contraseña vacíos si no desea cambiarla</span>
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
$(document).ready(function() {
    // Validación de contraseñas en tiempo real
    $('#confirmar_contrasenia').on('input', function() {
        const contrasenia = $('#contrasenia').val();
        const confirmar = $(this).val();

        if (contrasenia && confirmar && contrasenia !== confirmar) {
            $(this).addClass('is-invalid');
            $(this).removeClass('is-valid');
        } else if (contrasenia && confirmar && contrasenia === confirmar) {
            $(this).removeClass('is-invalid');
            $(this).addClass('is-valid');
        } else {
            $(this).removeClass('is-invalid is-valid');
        }

        checkFormChanges();
    });

    // Validar cambios en el formulario
    $('#profileForm input').on('input', function() {
        checkFormChanges();
    });

    function checkFormChanges() {
        const form = $('#profileForm')[0];
        const hasChanges = Array.from(form.elements).some(element => {
            if (element.type === 'password') {
                return element.value.length > 0;
            }
            return element.value !== element.defaultValue;
        });

        $('#submitBtn, #discardBtn').prop('disabled', !hasChanges);
    }

    // Envío del formulario
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();

        // Validar contraseñas
        const contrasenia = $('#contrasenia').val();
        const confirmar = $('#confirmar_contrasenia').val();

        if (contrasenia && !confirmar) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes confirmar la contraseña'
            });
            return;
        }

        if (contrasenia && confirmar && contrasenia !== confirmar) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas no coinciden'
            });
            return;
        }

        // Mostrar loading
        Swal.fire({
            title: 'Actualizando perfil...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Enviar datos
        $.ajax({
            url: '<?= url('usuarios/update-profile') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        // Recargar la página para mostrar los cambios
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al actualizar el perfil'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Error al actualizar el perfil';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).join('\n');
                    } else if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    });

    // Botón cancelar
    $('#cancelBtn').on('click', function() {
        Swal.fire({
            title: '¿Cancelar cambios?',
            text: 'Los cambios no guardados se perderán',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, continuar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= url('servicios') ?>';
            }
        });
    });

    // Botón descartar cambios
    $('#discardBtn').on('click', function() {
        Swal.fire({
            title: '¿Descartar cambios?',
            text: 'Los cambios no guardados se perderán',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'No, continuar'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    });
});
</script>