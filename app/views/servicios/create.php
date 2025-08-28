<?php
// Verificar si hay mensajes flash
$flashMessage = '';
$flashType = '';
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    $flashType = $_SESSION['flash_type'] ?? 'info';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}
?>

<div class="service-detail">

    <!-- Contenido principal -->
    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Formulario de información del servicio -->
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Datos del Servicio</h3>
                                <p class="form-intro__description">Complete la información requerida para crear un nuevo servicio</p>
                            </div>
                            <div class="service-detail__header-actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <button type="button" class="btn btn--outline" id="cancelBtn">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="createServiceForm">
                                    <i class="fas fa-save btn__icon"></i>Crear Servicio
                                </button>
                            </div>
                        </div>
                    </div>
                    <form id="createServiceForm" class="service-info-grid">
                        <!-- Búsqueda de Cliente -->
                        <?php if (!isset($clienteSeleccionado)): ?>
                            <div class="service-info__field">
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
                                    <input type="hidden" id="idcliente" name="idcliente" required>
                                </div>
                                <div class="form__feedback form__feedback--invalid" id="error-idcliente"></div>
                                
                                <!-- Dropdown de resultados de búsqueda -->
                                <div id="clienteSearchResults" class="cliente-search-results" style="display: none;">
                                    <!-- Los resultados se cargarán dinámicamente aquí -->
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Información del Cliente Seleccionado -->
                        <?php if (isset($clienteSeleccionado)): ?>
                            <div class="service-info-card service-info-card--info">
                                <div class="service-info-card__header">
                                    <h4 class="service-info-card__title">
                                        <i class="fas fa-user-check service-info-card__icon"></i>
                                        Cliente
                                    </h4>
                                </div>
                                <div class="service-info-card__body">
                                    <div class="service-info-grid">
                                        <div class="service-info__field">
                                            <label class="service-info__label">Nombre Completo</label>
                                            <div class="service-info__value">
                                                <i class="fas fa-user service-info__icon"></i>
                                                <span><?= htmlspecialchars($clienteSeleccionado['NombreCliente']) ?></span>
                                            </div>
                                        </div>

                                        <div class="service-info__field">
                                            <label class="service-info__label">Identificación</label>
                                            <div class="service-info__value">
                                                <i class="fas fa-id-card service-info__icon"></i>
                                                <span><?= htmlspecialchars($clienteSeleccionado['NoIdentificacionCliente']) ?></span>
                                            </div>
                                        </div>

                                        <?php if (!empty($clienteSeleccionado['telefono'])): ?>
                                            <div class="service-info__field">
                                                <label class="service-info__label">Teléfono</label>
                                                <div class="service-info__value">
                                                    <i class="fas fa-phone service-info__icon"></i>
                                                    <span><?= htmlspecialchars($clienteSeleccionado['telefono']) ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($clienteSeleccionado['direccion'])): ?>
                                            <div class="service-info__field service-info__field--full-width">
                                                <label class="service-info__label">Dirección</label>
                                                <div class="service-info__value">
                                                    <i class="fas fa-map-marker-alt service-info__icon"></i>
                                                    <span><?= htmlspecialchars($clienteSeleccionado['direccion']) ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>


                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- ID Cliente (oculto si hay cliente pre-seleccionado) -->
                        <?php if (!isset($clienteSeleccionado)): ?>
                            <div class="service-info__field">
                                <label class="service-info__label" for="idcliente_display">
                                    ID Cliente
                                </label>
                                <div class="service-info__input">
                                    <i class="fas fa-id-card service-info__icon"></i>
                                    <input type="text" class="form__control" id="idcliente_display" readonly>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Campos ocultos para mantener los valores del cliente -->
                        <?php if (isset($clienteSeleccionado)): ?>
                            <input type="hidden" id="idcliente" name="idcliente" value="<?= htmlspecialchars($clienteSeleccionado['NoIdentificacionCliente']) ?>">
                            <input type="hidden" id="idcliente_display" value="<?= htmlspecialchars($clienteSeleccionado['NoIdentificacionCliente']) ?>">
                        <?php endif; ?>

                        <!-- Equipo -->
                        <div class="service-info__field service-info__field--two-columns">
                            <label class="service-info__label" for="equipo">
                                Equipo <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-laptop service-info__icon"></i>
                                <input type="text" class="form__control" id="equipo" name="equipo" required>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-equipo"></div>
                        </div>

                        <!-- Costo -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="costo">
                                Costo
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-dollar-sign service-info__icon"></i>
                                <input type="number" class="form__control" id="costo" name="costo" min="0" step="0.01">
                            </div>
                        </div>

                        <!-- Técnico -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="NoIdentificacionEmpleado">
                                Técnico
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user-cog service-info__icon"></i>
                                <select class="form__control" id="NoIdentificacionEmpleado" name="NoIdentificacionEmpleado">
                                    <option value="sin_asignar">Sin Asignar</option>
                                    <?php if (!empty($tecnicos)): ?>
                                        <?php foreach ($tecnicos as $tecnico): ?>
                                            <option value="<?= $tecnico['NoIdentificacionEmpleado'] ?>">
                                                <?= htmlspecialchars($tecnico['NombreEmpleado']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No hay técnicos disponibles</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-NoIdentificacionEmpleado"></div>
                        </div>

                        <!-- Tipo de Servicio -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="IdTipoServicio">
                                Tipo de Servicio <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-tools service-info__icon"></i>
                                <select class="form__control" id="IdTipoServicio" name="IdTipoServicio" required>
                                    <option value="">Seleccionar tipo</option>
                                    <?php foreach ($tiposServicio as $tipo): ?>
                                        <option value="<?= $tipo['id'] ?>" data-costo="<?= $tipo['CostoAproximado'] ?? '' ?>">
                                            <?= htmlspecialchars($tipo['descripcion']) ?>
                                            <?= !empty($tipo['CostoAproximado']) ? ' - $' . number_format($tipo['CostoAproximado'], 0, ',', '.') : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-IdTipoServicio"></div>
                        </div>

                        <!-- Estado -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="IdEstadoEnTaller">
                                Estado <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-exchange-alt service-info__icon"></i>
                                <select class="form__control" id="IdEstadoEnTaller" name="IdEstadoEnTaller" required>
                                    <option value="1">Espera</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['id'] ?>">
                                            <?= htmlspecialchars($estado['descripcion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-IdEstadoEnTaller"></div>
                        </div>

                        <!-- Condiciones de Entrega -->
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label" for="condicionesentrega">
                                Condiciones de Entrega
                            </label>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-clipboard-list service-info__icon"></i>
                                <textarea class="form__control" id="condicionesentrega" name="condicionesentrega" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Problema -->
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label" for="problema">
                                Problema Reportado <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-exclamation-triangle service-info__icon"></i>
                                <textarea class="form__control" id="problema" name="problema" rows="3" required></textarea>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-problema"></div>
                        </div>

                        <!-- Nota Interna -->
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label" for="notainterna">
                                Nota Interna
                            </label>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-sticky-note service-info__icon"></i>
                                <textarea class="form__control" id="notainterna" name="notainterna" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="fecha">
                                Fecha de Ingreso
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-calendar-alt service-info__icon"></i>
                                <input type="text" class="form__control" id="fecha" name="fecha" value="<?= date('Y-m-d H:i:s') ?>" readonly>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Verificar si jQuery está disponible
    if (typeof jQuery !== 'undefined') {
        // jQuery está disponible, ejecutar inmediatamente
        initForm();
    } else {
        // jQuery no está disponible, esperar a que se cargue
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar nuevamente si jQuery está disponible
            if (typeof jQuery !== 'undefined') {
                initForm();
            } else {
                console.error('jQuery no está disponible');
            }
        });
    }

    function initForm() {
        $(document).ready(function() {
            let formChanged = false;
            const originalFormData = {};

            // Capturar datos originales del formulario
            function captureOriginalData() {
                $('input, select, textarea').each(function() {
                    originalFormData[this.name] = $(this).val();
                });
                // También capturar el campo de búsqueda de cliente
                originalFormData['clienteSearch'] = $('#clienteSearch').val();
            }

            // Verificar si el formulario está completo y ha cambiado
            function checkFormChanges() {
                // Verificar que todos los campos requeridos estén completos
                let isComplete = true;

                // Verificar campos requeridos
                const cliente = $('#idcliente').val().trim();
                const tipoServicio = $('#IdTipoServicio').val().trim();
                const equipo = $('#equipo').val().trim();
                const problema = $('#problema').val().trim();
                const estado = $('#IdEstadoEnTaller').val().trim();

                // Debug individual de cada campo
                console.log('Field values:', {
                    cliente: cliente,
                    tipoServicio: tipoServicio,
                    equipo: equipo,
                    problema: problema,
                    estado: estado
                });

                // Verificar que todos los campos requeridos tengan valor
                if (!cliente) {
                    console.log('❌ Cliente vacío');
                    isComplete = false;
                }
                if (!tipoServicio) {
                    console.log('❌ Tipo servicio vacío');
                    isComplete = false;
                }
                if (!equipo) {
                    console.log('❌ Equipo vacío');
                    isComplete = false;
                }
                if (!problema) {
                    console.log('❌ Problema vacío');
                    isComplete = false;
                }
                if (!estado) {
                    console.log('❌ Estado vacío');
                    isComplete = false;
                }

                console.log('Form complete:', isComplete);

                // Habilitar/deshabilitar botón de descartar
                $('#discardBtn').prop('disabled', !formChanged);

                // Habilitar/deshabilitar botón de enviar
                $('#submitBtn').prop('disabled', !isComplete);

                return isComplete;
            }

            // Capturar datos originales al cargar
            captureOriginalData();

            // Inicializar campos del cliente si hay un cliente pre-seleccionado
            <?php if (isset($clienteSeleccionado)): ?>
                $(document).ready(function() {
                    const clienteId = '<?= $clienteSeleccionado['NoIdentificacionCliente'] ?>';
                    if (clienteId) {
                        // Los campos ya están en los inputs hidden, no necesitamos inicializarlos
                        console.log('Cliente pre-seleccionado:', clienteId);
                    }
                });
            <?php endif; ?>

            // Detectar cambios en el formulario
            $('input, select, textarea').on('input change', function() {
                const currentValue = $(this).val();
                const originalValue = originalFormData[this.name] || '';

                if (currentValue !== originalValue) {
                    formChanged = true;
                } else {
                    // Verificar si todos los campos han vuelto a su estado original
                    let allOriginal = true;
                    $('input, select, textarea').each(function() {
                        const current = $(this).val();
                        const original = originalFormData[this.name] || '';
                        if (current !== original) {
                            allOriginal = false;
                            return false; // break the loop
                        }
                    });
                    
                    // Verificar también el campo de búsqueda de cliente
                    const currentSearchValue = $('#clienteSearch').val();
                    const originalSearchValue = originalFormData['clienteSearch'] || '';
                    if (currentSearchValue !== originalSearchValue) {
                        allOriginal = false;
                    }
                    
                    formChanged = !allOriginal;
                }

                checkFormChanges();
            });

            // Detectar cambios en el campo de búsqueda de cliente
            $('#clienteSearch').on('input', function() {
                const currentValue = $(this).val();
                const originalValue = originalFormData['clienteSearch'] || '';
                
                if (currentValue !== originalValue) {
                    formChanged = true;
                } else {
                    // Verificar si todos los campos han vuelto a su estado original
                    let allOriginal = true;
                    $('input, select, textarea').each(function() {
                        const current = $(this).val();
                        const original = originalFormData[this.name] || '';
                        if (current !== original) {
                            allOriginal = false;
                            return false; // break the loop
                        }
                    });
                    
                    if (currentValue !== originalValue) {
                        allOriginal = false;
                    }
                    
                    formChanged = !allOriginal;
                }

                checkFormChanges();
            });

            // Funcionalidad de búsqueda de clientes
            let searchTimeout;
            let selectedIndex = -1;
            let searchResults = [];

            // Búsqueda de clientes con autocompletado
            $('#clienteSearch').on('input', function() {
                const query = $(this).val().trim();
                
                // Limpiar timeout anterior
                clearTimeout(searchTimeout);
                
                // Ocultar resultados si la búsqueda está vacía
                if (query.length < 2) {
                    $('#clienteSearchResults').hide();
                    $('#idcliente').val('');
                    checkFormChanges();
                    return;
                }

                // Mostrar loading
                $('#clienteSearchResults').html('<div class="cliente-search-loading">Buscando clientes...</div>').show();

                // Hacer búsqueda después de 300ms de inactividad
                searchTimeout = setTimeout(function() {
                    buscarClientes(query);
                }, 300);
            });

            // Función para buscar clientes
            function buscarClientes(query) {
                fetch(`<?= url('servicios/buscar-clientes') ?>?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mostrarResultadosBusqueda(data.clientes);
                        } else {
                            mostrarErrorBusqueda('Error en la búsqueda');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        mostrarErrorBusqueda('Error de conexión');
                    });
            }

            // Función para mostrar resultados de búsqueda
            function mostrarResultadosBusqueda(clientes) {
                searchResults = clientes;
                const resultsContainer = $('#clienteSearchResults');
                
                if (clientes.length === 0) {
                    resultsContainer.html('<div class="cliente-search-no-results">No se encontraron clientes</div>');
                } else {
                    let html = '';
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
                            </div>
                        `;
                    });
                    resultsContainer.html(html);
                }
                resultsContainer.show();
            }

            // Función para mostrar error en búsqueda
            function mostrarErrorBusqueda(mensaje) {
                $('#clienteSearchResults').html(`<div class="cliente-search-no-results">${mensaje}</div>`).show();
            }

            // Manejar clic en resultado de búsqueda
            $(document).on('click', '.cliente-search-result-item', function() {
                const index = $(this).data('index');
                const cliente = searchResults[index];
                
                if (cliente) {
                    // Llenar el campo de búsqueda con el nombre del cliente
                    $('#clienteSearch').val(cliente.NombreCliente);
                    
                    // Guardar el ID del cliente en el campo oculto
                    $('#idcliente').val(cliente.NoIdentificacionCliente);
                    
                    // Ocultar resultados
                    $('#clienteSearchResults').hide();
                    
                    // Verificar cambios en el formulario
                    checkFormChanges();
                }
            });

            // Manejar navegación con teclado
            $('#clienteSearch').on('keydown', function(e) {
                const resultsContainer = $('#clienteSearchResults');
                const items = resultsContainer.find('.cliente-search-result-item');
                
                if (items.length === 0) return;

                switch(e.key) {
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
                            $('#clienteSearch').val(cliente.NombreCliente);
                            $('#idcliente').val(cliente.NoIdentificacionCliente);
                            resultsContainer.hide();
                            checkFormChanges();
                        }
                        break;
                    case 'Escape':
                        resultsContainer.hide();
                        selectedIndex = -1;
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
                if (!$(e.target).closest('#clienteSearch, #clienteSearchResults').length) {
                    $('#clienteSearchResults').hide();
                    selectedIndex = -1;
                }
            });

            // Botón descartar cambios
            $('#discardBtn').on('click', function() {
                if (confirm('¿Está seguro de descartar todos los cambios?')) {
                    // Restaurar valores originales
                    $('input, select, textarea').each(function() {
                        const originalValue = originalFormData[this.name] || '';
                        $(this).val(originalValue);
                    });
                    
                    // Restaurar campo de búsqueda de cliente
                    $('#clienteSearch').val(originalFormData['clienteSearch'] || '');
                    
                    formChanged = false;
                    checkFormChanges();

                    // Limpiar errores
                    $('.form__feedback--invalid').text('');
                    $('.form__control--invalid').removeClass('form__control--invalid');
                    
                    // Ocultar resultados de búsqueda
                    $('#clienteSearchResults').hide();
                }
            });

            // Botón cancelar
            $('#cancelBtn').on('click', function() {
                if (confirm('¿Está seguro de cancelar? Se perderán todos los cambios.')) {
                    window.location.href = '<?= url('servicios') ?>';
                }
            });



            // Envío del formulario
            $('#createServiceForm').on('submit', function(e) {
                e.preventDefault();

                // Validar formulario
                const isComplete = checkFormChanges();
                if (!isComplete) {
                    alert('Por favor complete todos los campos requeridos.');
                    return;
                }

                // Mostrar indicador de carga
                const submitBtn = $('#submitBtn');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin btn__icon"></i>Creando...');
                submitBtn.prop('disabled', true);

                // Recopilar datos del formulario
                const formData = {
                    idcliente: $('#idcliente').val(),
                    NoIdentificacionEmpleado: $('#NoIdentificacionEmpleado').val(),
                    IdTipoServicio: $('#IdTipoServicio').val(),
                    equipo: $('#equipo').val(),
                    condicionesentrega: $('#condicionesentrega').val(),
                    problema: $('#problema').val(),
                    notainterna: $('#notainterna').val(),
                    costo: $('#costo').val() || null,
                    IdEstadoEnTaller: $('#IdEstadoEnTaller').val(),
                    fecha: $('#fecha').val()
                };

                // Enviar datos
                fetch('<?= url('servicios/store') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mostrar mensaje de éxito
                            showSuccessMessage('Servicio creado exitosamente');

                            // Redirigir a la lista de servicios
                            setTimeout(() => {
                                window.location.href = '<?= url('servicios') ?>';
                            }, 1500);
                        } else {
                            // Mostrar errores
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    const errorElement = $(`#error-${field}`);
                                    const inputElement = $(`#${field}`);

                                    if (errorElement.length && inputElement.length) {
                                        errorElement.text(data.errors[field]);
                                        inputElement.addClass('form__control--invalid');
                                    }
                                });
                            } else {
                                showErrorMessage(data.message || 'Error al crear el servicio');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showErrorMessage('Error de conexión. Por favor intente nuevamente.');
                    })
                    .finally(() => {
                        // Restaurar botón
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    });
            });

            // Verificación inicial
            checkFormChanges();

            // Auto-completar costo cuando se selecciona un tipo de servicio
            $('#IdTipoServicio').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const costoAproximado = selectedOption.data('costo');

                // Si hay un costo aproximado y el campo costo está vacío, auto-completarlo
                if (costoAproximado && !$('#costo').val()) {
                    $('#costo').val(costoAproximado);
                    console.log('Costo auto-completado:', costoAproximado);
                }
            });
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
</script>