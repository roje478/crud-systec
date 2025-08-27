<?php
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_activo']) || !$_SESSION['usuario_activo']) {
    header('Location: index.php?route=auth/login');
    exit;
}

// Establecer breadcrumb
$breadcrumb = [
    ['text' => 'Servicios', 'url' => url('servicios')],
    ['text' => 'Seleccionar Cliente']
];
?>

<!-- Contenido de la página -->
<div class="service-detail">
    <div class="service-detail__content">
        <div class="service-detail__main">

            <!-- Opción 1: Seleccionar Cliente Existente -->
            <div class="service-info-card">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="form-intro__content">
                            <h3 class="form-intro__title">Cliente Existente</h3>
                            <p class="form-intro__description">Seleccione un cliente de la lista para continuar con el servicio</p>
                        </div>
                    </div>

                    <div class="service-info-grid mb-3">
                        <div class="service-info__field">
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <select class="form__control" id="cliente_existente" name="cliente_existente">
                                    <option value="">Seleccionar cliente</option>
                                    <?php if (!empty($clientes)): ?>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <option value="<?= htmlspecialchars($cliente['NoIdentificacionCliente']) ?>">
                                                <?= htmlspecialchars($cliente['NombreCliente']) ?>
                                                (<?= htmlspecialchars($cliente['NoIdentificacionCliente']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No hay clientes disponibles</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                            <button type="button" class="btn btn--primary" id="btnSeleccionarCliente" disabled>
                                <i class="fas fa-arrow-right btn__icon"></i>
                                Continuar con Cliente Seleccionado
                            </button>

                    </div>

                    <!-- Información del Cliente Seleccionado -->
                    <div id="cliente-info" class="service-info-card service-info-card--info" style="display: none;">
                        <div class="service-info-card__header">
                            <h4 class="service-info-card__title">
                                <i class="fas fa-user-check service-info-card__icon"></i>
                                Información del Cliente Seleccionado
                            </h4>
                        </div>
                        <div class="service-info-card__body">
                            <div class="service-info-grid">
                                <div class="service-info__field">
                                    <label class="service-info__label">Nombre Completo</label>
                                    <div class="service-info__value">
                                        <i class="fas fa-user service-info__icon"></i>
                                        <span id="cliente-nombre">-</span>
                                    </div>
                                </div>
                                <div class="service-info__field">
                                    <label class="service-info__label">Identificación</label>
                                    <div class="service-info__value">
                                        <i class="fas fa-id-card service-info__icon"></i>
                                        <span id="cliente-identificacion">-</span>
                                    </div>
                                </div>
                                <div class="service-info__field">
                                    <label class="service-info__label">Teléfono</label>
                                    <div class="service-info__value">
                                        <i class="fas fa-phone service-info__icon"></i>
                                        <span id="cliente-telefono">-</span>
                                    </div>
                                </div>
                                <div class="service-info__field">
                                    <label class="service-info__label">Dirección</label>
                                    <div class="service-info__value">
                                        <i class="fas fa-map-marker-alt service-info__icon"></i>
                                        <span id="cliente-direccion">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Separador -->
            <div style="text-align: center; margin: 2rem 0; position: relative;">
                <div style="position: relative; display: inline-block; background: #f8f9fa; padding: 0 2rem;">
                    <span style="background: #f8f9fa; padding: 0 1rem; font-weight: 600; color: #6c757d;">O</span>
                </div>
                <hr style="position: absolute; top: 50%; left: 0; right: 0; z-index: -1; margin: 0;">
            </div>

            <!-- Opción 2: Crear Nuevo Cliente -->
            <div class="service-info-card">

                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Nuevo Cliente</h3>
                                <p class="form-intro__description">Complete la información para crear un nuevo cliente</p>
                            </div>
                            <div class="form__actions mt-0 pt-0">
                                <button type="submit" class="btn btn--primary">
                                    <i class="fas fa-save btn__icon"></i>
                                    Crear Cliente y Continuar
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="createClienteForm" class="service-info-grid">
                        <!-- Información de Identificación -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="tipo_id">
                                Tipo de ID <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-id-card service-info__icon"></i>
                                <select class="form__control" id="tipo_id" name="tipo_id" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                    <option value="NIT">NIT</option>
                                    <option value="TI">Tarjeta de Identidad</option>
                                    <option value="PP">Pasaporte</option>
                                </select>
                            </div>
                        </div>

                        <div class="service-info__field">
                            <label class="service-info__label" for="no_identificacion">
                                Número de Identificación <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-hashtag service-info__icon"></i>
                                <input type="text" class="form__control" id="no_identificacion" name="no_identificacion" required>
                            </div>
                        </div>

                        <!-- Información Personal -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="nombres">
                                Nombres <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <input type="text" class="form__control" id="nombres" name="nombres" required>
                            </div>
                        </div>

                        <div class="service-info__field">
                            <label class="service-info__label" for="apellidos">
                                Apellidos <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <input type="text" class="form__control" id="apellidos" name="apellidos" required>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="telefono">
                                Teléfono
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-phone service-info__icon"></i>
                                <input type="text" class="form__control" id="telefono" name="telefono">
                            </div>
                        </div>

                        <div class="service-info__field">
                            <label class="service-info__label" for="direccion">
                                Dirección
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-map-marker-alt service-info__icon"></i>
                                <input type="text" class="form__control" id="direccion" name="direccion">
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<script>
    $(document).ready(function() {
        // Función para verificar si hay un cliente seleccionado
        function checkClienteSeleccionado() {
            const clienteSeleccionado = $('#cliente_existente').val();
            const btnSeleccionar = $('#btnSeleccionarCliente');

            if (clienteSeleccionado) {
                btnSeleccionar.prop('disabled', false);
                return true;
            } else {
                btnSeleccionar.prop('disabled', true);
                return false;
            }
        }

        // Detectar cambios en la selección de cliente existente
        $('#cliente_existente').on('change', function() {
            const clienteSeleccionado = checkClienteSeleccionado();

            if (clienteSeleccionado) {
                const clienteId = $(this).val();
                const clienteNombre = $(this).find('option:selected').text();

                // Mostrar información básica
                $('#cliente-nombre').text(clienteNombre);
                $('#cliente-identificacion').text(clienteId);
                $('#cliente-telefono').text('Consultando...');
                $('#cliente-direccion').text('Consultando...');

                // Mostrar la información
                $('#cliente-info').show();

                // Aquí podrías hacer una llamada AJAX para obtener más detalles del cliente
                // Por ahora mostramos información básica
            } else {
                $('#cliente-info').hide();
            }
        });

        // Botón para continuar con cliente seleccionado
        $('#btnSeleccionarCliente').on('click', function() {
            const clienteId = $('#cliente_existente').val();
            if (clienteId) {
                window.location.href = 'index.php?route=servicios/create&cliente_id=' + clienteId;
            }
        });

        // Formulario para crear nuevo cliente
        $('#createClienteForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                tipo_id: $('#tipo_id').val(),
                no_identificacion: $('#no_identificacion').val(),
                nombres: $('#nombres').val(),
                apellidos: $('#apellidos').val(),
                telefono: $('#telefono').val(),
                direccion: $('#direccion').val()
            };

            // Validación básica
            if (!formData.tipo_id || !formData.no_identificacion || !formData.nombres || !formData.apellidos) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor complete todos los campos obligatorios'
                });
                return;
            }

            // Crear cliente
            $.ajax({
                url: 'index.php?route=clientes/store',
                method: 'POST',
                data: formData,
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cliente Creado',
                                text: 'El cliente se ha creado exitosamente'
                            }).then(() => {
                                // Redirigir a crear servicio con el nuevo cliente
                                window.location.href = 'index.php?route=servicios/create&cliente_id=' + formData.no_identificacion;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Error al crear el cliente'
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al procesar la respuesta del servidor'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión al crear el cliente'
                    });
                }
            });
        });

        // Validación en tiempo real
        $('#no_identificacion').on('input', function() {
            const value = $(this).val();
            if (value.length > 0) {
                $(this).removeClass('form__control--invalid').addClass('form__control--valid');
            } else {
                $(this).removeClass('form__control--valid').addClass('form__control--invalid');
            }
        });

        $('#nombres, #apellidos').on('input', function() {
            const value = $(this).val();
            if (value.length > 0) {
                $(this).removeClass('form__control--invalid').addClass('form__control--valid');
            } else {
                $(this).removeClass('form__control--valid').addClass('form__control--invalid');
            }
        });

        // Toggle sidebar
        $('#sidebarToggle').on('click', function() {
            $('.sidebar').toggleClass('collapsed');
            $('.main').toggleClass('expanded');
        });
    });
</script>