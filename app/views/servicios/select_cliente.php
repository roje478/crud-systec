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
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label" for="clienteSearch">
                                Buscar Cliente <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-search service-info__icon"></i>
                                <input type="text"
                                    class="form__control"
                                    id="clienteSearch"
                                    placeholder="Escriba nombre, apellido o identificación del cliente..."
                                    autocomplete="off">
                                <input type="hidden" id="cliente_existente" name="cliente_existente">
                            </div>
                        </div>

                    </div>

                    <!-- Contenedor para resultados de búsqueda -->
                    <div id="clienteSearchContainer" class="cliente-search-container" style="display: none;">
                        <!-- Los resultados se cargarán dinámicamente aquí -->
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
        // Variables para la búsqueda de clientes
        let searchTimeout;
        let selectedIndex = -1;
        let searchResults = [];

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

        // Búsqueda de clientes con autocompletado optimizada
        $('#clienteSearch').on('input', function() {
            const query = $(this).val().trim();

            // Limpiar timeout anterior
            clearTimeout(searchTimeout);

            // Ocultar resultados si la búsqueda está vacía
            if (query.length < 1) {
                $('#clienteSearchContainer').hide();
                $('#cliente_existente').val('');
                checkClienteSeleccionado();
                $('#cliente-info').hide();
                return;
            }

            // Mostrar loading
            $('#clienteSearchContainer').html('<div class="cliente-search-loading">Buscando clientes...</div>').show();

            // Hacer búsqueda después de 200ms de inactividad (más rápido)
            searchTimeout = setTimeout(function() {
                buscarClientes(query);
            }, 200);
        });

        // Función para buscar clientes
        function buscarClientes(query) {
            console.log('Buscando clientes con query:', query);

            $.ajax({
                url: 'index.php?route=servicios/buscar-clientes',
                method: 'GET',
                data: {
                    q: query
                },
                dataType: 'json',
                success: function(data) {
                    console.log('Respuesta de búsqueda:', data);
                    if (data.success) {
                        mostrarResultadosBusqueda(data.clientes);
                    } else {
                        mostrarErrorBusqueda('Error en la búsqueda');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en búsqueda AJAX:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    mostrarErrorBusqueda('Error de conexión');
                }
            });
        }

        // Función para mostrar resultados de búsqueda optimizada
        function mostrarResultadosBusqueda(clientes) {
            console.log('Mostrando resultados:', clientes);
            searchResults = clientes;
            const resultsContainer = $('#clienteSearchContainer');

            if (clientes.length === 0) {
                resultsContainer.html('<div class="cliente-search-no-results">No se encontraron clientes</div>');
            } else {
                let html = '<div class="cliente-search-header">Resultados de búsqueda (haga clic en cualquier resultado para seleccionar):</div>';
                if (clientes.length === 1) {
                    html += '<div class="cliente-search-single-result">Se encontró un cliente. Haga clic para seleccionarlo.</div>';
                }

                clientes.forEach((cliente, index) => {
                    html += `
                        <div class="cliente-search-result-item" data-index="${index}">
                            <div class="cliente-info">
                                <div class="cliente-nombre">${cliente.NombreCliente}</div>
                                <div class="cliente-detalles">
                                    <span class="cliente-identificacion">ID: ${cliente.NoIdentificacionCliente}</span>
                                    ${cliente.telefono ? `<span class="cliente-telefono">Tel: ${cliente.telefono}</span>` : ''}
                                </div>
                            </div>
                            <div class="cliente-action">
                                <button type="button" class="btn btn--sm btn--primary seleccionar-cliente" data-index="${index}">
                                    <i class="fas fa-check"></i> Seleccionar
                                </button>
                            </div>
                        </div>
                    `;
                });
                resultsContainer.html(html);
            }
            resultsContainer.show();
        }

        // Función para mostrar error en búsqueda
        function mostrarErrorBusqueda(mensaje) {
            $('#clienteSearchContainer').html(`<div class="cliente-search-no-results">${mensaje}</div>`).show();
        }

        // Manejar clic en resultado de búsqueda
        $(document).on('click', '.cliente-search-result-item', function() {
            const index = $(this).data('index');
            const cliente = searchResults[index];

            if (cliente) {
                seleccionarCliente(cliente);
            }
        });

        // Manejar clic en botón de seleccionar
        $(document).on('click', '.seleccionar-cliente', function(e) {
            e.stopPropagation(); // Evitar que se active el clic del item
            const index = $(this).data('index');
            const cliente = searchResults[index];

            if (cliente) {
                seleccionarCliente(cliente);
            }
        });

        // Función para seleccionar cliente optimizada con redirección directa
        function seleccionarCliente(cliente) {
            console.log('Seleccionando cliente:', cliente);

            // Mostrar mensaje de confirmación rápida
            mostrarMensajeExito('Cliente seleccionado: ' + cliente.NombreCliente + '. Redirigiendo...');

            // Redirección directa a crear servicio
            setTimeout(function() {
                window.location.href = 'index.php?route=servicios/create&cliente_id=' + cliente.NoIdentificacionCliente;
            }, 500);
        }

        // Función para mostrar información del cliente
        function mostrarInformacionCliente(cliente) {
            $('#cliente-nombre').text(cliente.NombreCliente);
            $('#cliente-identificacion').text(cliente.NoIdentificacionCliente);
            $('#cliente-telefono').text(cliente.telefono || 'No disponible');
            $('#cliente-direccion').text(cliente.direccion || 'No disponible');
            $('#cliente-info').show();
        }

        // Función para mostrar mensaje de éxito
        function mostrarMensajeExito(mensaje) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.minWidth = '300px';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);
        }

        // Manejar navegación con teclado optimizada
        $('#clienteSearch').on('keydown', function(e) {
            const resultsContainer = $('#clienteSearchContainer');
            const items = resultsContainer.find('.cliente-search-result-item');

            if (items.length === 0) return;

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    actualizarSeleccion(items);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    actualizarSeleccion(items);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (selectedIndex >= 0 && searchResults[selectedIndex]) {
                        const cliente = searchResults[selectedIndex];
                        seleccionarCliente(cliente);
                    }
                    break;
                case 'Escape':
                    resultsContainer.hide();
                    selectedIndex = -1;
                    break;
                case 'Tab':
                    // Si hay resultados y se presiona Tab, seleccionar el primero
                    if (searchResults.length > 0 && selectedIndex === -1) {
                        e.preventDefault();
                        selectedIndex = 0;
                        actualizarSeleccion(items);
                    }
                    break;
            }
        });

        // Función para actualizar selección visual
        function actualizarSeleccion(items) {
            items.removeClass('selected');
            if (selectedIndex >= 0) {
                items.eq(selectedIndex).addClass('selected');
            }
        }

        // Ocultar resultados al hacer clic fuera
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#clienteSearch, #clienteSearchContainer').length) {
                $('#clienteSearchContainer').hide();
                selectedIndex = -1;
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