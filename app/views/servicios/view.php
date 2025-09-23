        <?php
        // Verificar que las variables estén definidas
        $esTecnico = $esTecnico ?? false;
        $esAsesor = $esAsesor ?? false;
        ?>

        <div class="service-detail">

            <?php if ($esAsesor): ?>
            <!-- Mensaje informativo para asesores -->
            <div class="alert alert-warning mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-tie me-3 fa-2x"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Vista de Asesor</h5>
                        <p class="mb-0">Como asesor, puedes ver y editar servicios, pero no cambiar su estado.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Información principal -->
            <div class="service-detail__content">
                <!-- Columna principal -->
                <div class="service-detail__main">
                    <!-- Información del servicio -->
                    <div class="service-info-card">
                        <div class="service-info-card__header">
                            <div class="form-intro__wrapper">
                                <div class="service-info-card__header-left">
                                    <div class="form-intro__icon">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <h5 class="service-info-card__title">Información del Servicio</h5>
                                    <?php
                                    $statusClass = 'status-badge--warning';
                                    switch ($servicio['IdEstadoEnTaller']) {
                                        case 1:
                                            $statusClass = 'status-badge--warning';
                                            break;    // En Espera
                                        case 2:
                                            $statusClass = 'status-badge--info';
                                            break;       // En Mantenimiento
                                        case 3:
                                            $statusClass = 'status-badge--success';
                                            break;    // Terminado
                                        case 4:
                                            $statusClass = 'status-badge--danger';
                                            break;     // En Revisión
                                        case 6:
                                            $statusClass = 'status-badge--info';
                                            break;    // En Autorización
                                        case 7:
                                            $statusClass = 'status-badge--warning';
                                            break;       // Entregado No Reparado
                                        default:
                                            $statusClass = 'status-badge--warning';
                                            break;
                                    }
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= ucfirst(strtolower(htmlspecialchars($servicio['estado_descripcion'] ?? 'N/A'))) ?>
                                    </span>
                                </div>
                                <?php if (!empty($servicio['Costo']) && $servicio['Costo'] > 0): ?>
                                    <div class="service-info-card__cost">
                                        $<?= number_format($servicio['Costo'], 0, ',', '.') ?>
                                    </div>
                                <?php endif; ?>
                                <div class="service-detail__header-actions">
                                    <?php 
                                    // Mostrar botón de cambiar estado solo si:
                                    // 1. No es asesor Y no es técnico (usuarios admin, etc.) - SIEMPRE pueden cambiar estado
                                    // 2. Es técnico PERO el servicio NO está terminado (ID != 3) - técnicos NO pueden cambiar estado si está terminado
                                    $mostrarCambiarEstado = (!$esAsesor && !$esTecnico) || 
                                                          ($esTecnico && $servicio['IdEstadoEnTaller'] != 3);
                                    
                                    // Debug temporal - REMOVER DESPUÉS
                                    echo "<!-- DEBUG: esTecnico=" . ($esTecnico ? 'true' : 'false') . ", esAsesor=" . ($esAsesor ? 'true' : 'false') . ", estadoId=" . $servicio['IdEstadoEnTaller'] . ", mostrarCambiarEstado=" . ($mostrarCambiarEstado ? 'true' : 'false') . " -->";
                                    ?>
                                    
                                    <?php if ($mostrarCambiarEstado): ?>
                                    <!-- Botón de cambiar estado -->
                                    <div class="dropdown d-inline">
                                        <button class="btn btn--outline dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-exchange-alt btn__icon"></i>Cambiar Estado
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php if (!empty($estados)): ?>
                                                <?php foreach ($estados as $estado): ?>
                                                    <?php if ($estado['id'] != $servicio['IdEstadoEnTaller']): ?>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="changeStatus(<?= $servicio['IdServicio'] ?>, <?= $estado['id'] ?>, '<?= htmlspecialchars($estado['descripcion']) ?>')">
                                                                <?= ucfirst(strtolower(htmlspecialchars($estado['descripcion']))) ?>
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li><span class="dropdown-item-text text-muted">No hay estados disponibles</span></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                    <button class="btn btn--outline" onclick="editService(<?= $servicio['IdServicio'] ?>)">
                                        <i class="fas fa-edit btn__icon"></i>Editar
                                    </button>
                                    <button class="btn btn--outline" onclick="imprimirDirecto(<?= $servicio['IdServicio'] ?>)">
                                        <i class="fas fa-print btn__icon"></i>Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="service-info-card__body">
                            <div class="service-info-grid">
                                <div class="service-info__field">
                                    <label class="service-info__label">Cliente</label>
                                    <div class="service-info__input">
                                        <i class="fas fa-user service-info__icon"></i>
                                        <?= htmlspecialchars($servicio['cliente_nombre'] ?? 'Cliente no encontrado') ?>
                                    </div>
                                </div>

                                <div class="service-info__field">
                                    <label class="service-info__label">ID Cliente</label>
                                    <div class="service-info__input">
                                        <i class="fas fa-id-card service-info__icon"></i>
                                        <?= htmlspecialchars($servicio['NoIdentificacionCliente'] ?? 'No especificado') ?>
                                    </div>
                                </div>

                                <div class="service-info__field">
                                    <label class="service-info__label">Técnico</label>
                                    <div class="service-info__input">
                                        <i class="fas fa-user-cog service-info__icon"></i>
                                        <?php if (!empty($servicio['tecnico_nombre'])): ?>
                                            <?= htmlspecialchars($servicio['tecnico_nombre']) ?>
                                        <?php else: ?>
                                            Sin asignar
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="service-info__field service-info__field--two-columns">
                                    <label class="service-info__label">Equipo</label>
                                    <div class="service-info__input">
                                        <i class="fas fa-laptop service-info__icon"></i>
                                        <?= htmlspecialchars($servicio['Equipo'] ?? 'No especificado') ?>
                                    </div>
                                </div>

                                <div class="service-info__field">
                                    <label class="service-info__label">Fecha de Ingreso</label>
                                    <div class="service-info__input">
                                        <i class="fas fa-calendar-alt service-info__icon"></i>
                                        <?= DateHelper::extractDateTime($servicio['FechaIngreso']) ?>
                                    </div>
                                </div>

                                <?php if (!empty($servicio['CondicionesEntrega'])): ?>
                                    <div class="service-info__field service-info__field--full-width">
                                        <label class="service-info__label">Condiciones de Entrega</label>
                                        <div class="service-info__input service-info__input--textarea">
                                            <i class="fas fa-clipboard-list service-info__icon"></i>
                                            <?= htmlspecialchars($servicio['CondicionesEntrega']) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="service-info__field service-info__field--full-width">
                                    <label class="service-info__label">Problema Reportado</label>
                                    <div class="service-info__input service-info__input--textarea">
                                        <i class="fas fa-exclamation-triangle service-info__icon"></i>
                                        <?= htmlspecialchars($servicio['Problema'] ?? 'No se especificó problema') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Solución -->
                    <?php
                    // Debug temporal
                    echo "<!-- DEBUG: Campo Solucion = '" . htmlspecialchars($servicio['Solucion'] ?? 'NULL') . "' -->";
                    echo "<!-- DEBUG: !empty() = " . (!empty($servicio['Solucion']) ? 'TRUE' : 'FALSE') . " -->";
                    ?>
                    <?php if (!empty($servicio['Solucion'])): ?>
                        <div class="service-content-card">
                            <div class="service-content-card__header">
                                <h6 class="service-content-card__title">Solución Aplicada</h6>
                            </div>
                            <div class="service-content-card__body">
                                <div class="service-content">
                                    <?= nl2br(htmlspecialchars($servicio['Solucion'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Notas internas -->
                    <?php if (!empty($servicio['NotaInterna'])): ?>
                        <div class="service-content-card">
                            <div class="service-content-card__header">
                                <h6 class="service-content-card__title">Notas Internas</h6>
                            </div>
                            <div class="service-content-card__body">
                                <div class="service-content service-content--small">
                                    <?= nl2br(htmlspecialchars($servicio['NotaInterna'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Scripts para las acciones -->
        <script>
            // Cambiar estado del servicio
            function changeStatus(id, newStatus, estadoNombre) {
                const mensaje = `¿Está seguro de cambiar el estado de este servicio a "${estadoNombre}"?`;

                if (confirm(mensaje)) {
                    // Mostrar indicador de carga
                    const button = event.target.closest('.dropdown').querySelector('button');
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cambiando...';
                    button.disabled = true;

                    fetch(`<?= url('servicios/changeStatus/') ?>${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                estado: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mostrar mensaje de éxito
                                showSuccessMessage(`Estado cambiado exitosamente a "${estadoNombre}"`);

                                // Recargar página después de un breve delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                alert('Error: ' + data.message);
                                // Restaurar botón
                                button.innerHTML = originalText;
                                button.disabled = false;
                            }
                        })
                        .catch(error => {
                            alert('Error al cambiar el estado. Por favor intente nuevamente.');
                            console.error('Error:', error);
                            // Restaurar botón
                            button.innerHTML = originalText;
                            button.disabled = false;
                        });
                }
            }

            // Función para mostrar mensaje de éxito
            function showSuccessMessage(message) {
                // Crear alerta de éxito temporal
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

                // Auto-remover después de 3 segundos
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 3000);
            }

            // Función para editar servicio
            function editService(id) {
                window.location.href = '<?= url('servicios/edit/') ?>' + id;
            }



            // Función para imprimir directamente
            function imprimirDirecto(id) {
                // Mostrar indicador de carga
                const button = event.target.closest('.btn');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin btn__icon"></i>Generando...';
                button.disabled = true;

                // Crear iframe temporal para cargar el PDF
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = '<?= url('servicios/imprimir/') ?>' + id;

                // Cuando el iframe esté cargado, imprimir
                iframe.onload = function() {
                    try {
                        // Intentar imprimir el contenido del iframe
                        iframe.contentWindow.print();
                    } catch (e) {
                        // Si falla, imprimir toda la página
                        window.print();
                    }

                    // Limpiar el iframe después de un breve delay
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                    }, 1000);

                    // Restaurar el botón
                    button.innerHTML = originalText;
                    button.disabled = false;
                };

                // Si hay error al cargar, restaurar el botón
                iframe.onerror = function() {
                    document.body.removeChild(iframe);
                    button.innerHTML = originalText;
                    button.disabled = false;
                    alert('Error al generar el PDF. Intente nuevamente.');
                };

                // Agregar iframe al DOM
                document.body.appendChild(iframe);
            }

            // Función para imprimir el PDF (mantenida para compatibilidad)
            function imprimirPDF() {
                // Obtener el iframe del PDF
                const iframe = document.querySelector('.pdf-container iframe');
                if (iframe && iframe.contentWindow) {
                    // Ejecutar la impresión en el iframe
                    iframe.contentWindow.print();
                } else {
                    // Fallback: imprimir toda la página
                    window.print();
                }
            }
        </script>

        <style>
            /* Estilos adicionales para la vista */
            .form-control-plaintext {
                padding-left: 0;
                padding-right: 0;
            }



            @media print {

                .btn,
                .dropdown,
                nav {
                    display: none !important;
                }
            }
        </style>