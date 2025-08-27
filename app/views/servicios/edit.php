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
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="form-intro__content">
                                        <h3 class="form-intro__title">Editar Servicio</h3>
                                        <p class="form-intro__description">Modifique la información del servicio según sea necesario</p>
                                    </div>

                                    <div class="form-intro__actions">
                                        <div class="service-detail__header-actions">
                                            <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                                <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                            </button>
                                            <button type="button" class="btn btn--outline" id="cancelBtn">
                                                <i class="fas fa-ban btn__icon"></i>Cancelar
                                            </button>
                                            <button type="submit" class="btn btn--primary" id="submitBtn" form="editServiceForm">
                                                <i class="fas fa-save btn__icon"></i>Actualizar Servicio
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            // Verificar el perfil del usuario
                            $perfilUsuario = $_SESSION['usuario_perfil_nombre'] ?? '';
                            $esTecnico = !empty($perfilUsuario) &&
                                       strtolower($perfilUsuario) === 'tecnico';
                            $esAsesor = !empty($perfilUsuario) &&
                                       strtolower($perfilUsuario) === 'asesor';

                            ?>



                            <?php if ($esTecnico): ?>
                            <!-- Mensaje informativo para técnicos -->
                            <div class="service-info-card service-info-card--info">
                                <div class="service-info-card__header">
                                    <h4 class="service-info-card__title">
                                        <i class="fas fa-info-circle service-info-card__icon"></i>
                                        Información para Técnicos
                                    </h4>
                                </div>
                                <div class="service-info-card__body">
                                    <div class="service-info-grid">
                                        <div class="service-info__field">
                                            <div class="service-info__value">
                                                <i class="fas fa-lock service-info__icon"></i>
                                                <span>Como técnico, puedes editar: "Estado", "Solución Aplicada" y "Nota Interna"</span>
                                            </div>
                                        </div>
                                        <div class="service-info__field">
                                            <div class="service-info__value">
                                                <i class="fas fa-edit service-info__icon"></i>
                                                <span>Los demás campos están bloqueados para mantener la integridad del servicio</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($esAsesor): ?>
                            <!-- Mensaje informativo para asesores -->
                            <div class="service-info-card service-info-card--info">
                                <div class="service-info-card__header">
                                    <h4 class="service-info-card__title">
                                        <i class="fas fa-info-circle service-info-card__icon"></i>
                                        Información para Asesores
                                    </h4>
                                </div>
                                <div class="service-info-card__body">
                                    <div class="service-info-grid">
                                        <div class="service-info__field">
                                            <div class="service-info__value">
                                                <i class="fas fa-lock service-info__icon"></i>
                                                <span>Como asesor, puedes editar únicamente: "Nota Interna" (los demás campos están bloqueados)</span>
                                            </div>
                                        </div>
                                        <div class="service-info__field">
                                            <div class="service-info__value">
                                                <i class="fas fa-edit service-info__icon"></i>
                                                <span>Los demás campos están bloqueados para mantener la integridad del servicio</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <form id="editServiceForm" class="service-info-grid">
                                <!-- Campos ocultos para valores deshabilitados -->
                                <?php if ($esTecnico): ?>
                                    <input type="hidden" name="idcliente" value="<?= htmlspecialchars($servicio['NoIdentificacionCliente'] ?? '') ?>">
                                    <input type="hidden" name="NoIdentificacionEmpleado" value="<?= htmlspecialchars($servicio['NoIdentificacionEmpleado'] ?? '') ?>">
                                    <input type="hidden" name="equipo" value="<?= htmlspecialchars($servicio['Equipo'] ?? '') ?>">
                                    <input type="hidden" name="IdTipoServicio" value="<?= htmlspecialchars($servicio['IdTipoServicio'] ?? '') ?>">
                                    <input type="hidden" name="costo" value="<?= htmlspecialchars($servicio['Costo'] ?? '') ?>">
                                    <input type="hidden" name="condicionesentrega" value="<?= htmlspecialchars($servicio['CondicionesEntrega'] ?? '') ?>">
                                    <input type="hidden" name="problema" value="<?= htmlspecialchars($servicio['Problema'] ?? '') ?>">
                                    <input type="hidden" name="solucion" value="<?= htmlspecialchars($servicio['Solucion'] ?? '') ?>">
                                <?php endif; ?>

                                <?php if ($esAsesor): ?>
                                    <input type="hidden" name="idcliente" value="<?= htmlspecialchars($servicio['NoIdentificacionCliente'] ?? '') ?>">
                                    <input type="hidden" name="NoIdentificacionEmpleado" value="<?= htmlspecialchars($servicio['NoIdentificacionEmpleado'] ?? '') ?>">
                                    <input type="hidden" name="equipo" value="<?= htmlspecialchars($servicio['Equipo'] ?? '') ?>">
                                    <input type="hidden" name="IdTipoServicio" value="<?= htmlspecialchars($servicio['IdTipoServicio'] ?? '') ?>">
                                    <input type="hidden" name="costo" value="<?= htmlspecialchars($servicio['Costo'] ?? '') ?>">
                                    <input type="hidden" name="condicionesentrega" value="<?= htmlspecialchars($servicio['CondicionesEntrega'] ?? '') ?>">
                                    <input type="hidden" name="problema" value="<?= htmlspecialchars($servicio['Problema'] ?? '') ?>">
                                    <input type="hidden" name="solucion" value="<?= htmlspecialchars($servicio['Solucion'] ?? '') ?>">
                                    <input type="hidden" name="IdEstadoEnTaller" value="<?= htmlspecialchars($servicio['IdEstadoEnTaller'] ?? '') ?>">
                                <?php endif; ?>

                                <!-- Cliente -->
                                <div class="service-info__field">
                                    <label class="service-info__label" for="idcliente">
                                        Cliente <span class="form__required">*</span>
                                    </label>
                                    <div class="service-info__input">
                                        <i class="fas fa-user service-info__icon"></i>
                                        <select class="form__control" id="idcliente" name="idcliente" required disabled>
                                            <option value="">Seleccionar cliente</option>
                                            <?php foreach ($clientes as $cliente): ?>
                                                <option value="<?= $cliente['NoIdentificacionCliente'] ?>" <?= $cliente['NoIdentificacionCliente'] == $servicio['NoIdentificacionCliente'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cliente['NombreCliente']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form__feedback form__feedback--invalid" id="error-idcliente"></div>
                                </div>

                                <!-- ID Cliente -->
                                <div class="service-info__field">
                                    <label class="service-info__label" for="idcliente_display">
                                        ID Cliente
                                    </label>
                                    <div class="service-info__input">
                                        <i class="fas fa-id-card service-info__icon"></i>
                                        <input type="text" class="form__control" id="idcliente_display" value="<?= htmlspecialchars($servicio['NoIdentificacionCliente'] ?? '') ?>" readonly disabled>
                                        <!-- Campo oculto para enviar el ID del cliente -->
                                        <input type="hidden" name="idcliente" value="<?= htmlspecialchars($servicio['NoIdentificacionCliente'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- Técnico -->
                                <div class="service-info__field">
                                    <label class="service-info__label" for="NoIdentificacionEmpleado">
                                        Técnico
                                    </label>
                                    <div class="service-info__input">
                                        <i class="fas fa-user-cog service-info__icon"></i>
                                        <select class="form__control" id="NoIdentificacionEmpleado" name="NoIdentificacionEmpleado"
                                                <?= ($esTecnico || $esAsesor) ? 'disabled' : '' ?>>
                                            <option value="">Seleccionar técnico</option>
                                            <?php if (!empty($tecnicos)): ?>
                                                <?php foreach ($tecnicos as $tecnico): ?>
                                                    <option value="<?= $tecnico['NoIdentificacionEmpleado'] ?>" <?= $tecnico['NoIdentificacionEmpleado'] == $servicio['NoIdentificacionEmpleado'] ? 'selected' : '' ?>>
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

                                <!-- Equipo -->
                                <div class="service-info__field service-info__field--two-columns">
                                    <label class="service-info__label" for="equipo">
                                        Equipo <span class="form__required">*</span>
                                    </label>
                                    <div class="service-info__input">
                                        <i class="fas fa-laptop service-info__icon"></i>
                                        <input type="text" class="form__control" id="equipo" name="equipo"
                                            value="<?= htmlspecialchars($servicio['Equipo'] ?? '') ?>"
                                            placeholder="Ej: Laptop HP, iPhone 12, etc."
                                            <?= ($esTecnico || $esAsesor) ? 'disabled' : '' ?> required>
                                    </div>
                                    <div class="form__feedback form__feedback--invalid" id="error-equipo"></div>
                                </div>

                                <!-- Tipo de Servicio -->
                                <div class="service-info__field">
                                    <label class="service-info__label" for="IdTipoServicio">
                                        Tipo de Servicio <span class="form__required">*</span>
                                    </label>
                                    <div class="service-info__input">
                                        <i class="fas fa-tools service-info__icon"></i>
                                        <select class="form__control" id="IdTipoServicio" name="IdTipoServicio"
                                                <?= ($esTecnico || $esAsesor) ? 'disabled' : '' ?> required>
                                            <option value="">Seleccionar tipo</option>
                                            <?php foreach ($tiposServicio as $tipo): ?>
                                                <option value="<?= $tipo['id'] ?>"
                                                    data-costo="<?= $tipo['CostoAproximado'] ?>"
                                                    <?= $tipo['id'] == $servicio['IdTipoServicio'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($tipo['descripcion']) ?>
                                                    <?= $tipo['CostoAproximado'] ? ' - $' . number_format($tipo['CostoAproximado'], 0, ',', '.') : '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form__feedback form__feedback--invalid" id="error-IdTipoServicio"></div>
                                </div>

                                <!-- Fecha -->
                                <div class="service-info__field">
                                    <label class="service-info__label" for="fecha">
                                        Fecha de Ingreso
                                    </label>
                                    <div class="service-info__input">
                                        <i class="fas fa-calendar-alt service-info__icon"></i>
                                        <input type="text" class="form__control" id="fecha" value="<?= DateHelper::extractDateTime($servicio['FechaIngreso']) ?>" readonly>
                                    </div>
                                </div>

                                <!-- Costo -->
                                <div class="service-info__field">
                                    <label class="service-info__label" for="costo">
                                        Costo
                                    </label>
                                    <div class="service-info__input">
                                        <i class="fas fa-dollar-sign service-info__icon"></i>
                                        <input type="number" class="form__control" id="costo" name="costo"
                                            value="<?= htmlspecialchars($servicio['Costo'] ?? '') ?>"
                                            placeholder="0" min="0" step="1000"
                                            <?= ($esTecnico || $esAsesor) ? 'disabled' : '' ?>>
                                    </div>
                                    <div class="form__feedback form__feedback--invalid" id="error-costo"></div>
                                </div>

                                <!-- Estado -->
                                <div class="service-info__field">
                                    <label class="service-info__label" for="IdEstadoEnTaller">
                                        Estado <span class="form__required">*</span>
                                    </label>
                                    <div class="service-info__input">
                                        <i class="fas fa-exchange-alt service-info__icon"></i>
                                        <select class="form__control" id="IdEstadoEnTaller" name="IdEstadoEnTaller"
                                                <?= $esAsesor ? 'disabled' : '' ?> required>
                                            <?php foreach ($estados as $estado): ?>
                                                <option value="<?= $estado['id'] ?>" <?= $estado['id'] == $servicio['IdEstadoEnTaller'] ? 'selected' : '' ?>>
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
                                        <textarea class="form__control" id="condicionesentrega" name="condicionesentrega" rows="3"
                                            placeholder="Condiciones especiales de entrega del equipo..."
                                            <?= ($esTecnico || $esAsesor) ? 'disabled' : '' ?>><?= htmlspecialchars($servicio['CondicionesEntrega'] ?? '') ?></textarea>
                                    </div>
                                    <div class="form__feedback form__feedback--invalid" id="error-condicionesentrega"></div>
                                </div>

                                <!-- Problema -->
                                <div class="service-info__field service-info__field--full-width">
                                    <label class="service-info__label" for="problema">
                                        Problema Reportado <span class="form__required">*</span>
                                    </label>
                                    <div class="service-info__input service-info__input--textarea">
                                        <i class="fas fa-exclamation-triangle service-info__icon"></i>
                                        <textarea class="form__control" id="problema" name="problema" rows="3"
                                            placeholder="Describe el problema del equipo..."
                                            <?= ($esTecnico || $esAsesor) ? 'disabled' : '' ?> required><?= htmlspecialchars($servicio['Problema'] ?? '') ?></textarea>
                                    </div>
                                    <div class="form__feedback form__feedback--invalid" id="error-problema"></div>
                                </div>

                                <!-- Solución -->
                                <div class="service-info__field service-info__field--full-width">
                                    <label class="service-info__label" for="solucion">
                                        Solución Aplicada
                                    </label>
                                    <div class="service-info__input service-info__input--textarea">
                                        <i class="fas fa-tools service-info__icon"></i>
                                        <textarea class="form__control" id="solucion" name="solucion" rows="4"
                                            placeholder="Describe la solución aplicada al problema..."
                                            <?= $esAsesor ? 'disabled' : '' ?>><?= htmlspecialchars($servicio['Solucion'] ?? '') ?></textarea>
                                    </div>
                                    <div class="form__feedback form__feedback--invalid" id="error-solucion"></div>
                                </div>

                                <!-- Nota Interna -->
                                <div class="service-info__field service-info__field--full-width">
                                    <label class="service-info__label" for="notainterna">
                                        Nota Interna
                                    </label>
                                    <div class="service-info__input service-info__input--textarea">
                                        <i class="fas fa-sticky-note service-info__icon"></i>
                                        <textarea class="form__control" id="notainterna" name="notainterna" rows="3"
                                            placeholder="Notas internas para el equipo técnico (no visible para el cliente)..."><?= htmlspecialchars($servicio['NotaInterna'] ?? '') ?></textarea>
                                    </div>
                                    <div class="form__feedback form__feedback--invalid" id="error-notainterna"></div>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>

        <style>
            /* Estilos para campos deshabilitados */
            .form__control:disabled {
                background-color: #f8f9fa !important;
                color: #6c757d !important;
                cursor: not-allowed !important;
                opacity: 0.7;
            }

            .form__control:disabled+.service-info__icon {
                color: #6c757d !important;
            }

            /* Estilo especial para el dropdown deshabilitado */
            select.form__control:disabled {
                background-image: none !important;
            }
        </style>

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

                        // Debug: Mostrar datos originales capturados
                        console.log('📋 Datos originales capturados:', originalFormData);
                        console.log('📋 Nota Interna original:', originalFormData['notainterna']);
                    }

                    // Verificar si el formulario está completo y ha cambiado (SIMPLIFICADA)
                    function checkFormChanges() {
                        // HABILITAR EL BOTÓN INMEDIATAMENTE PARA PRUEBAS
                        $('#submitBtn').prop('disabled', false);
                        $('#discardBtn').prop('disabled', false);

                        console.log('✅ Botón habilitado para pruebas');
                        console.log('✅ Formulario listo para enviar');
                    }

                    // Función para forzar la verificación (disponible globalmente)
                    window.forceCheck = function() {
                        checkFormChanges();
                    };

                    // Capturar datos originales al cargar
                    captureOriginalData();

                    // Verificación inicial del estado del botón
                    setTimeout(function() {
                        checkFormChanges();
                        console.log('Initial form state checked');
                    }, 100);

                    // Escuchar cambios en el formulario
                    $('input, select, textarea').on('input change', function() {
                        console.log('Campo cambiado:', $(this).attr('id'), $(this).val());
                        checkFormChanges();
                    });

                    // Evento específico para dropdowns
                    $('select').on('change', function() {
                        console.log('Select changed:', $(this).attr('id'), $(this).val());
                        checkFormChanges();
                    });

                    // Evento específico para inputs de texto
                    $('input[type="text"], textarea').on('input', function() {
                        console.log('Input changed:', $(this).attr('id'), $(this).val());
                        checkFormChanges();
                    });

                    // Eventos específicos para campos editables por técnicos
                    const esTecnico = <?= $esTecnico ? 'true' : 'false' ?>;
                    const esAsesor = <?= $esAsesor ? 'true' : 'false' ?>;

                    if (esTecnico) {
                        // Evento para Estado (dropdown)
                        $('#IdEstadoEnTaller').on('change', function() {
                            console.log('Estado cambiado por técnico:', $(this).val());
                            checkFormChanges();
                        });

                        // Evento para Solución (textarea)
                        $('#solucion').on('input', function() {
                            console.log('Solución cambiada por técnico:', $(this).val());
                            checkFormChanges();
                        });

                        // Evento para Nota Interna (textarea)
                        $('#notainterna').on('input', function() {
                            console.log('Nota interna cambiada por técnico:', $(this).val());
                            checkFormChanges();
                        });
                    }

                    // Eventos específicos para campos editables por asesores
                    if (esAsesor) {
                        // Evento para Nota Interna (textarea) - único campo editable para asesores
                        $('#notainterna').on('input', function() {
                            console.log('Nota interna cambiada por asesor:', $(this).val());
                            checkFormChanges();
                        });
                    }

                    // Auto-completar costo cuando se selecciona tipo de servicio
                    $('#IdTipoServicio').on('change', function() {
                        const selectedOption = $(this).find('option:selected');
                        const costoAproximado = selectedOption.data('costo');

                        if (costoAproximado && !$('#costo').val()) {
                            $('#costo').val(costoAproximado);
                            console.log('Costo auto-completado:', costoAproximado);
                        }
                        checkFormChanges(); // Re-ejecutar validación
                    });

                    // Nota: El dropdown de cliente está deshabilitado en editar
                    // No se necesita actualizar el ID Cliente automáticamente
                    // El valor se mantiene fijo desde el servidor

                    // Validación en tiempo real
                    $('.form__control').on('blur', function() {
                        validateField($(this));
                        checkFormChanges(); // Re-ejecutar validación completa
                    });

                    // Validación adicional en tiempo real
                    $('.form__control').on('input', function() {
                        checkFormChanges(); // Re-ejecutar validación completa en cada input
                    });

                    function validateField($field) {
                        const field = $field[0];
                        const value = field.value.trim();

                        // Remover clases previas
                        $field.removeClass('form__control--valid form__control--invalid');

                        // Validar campo requerido
                        if (field.hasAttribute('required') && !value) {
                            $field.addClass('form__control--invalid');
                            return false;
                        }

                        // Validaciones específicas
                        if (field.id === 'equipo' && value && value.length < 3) {
                            $field.addClass('form__control--invalid');
                            return false;
                        }

                        if (field.id === 'problema' && value && value.length < 10) {
                            $field.addClass('form__control--invalid');
                            return false;
                        }

                        // Si pasa todas las validaciones
                        if (value) {
                            $field.addClass('form__control--valid');
                        }

                        return true;
                    }

                    // Manejar envío del formulario
                    $('#editServiceForm').on('submit', function(e) {
                        console.log('🎯 Evento submit disparado');
                        e.preventDefault();

                        console.log('🚀 Formulario enviado - Iniciando actualización');
                        console.log('📋 URL de destino:', '<?= url('servicios/edit/' . $servicio['IdServicio']) ?>');
                        console.log('📋 Datos del formulario:', $(this).serialize());

                        // Validar todos los campos
                        let isValid = true;
                        $('.form__control[required]').each(function() {
                            if (!validateField($(this))) {
                                isValid = false;
                            }
                        });

                        if (!isValid) {
                            console.log('❌ Validación fallida');
                            Swal.fire('Error', 'Por favor completa todos los campos requeridos correctamente', 'error');
                            return;
                        }

                        console.log('✅ Validación exitosa - Enviando datos');

                        // Deshabilitar botón
                        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>Actualizando...');

                        $.ajax({
                            url: '<?= url('servicios/edit/' . $servicio['IdServicio']) ?>',
                            method: 'POST',
                            data: $(this).serialize(),
                            dataType: 'json',
                            beforeSend: function() {
                                console.log('📤 Enviando petición AJAX...');
                            },
                            success: function(response) {
                                console.log('✅ Respuesta exitosa:', response);
                                if (response.success) {
                                    Swal.fire({
                                        title: '¡Servicio Actualizado!',
                                        text: 'El servicio se ha actualizado exitosamente',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = '<?= url('servicios') ?>';
                                    });
                                } else {
                                    console.log('❌ Error en respuesta:', response.message);
                                    Swal.fire('Error', response.message || 'Error al actualizar el servicio', 'error');
                                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save"></i>Actualizar Servicio');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('❌ Error AJAX completo:');
                                console.error('  - Status:', status);
                                console.error('  - Error:', error);
                                console.error('  - Response Text:', xhr.responseText);
                                console.error('  - Status Code:', xhr.status);

                                Swal.fire('Error', 'Error de conexión: ' + error, 'error');
                                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save"></i>Actualizar Servicio');
                            }
                        });
                    });

                    // Manejar clic en el botón de actualizar
                    $('#submitBtn').on('click', function(e) {
                        console.log('🎯 Botón Actualizar clickeado');
                        console.log('📋 Botón habilitado:', !$(this).prop('disabled'));
                        console.log('📋 Formulario válido:', $('#editServiceForm')[0].checkValidity());
                    });

                    // Manejar botón cancelar
                    $('#cancelBtn').on('click', function() {
                        Swal.fire({
                            title: '¿Cancelar?',
                            text: '¿Estás seguro de que quieres cancelar? Se perderán todos los cambios realizados.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, cancelar',
                            cancelButtonText: 'Continuar editando'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '<?= url('servicios') ?>';
                            }
                        });
                    });

                    // Manejar botón descartar cambios
                    $('#discardBtn').on('click', function() {
                        Swal.fire({
                            title: '¿Descartar cambios?',
                            text: '¿Estás seguro de que quieres descartar todos los cambios?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, descartar',
                            cancelButtonText: 'Continuar editando'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Resetear formulario a los datos originales
                                location.reload();
                            }
                        });
                    });
                });
            }
        </script>