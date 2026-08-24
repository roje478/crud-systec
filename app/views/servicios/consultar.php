<?php
// Verificar que las variables estén definidas
$servicios = $servicios ?? [];
$estados = $estados ?? [];
$tecnicos = $tecnicos ?? [];
$tiposServicio = $tiposServicio ?? [];
$filtros = $filtros ?? [];
$totalServicios = $totalServicios ?? 0;
$esTecnico = $esTecnico ?? false;
$esTecnicoAdministrador = $esTecnicoAdministrador ?? false;
$esAsesor = $esAsesor ?? false;
?>

<?php if ($esTecnico): ?>
    <!-- Mensaje informativo para técnicos -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-cog me-3 fa-2x"></i>
            <div>
                <h5 class="alert-heading mb-1">Consulta de Técnico</h5>
                <p class="mb-0">Solo se muestran los servicios asignados a tu perfil técnico.</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($esTecnicoAdministrador): ?>
    <!-- Mensaje informativo para técnicos administradores -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-shield me-3 fa-2x"></i>
            <div>
                <h5 class="alert-heading mb-1">Consulta de Técnico Administrador</h5>
                <p class="mb-0">Solo se muestran los servicios asignados a tu perfil técnico administrador.</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($esAsesor): ?>
    <!-- Mensaje informativo para asesores -->
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-tie me-3 fa-2x"></i>
            <div>
                <h5 class="alert-heading mb-1">Consulta de Asesor</h5>
                <p class="mb-0">Como asesor, puedes consultar todos los servicios con filtros avanzados.</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Formulario de Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-filter me-2"></i>Filtros de Consulta
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= url('servicios/consultar') ?>" id="filtrosForm">
            <div class="row g-3">

                <!-- Cliente -->
                <div class="col-md-4">
                    <label for="cliente_id" class="form-label">Cliente (ID)</label>
                    <input type="text" class="form-control" id="cliente_id" name="cliente_id"
                        placeholder="Ingrese ID del cliente"
                        value="<?= htmlspecialchars($filtros['cliente_id']) ?>">
                </div>

                <!-- Nombre del Cliente -->
                <div class="col-md-8">
                    <label for="cliente_nombre" class="form-label">Nombre del Cliente</label>
                    <input type="text" class="form-control" id="cliente_nombre" name="cliente_nombre"
                        placeholder="Ej: Juan Pérez"
                        value="<?= htmlspecialchars($filtros['cliente_nombre']) ?>">
                </div>


                <!-- ID del Servicio -->
                <div class="col-md-4">
                    <label for="servicio_id" class="form-label">ID del Servicio</label>
                    <input type="number" class="form-control" id="servicio_id" name="servicio_id"
                        placeholder="Ej: 12345"
                        value="<?= htmlspecialchars($filtros['servicio_id']) ?>">
                </div>
                <!-- Rango de Fechas -->
                <div class="col-md-4">
                    <label for="fecha_desde" class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                        value="<?= htmlspecialchars($filtros['fecha_desde']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                        value="<?= htmlspecialchars($filtros['fecha_hasta']) ?>">
                </div>

                <!-- Técnico -->
                <div class="col-md-4">
                    <label for="tecnico_id" class="form-label">Técnico</label>
                    <?php if ($esTecnico && !$esTecnicoAdministrador): ?>
                        <!-- Campo deshabilitado para técnicos regulares -->
                        <select class="form-select" id="tecnico_id" name="tecnico_id" disabled>
                            <option value="">Solo mis servicios</option>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Como técnico, solo puedes ver tus servicios asignados
                        </small>
                    <?php else: ?>
                        <!-- Campo habilitado para otros perfiles -->
                        <select class="form-select" id="tecnico_id" name="tecnico_id">
                            <option value="">Todos los técnicos</option>
                            <?php foreach ($tecnicos as $tecnico): ?>
                                <option value="<?= $tecnico['NoIdentificacionEmpleado'] ?>"
                                    <?= $filtros['tecnico_id'] == $tecnico['NoIdentificacionEmpleado'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tecnico['NombreEmpleado']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Estado -->
                <div class="col-md-4">
                    <label for="estado_id" class="form-label">Estado</label>
                    <select class="form-select" id="estado_id" name="estado_id">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?= $estado['id'] ?>"
                                <?= $filtros['estado_id'] == $estado['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($estado['descripcion']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tipo de Servicio -->
                <div class="col-md-4">
                    <label for="tipo_servicio_id" class="form-label">Tipo de Servicio</label>
                    <select class="form-select" id="tipo_servicio_id" name="tipo_servicio_id">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tiposServicio as $tipo): ?>
                            <option value="<?= $tipo['id'] ?>"
                                <?= $filtros['tipo_servicio_id'] == $tipo['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipo['descripcion']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>





                <!-- Equipo -->
                <div class="col-md-6">
                    <label for="equipo" class="form-label">Equipo</label>
                    <input type="text" class="form-control" id="equipo" name="equipo"
                        placeholder="Buscar por equipo"
                        value="<?= htmlspecialchars($filtros['equipo']) ?>">
                </div>

                <!-- Problema -->
                <div class="col-md-6">
                    <label for="problema" class="form-label">Problema</label>
                    <input type="text" class="form-control" id="problema" name="problema"
                        placeholder="Buscar por problema"
                        value="<?= htmlspecialchars($filtros['problema']) ?>">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Consultar
                    </button>
                    <a href="<?= url('servicios/consultar') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Limpiar Filtros
                    </a>
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#columnasModal">
                        <i class="fas fa-columns me-2"></i>Seleccionar Columnas
                    </button>
                    <?php if ($totalServicios > 0): ?>
                        <button type="button" class="btn btn-success" onclick="exportarResultados()">
                            <i class="fas fa-download me-2"></i>Exportar Resultados
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Seleccionar Columnas -->
<div class="modal fade" id="columnasModal" tabindex="-1" aria-labelledby="columnasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnasModalLabel">
                    <i class="fas fa-columns me-2"></i>Seleccionar Columnas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Selecciona las columnas que deseas mostrar en la tabla y exportar:</p>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="0" id="col_id" checked>
                    <label class="form-check-label fw-bold" for="col_id">#</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="1" id="col_cliente" checked>
                    <label class="form-check-label" for="col_cliente">Cliente</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="2" id="col_equipo" checked>
                    <label class="form-check-label" for="col_equipo">Equipo</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="3" id="col_problema" checked>
                    <label class="form-check-label" for="col_problema">Problema</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="4" id="col_solucion" checked>
                    <label class="form-check-label" for="col_solucion">Solución</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="5" id="col_estado" checked>
                    <label class="form-check-label" for="col_estado">Estado</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="6" id="col_tecnico" checked>
                    <label class="form-check-label" for="col_tecnico">Técnico</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="7" id="col_tipo_servicio" checked>
                    <label class="form-check-label" for="col_tipo_servicio">Tipo Servicio</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="8" id="col_fecha" checked>
                    <label class="form-check-label" for="col_fecha">Fecha Ingreso</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="9" id="col_costo" checked>
                    <label class="form-check-label" for="col_costo">Costo</label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="10" id="col_acciones" checked disabled>
                    <label class="form-check-label" for="col_acciones">Acciones <small class="text-muted">(Siempre visible)</small></label>
                </div>
                
                <hr class="my-3">
                
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodasColumnas()">
                        <i class="fas fa-check-double me-1"></i>Seleccionar Todas
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodasColumnas()">
                        <i class="fas fa-times me-1"></i>Deseleccionar Todas
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="aplicarColumnas()" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i>Aplicar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Contenedor de Resultados -->
<div id="resultadosContainer">
    <!-- Los resultados se cargarán aquí dinámicamente -->
</div>

<!-- Plantilla para resultados (oculta) -->
<div id="resultadosTemplate" style="display: none;">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Resultados de la Consulta
                </h5>
                <span class="badge bg-primary fs-6" id="totalServicios">0 servicios encontrados</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="consultaTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Equipo</th>
                            <th>Problema</th>
                            <th>Solución</th>
                            <th>Estado</th>
                            <th>Técnico</th>
                            <th>Tipo Servicio</th>
                            <th>Fecha Ingreso</th>
                            <th>Costo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaResultados">
                        <!-- Los datos se cargarán aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Plantilla para sin resultados (oculta) -->
<div id="sinResultadosTemplate" style="display: none;">
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No se encontraron servicios</h5>
            <p class="text-muted">No hay servicios que coincidan con los filtros aplicados.</p>
            <button type="button" class="btn btn-outline-primary" onclick="limpiarFiltros()">
                <i class="fas fa-refresh me-2"></i>Limpiar Filtros
            </button>
        </div>
    </div>
</div>

<!-- Plantilla para estado inicial (oculta) -->
<div id="estadoInicialTemplate" style="display: none;">
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-filter fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Consulta de Servicios</h5>
            <p class="text-muted">Utiliza los filtros arriba para buscar servicios específicos.</p>
        </div>
    </div>
</div>

<!-- Estilos personalizados para campos largos -->
<style>
    /* Estilo para celdas con texto largo - Mostrar contenido completo */
    #consultaTable td.texto-largo {
        max-width: 400px;
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
        line-height: 1.4;
        padding: 10px;
    }
    
    /* Ajustar ancho de la tabla para scroll horizontal si es necesario */
    #consultaTable {
        width: 100% !important;
    }
    
    /* Wrapper de la tabla con scroll horizontal */
    .table-responsive {
        overflow-x: auto;
    }
    
    /* Hacer columnas ajustables */
    #consultaTable th,
    #consultaTable td {
        vertical-align: middle;
    }
</style>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    let dataTable = null;

    $(document).ready(function() {
        // Cargar columnas guardadas del localStorage
        cargarColumnasSeleccionadas();
        
        // Mostrar estado inicial
        mostrarEstadoInicial();

        // Validación de fechas
        $('#fecha_desde, #fecha_hasta').on('change', function() {
            const fechaDesde = $('#fecha_desde').val();
            const fechaHasta = $('#fecha_hasta').val();

            if (fechaDesde && fechaHasta && fechaDesde > fechaHasta) {
                alert('La fecha "Desde" no puede ser mayor que la fecha "Hasta"');
                $(this).val('');
            }
        });

        // Validación del ID del servicio
        $('#servicio_id').on('input', function() {
            const valor = $(this).val();
            if (valor && (isNaN(valor) || valor < 1)) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">El ID debe ser un número mayor a 0</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Validación del nombre del cliente
        $('#cliente_nombre').on('input', function() {
            const valor = $(this).val().trim();
            if (valor && valor.length < 2) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">El nombre debe tener al menos 2 caracteres</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Validación antes de enviar
        $('#filtrosForm').on('submit', function(e) {
            const servicioId = $('#servicio_id').val();
            if (servicioId && (isNaN(servicioId) || servicioId < 1)) {
                e.preventDefault();
                alert('El ID del servicio debe ser un número válido mayor a 0');
                $('#servicio_id').focus();
                return false;
            }
        });

        // Envío del formulario con AJAX
        $('#filtrosForm').on('submit', function(e) {
            e.preventDefault();
            cargarResultados();
        });

        // Auto-consulta al cambiar filtros (opcional)
        // $('#filtrosForm select, #filtrosForm input[type="text"]').on('change', function() {
        //     cargarResultados();
        // });
    });

    // Función para mostrar estado inicial
    function mostrarEstadoInicial() {
        const template = $('#estadoInicialTemplate').html();
        $('#resultadosContainer').html(template);
    }

    // Función para cargar resultados via AJAX
    function cargarResultados() {
        // Mostrar indicador de carga
        mostrarCargando();

        // Obtener datos del formulario
        const formData = $('#filtrosForm').serialize();

        // Realizar petición AJAX
        $.ajax({
            url: '<?= url('servicios/cargar-resultados') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarResultados(response);
                } else {
                    mostrarError(response.message || 'Error al cargar los resultados');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                mostrarError('Error de conexión. Por favor intente nuevamente.');
            }
        });
    }

    // Función para mostrar indicador de carga
    function mostrarCargando() {
        const loadingHtml = `
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <h5 class="text-muted">Cargando resultados...</h5>
                <p class="text-muted">Por favor espere mientras se procesan los datos.</p>
            </div>
        </div>
    `;
        $('#resultadosContainer').html(loadingHtml);
    }

    // Función para mostrar resultados
    function mostrarResultados(data) {
        if (data.total === 0) {
            mostrarSinResultados();
            return;
        }

        // Clonar plantilla de resultados
        const template = $('#resultadosTemplate').clone();
        template.removeAttr('id').removeAttr('style');

        // Actualizar contador
        template.find('#totalServicios').text(data.total + ' servicios encontrados');

        // Generar filas de la tabla
        const tbody = template.find('#tablaResultados');
        tbody.empty();

        data.servicios.forEach(function(servicio) {
            const row = generarFilaTabla(servicio);
            tbody.append(row);
        });

        // Mostrar resultados
        $('#resultadosContainer').html(template.html());

        // Inicializar DataTables
        inicializarDataTable();
    }

    // Función para generar fila de tabla
    function generarFilaTabla(servicio) {
        const statusClass = obtenerClaseEstado(servicio.IdEstadoEnTaller);
        const costo = servicio.Costo && servicio.Costo > 0 ?
            '$' + new Intl.NumberFormat('es-CO').format(servicio.Costo) :
            '<span class="text-muted">-</span>';

        const fechaIngreso = formatearFecha(servicio.FechaIngreso);
        
        // Mostrar contenido COMPLETO sin truncar (se ajustará con CSS)
        const problema = servicio.Problema || '';
        const solucion = servicio.Solucion || '<span class="text-muted">-</span>';

        return `
        <tr>
            <td class="fw-bold">${servicio.IdServicio}</td>
            <td>
                <div class="d-flex flex-column">
                    <span class="fw-bold">${capitalizarTexto(servicio.cliente_nombre || 'N/A')}</span>
                    <small class="text-muted">ID: ${servicio.NoIdentificacionCliente || 'N/A'}</small>
                </div>
            </td>
            <td>${capitalizarTexto(servicio.Equipo || '-')}</td>
            <td class="texto-largo">${capitalizarTexto(problema)}</td>
            <td class="texto-largo">${capitalizarTexto(solucion)}</td>
            <td>
                <span class="badge ${statusClass}">
                    ${capitalizarTexto(servicio.estado_descripcion || 'N/A')}
                </span>
            </td>
            <td>${capitalizarTexto(servicio.tecnico_nombre || 'Sin asignar')}</td>
            <td>${servicio.tipo_servicio_nombre || 'N/A'}</td>
            <td>${fechaIngreso}</td>
            <td>${costo}</td>
            <td class="text-center">
                <div class="btn-group" role="group">
                    <a href="<?= url('servicios/view/') ?>${servicio.IdServicio}"
                       class="btn btn-sm btn-outline-primary" title="Ver detalles">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    <a href="<?= url('servicios/edit/') ?>${servicio.IdServicio}"
                       class="btn btn-sm btn-outline-secondary" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="<?= url('servicios/imprimir/') ?>${servicio.IdServicio}"
                       class="btn btn-sm btn-outline-info" title="Imprimir" target="_blank">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
            </td>
        </tr>
    `;
    }

    // Función para mostrar sin resultados
    function mostrarSinResultados() {
        const template = $('#sinResultadosTemplate').html();
        $('#resultadosContainer').html(template);
    }

    // Función para mostrar error
    function mostrarError(mensaje) {
        const errorHtml = `
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h5 class="text-danger">Error al cargar resultados</h5>
                <p class="text-muted">${mensaje}</p>
                <button type="button" class="btn btn-outline-primary" onclick="cargarResultados()">
                    <i class="fas fa-refresh me-2"></i>Intentar Nuevamente
                </button>
            </div>
        </div>
    `;
        $('#resultadosContainer').html(errorHtml);
    }

    // Función para inicializar DataTables
    function inicializarDataTable() {
        if (dataTable) {
            dataTable.destroy();
        }

        dataTable = $('#consultaTable').DataTable({
            initComplete: function() {
                // Aplicar columnas seleccionadas después de inicializar
                aplicarColumnasAlCargar();
            },
            // Configuración en español
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },

            // Configuración responsive
            responsive: true,

            // Configuración de columnas
            columnDefs: [{
                    targets: [0], // Columna ID
                    type: 'num'
                },
                {
                    targets: [10], // Columna Acciones (ahora posición 10 porque agregamos Solución)
                    orderable: false,
                    searchable: false
                }
            ],

            // Configuración de ordenamiento inicial
            order: [
                [0, 'desc']
            ], // Ordenar por ID descendente

            // Configuración de paginación
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],

            // Configuración de búsqueda
            search: {
                smart: true,
                regex: false,
                caseInsensitive: true
            },

            // Configuración de exportación
            dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
            buttons: [{
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Copiar',
                    className: 'btn btn-secondary btn-sm'
                },
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-secondary btn-sm'
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-secondary btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-secondary btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimir',
                    className: 'btn btn-secondary btn-sm'
                }
            ],

            // Configuración de información
            info: true,

            // Configuración de estado
            stateSave: true
        });

        // Personalizar el campo de búsqueda
        $('.dataTables_filter input').attr('placeholder', 'Buscar en todos los campos...');
    }

    // Funciones helper
    function obtenerClaseEstado(estadoId) {
        const clases = {
            1: 'badge bg-warning', // En Espera
            2: 'badge bg-info', // En Mantenimiento
            3: 'badge bg-success', // Terminado
            4: 'badge bg-danger', // En Revisión
            6: 'badge bg-info', // En Autorización
            7: 'badge bg-warning' // Entregado No Reparado
        };
        return clases[estadoId] || 'badge bg-warning';
    }

    function capitalizarTexto(texto) {
        if (!texto) return '';
        return texto.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
    }

    function formatearFecha(fecha) {
        if (!fecha) return '';
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES');
    }

    // Función para exportar resultados
    function exportarResultados() {
        // Obtener los filtros actuales
        const filtros = $('#filtrosForm').serialize();
        
        // SIEMPRE obtener columnas del modal (la fuente de verdad)
        let columnasSeleccionadas = obtenerColumnasSeleccionadas();
        
        // Si no hay columnas seleccionadas, exportar todas
        if (columnasSeleccionadas.length === 0) {
            alert('Por favor, selecciona al menos una columna en "Seleccionar Columnas"');
            return;
        }
        
        const columnasParam = '&columnas=' + columnasSeleccionadas.join(',');

        // Crear URL de exportación
        const url = '<?= url('servicios/exportar-consulta') ?>?' + filtros + columnasParam;

        // Mostrar mensaje de depuración
        console.log('=== EXPORTACIÓN CSV ===');
        console.log('Columnas seleccionadas:', columnasSeleccionadas);
        console.log('Parámetro columnas:', columnasParam);
        console.log('URL completa:', url);
        alert('Exportando ' + columnasSeleccionadas.length + ' columnas. Revisa la consola (F12) para más detalles.');

        // Abrir en nueva ventana
        window.open(url, '_blank');
    }

    // Función para limpiar filtros
    function limpiarFiltros() {
        $('#filtrosForm')[0].reset();
        mostrarEstadoInicial();
    }
    
    // ========== FUNCIONES PARA SELECCIÓN DE COLUMNAS ==========
    
    // Obtener columnas seleccionadas
    function obtenerColumnasSeleccionadas() {
        const columnas = [];
        $('#columnasModal input[type="checkbox"]:not(:disabled):checked').each(function() {
            const val = $(this).val();
            if (val && val !== '10') { // Excluir columna de Acciones
                columnas.push(val);
            }
        });
        console.log('Columnas seleccionadas del modal:', columnas);
        return columnas;
    }
    
    // Guardar columnas seleccionadas en localStorage
    function guardarColumnasSeleccionadas() {
        const columnas = obtenerColumnasSeleccionadas();
        localStorage.setItem('columnasConsultaServicios', JSON.stringify(columnas));
    }
    
    // Cargar columnas seleccionadas desde localStorage
    function cargarColumnasSeleccionadas() {
        const columnas = localStorage.getItem('columnasConsultaServicios');
        if (columnas) {
            try {
                const columnasArray = JSON.parse(columnas);
                console.log('Columnas cargadas desde localStorage:', columnasArray);
                // Desmarcar todas primero
                $('#columnasModal input[type="checkbox"]:not(:disabled)').prop('checked', false);
                // Marcar las guardadas
                columnasArray.forEach(function(col) {
                    $('#columnasModal input[value="' + col + '"]').prop('checked', true);
                });
            } catch(e) {
                console.error('Error al cargar columnas:', e);
                // Si hay error, marcar todas por defecto
                $('#columnasModal input[type="checkbox"]:not(:disabled)').prop('checked', true);
            }
        } else {
            console.log('No hay columnas guardadas, marcando todas por defecto');
            // Si no hay preferencias guardadas, marcar todas
            $('#columnasModal input[type="checkbox"]:not(:disabled)').prop('checked', true);
        }
    }
    
    // Seleccionar todas las columnas
    function seleccionarTodasColumnas() {
        $('#columnasModal input[type="checkbox"]:not(:disabled)').prop('checked', true);
    }
    
    // Deseleccionar todas las columnas
    function deseleccionarTodasColumnas() {
        $('#columnasModal input[type="checkbox"]:not(:disabled)').prop('checked', false);
    }
    
    // Aplicar selección de columnas
    function aplicarColumnas() {
        const columnasSeleccionadas = obtenerColumnasSeleccionadas();
        
        // Guardar selección
        guardarColumnasSeleccionadas();
        
        // Si hay tabla, aplicar cambios
        if (dataTable) {
            // Ocultar/mostrar columnas en DataTable
            dataTable.columns().every(function(index) {
                const visible = columnasSeleccionadas.includes(index.toString()) || index === 10; // Siempre mostrar columna de Acciones
                this.visible(visible);
            });
        }
        
        // Mensaje de confirmación
        const totalColumnas = columnasSeleccionadas.length;
        mostrarMensaje('success', `Configuración guardada: ${totalColumnas} columnas seleccionadas`);
    }
    
    // Aplicar columnas seleccionadas al cargar la tabla
    function aplicarColumnasAlCargar() {
        if (!dataTable) return;
        
        const columnasSeleccionadas = obtenerColumnasSeleccionadas();
        
        // Si no hay columnas seleccionadas, mostrar todas por defecto
        if (columnasSeleccionadas.length === 0) {
            return;
        }
        
        // Ocultar/mostrar columnas sin mensaje
        dataTable.columns().every(function(index) {
            const visible = columnasSeleccionadas.includes(index.toString()) || index === 10; // Siempre mostrar columna de Acciones
            this.visible(visible);
        });
    }
    
    // Función auxiliar para mostrar mensajes
    function mostrarMensaje(tipo, mensaje) {
        const alertClass = tipo === 'success' ? 'alert-success' : 'alert-info';
        const icono = tipo === 'success' ? 'fa-check-circle' : 'fa-info-circle';
        
        const alerta = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${icono} me-2"></i>${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        
        $('body').append(alerta);
        
        // Auto-cerrar después de 3 segundos
        setTimeout(() => {
            alerta.alert('close');
        }, 3000);
    }
</script>

<style>
    /* Estilos adicionales para la consulta */
    .consulta-filters {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    /* Estilos para campo técnico deshabilitado */
    .form-select:disabled {
        background-color: #e9ecef;
        border-color: #ced4da;
        color: #6c757d;
        cursor: not-allowed;
    }

    .form-text.text-muted {
        font-size: 0.875rem;
        margin-top: 5px;
    }

    .form-text.text-muted i {
        color: #6c757d;
    }

    .consulta-results {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .consulta-stats {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .consulta-stats h5 {
        margin: 0;
        font-weight: 600;
    }

    .consulta-stats .stat-item {
        text-align: center;
        padding: 10px;
    }

    .consulta-stats .stat-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
    }

    .consulta-stats .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .consulta-filters .row>div {
            margin-bottom: 15px;
        }

        .consulta-stats .stat-item {
            margin-bottom: 15px;
        }
    }

    /* DataTables customizations */
    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
        margin-bottom: 20px;
    }

    .dataTables_wrapper .dataTables_length {
        float: left;
        margin-bottom: 20px;
    }

    .dataTables_wrapper .dataTables_info {
        clear: both;
        float: left;
        padding-top: 0.755em;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: right;
        text-align: right;
        padding-top: 0.25em;
    }

    /* Button group styling */
    .btn-group .btn {
        margin-right: 2px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    /* Table styling */
    #consultaTable {
        font-size: 0.9rem;
    }

    #consultaTable th {
        background-color: #343a40;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    #consultaTable td {
        vertical-align: middle;
    }

    /* Badge styling */
    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    /* Form styling */
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 6px;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Card styling */
    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px 8px 0 0 !important;
        border: none;
    }

    .card-header h5 {
        margin: 0;
        font-weight: 600;
    }

    /* Alert styling */
    .alert {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .alert-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
    }

    .alert-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: #212529;
    }

    /* Button styling */
    .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.15s ease-in-out;
    }

    .btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        border: none;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    .btn-outline-secondary {
        border: 2px solid #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background: #6c757d;
        border-color: #6c757d;
        transform: translateY(-1px);
    }
</style>