<?php
// Verificar que las variables estén definidas
$servicios = $servicios ?? [];
$estados = $estados ?? [];
$esTecnico = $esTecnico ?? false;
$esAsesor = $esAsesor ?? false;
?>

<?php if ($esTecnico): ?>
<!-- Mensaje informativo para técnicos -->
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center">
        <i class="fas fa-user-cog me-3 fa-2x"></i>
        <div>
            <h5 class="alert-heading mb-1">Vista de Técnico</h5>
            <p class="mb-0">Solo se muestran los servicios asignados a tu perfil técnico.</p>
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
            <h5 class="alert-heading mb-1">Vista de Asesor</h5>
            <p class="mb-0">Como asesor, puedes ver y editar servicios, pero no cambiar su estado.</p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Barra de búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       id="servicioSearchMejorado" 
                       placeholder="..."
                       autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" onclick="limpiarBusqueda()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="form-text">
                <i class="fas fa-info-circle text-info"></i>
                Puede buscar por: ID del servicio, nombre del equipo, problema, nombre del cliente o identificación
            </div>
        </div>
    </div>
</div>



<!-- Table Container -->
<div class="table-container">
    <table class="table table-striped table-hover" id="serviciosTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Problema</th>
                <th>Estado</th>
                <th>Técnico</th>
                <th>Fecha Ingreso</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody id="serviciosTableBody">
            <!-- Los resultados se cargarán dinámicamente aquí -->
        </tbody>
    </table>
</div>

<!-- Estados de la tabla -->
<div id="estadoInicial" class="text-center py-5">
    <i class="fas fa-search fa-3x text-primary mb-3"></i>
    <h5 class="text-primary">Búsqueda de Servicios</h5>
</div>

<div id="estadoCargando" class="text-center py-5" style="display: none;">
    <div class="spinner-border text-primary mb-3" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
    <p class="text-muted">Buscando servicios...</p>
</div>

<div id="estadoNoResultados" class="text-center py-5" style="display: none;">
    <i class="fas fa-search fa-3x text-muted mb-3"></i>
    <h5 class="text-muted">No se encontraron servicios</h5>
    <p class="text-muted">Intenta con otros términos de búsqueda</p>
</div>

<div id="estadoError" class="text-center py-5" style="display: none;">
    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
    <h5 class="text-warning">Error en la búsqueda</h5>
    <p class="text-muted" id="mensajeError">Ha ocurrido un error</p>
</div>

<script>
// Variables globales
let searchTimeout;
let resultadosActuales = [];
let isLoading = false;

// Inicialización
$(document).ready(function() {
    console.log('=== BÚSQUEDA MEJORADA INICIADA ===');
    
    // Evento de búsqueda principal
    $('#servicioSearchMejorado').on('input', function() {
        const query = $(this).val().trim();
        
        // Limpiar timeout anterior
        clearTimeout(searchTimeout);
        
        if (query.length < 1) {
            mostrarEstadoInicial();
            return;
        }
        
        // Mostrar loading
        mostrarEstadoCargando();
        
        // Búsqueda con delay de 300ms
        searchTimeout = setTimeout(function() {
            buscarServiciosMejorado(query);
        }, 300);
    });



    // Evento Enter para búsqueda
    $('#servicioSearchMejorado').on('keypress', function(e) {
        if (e.which === 13) {
            const query = $(this).val().trim();
            if (query.length > 0) {
                buscarServiciosMejorado(query);
            }
        }
    });
});

// Función de búsqueda mejorada
function buscarServiciosMejorado(query) {
    if (isLoading) return;
    
    isLoading = true;
    console.log('Buscando servicios:', query);
    
    $.ajax({
        url: 'index.php?route=servicios/buscar-servicios',
        method: 'GET',
        data: { q: query },
        dataType: 'json',
        beforeSend: function() {
            mostrarEstadoCargando();
        },
        success: function(data) {
            isLoading = false;
            
            if (data.success) {
                console.log('Búsqueda exitosa:', data.servicios.length, 'servicios');
                resultadosActuales = data.servicios;
                mostrarResultadosEnTabla(data.servicios);
            } else {
                console.log('Búsqueda falló:', data);
                mostrarError('Error en la búsqueda');
            }
        },
        error: function(xhr, status, error) {
            isLoading = false;
            console.error('Error en búsqueda:', error);
            mostrarError('Error de conexión: ' + error);
        }
    });
}

// Función para mostrar resultados en tabla
function mostrarResultadosEnTabla(servicios) {
    const tbody = $('#serviciosTableBody');
    
    if (servicios.length === 0) {
        mostrarEstadoNoResultados();
        return;
    }
    
    let html = '';
    servicios.forEach(function(servicio) {
        const statusClass = getStatusClass(servicio.IdEstadoEnTaller);
        const fechaFormateada = formatearFecha(servicio.FechaIngreso);
        
        html += `
            <tr>
                <td class="fw-bold">${servicio.IdServicio}</td>
                <td>${ucfirst(strtolower(servicio.cliente_nombre || 'N/A'))}</td>
                <td>${ucfirst(strtolower(servicio.Equipo || '-'))}</td>
                <td>
                    ${ucfirst(strtolower(substr(servicio.Problema || '', 0, 30)))}
                    ${(servicio.Problema || '').length > 30 ? '...' : ''}
                </td>
                <td>
                    <span class="${statusClass}">
                        ${ucfirst(strtolower(servicio.estado_descripcion || 'N/A'))}
                    </span>
                </td>
                <td>${ucfirst(strtolower(servicio.tecnico_nombre || 'Sin asignar'))}</td>
                <td>${fechaFormateada}</td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <a href="index.php?route=servicios/view/${servicio.IdServicio}"
                           class="btn btn-sm btn-outline-primary" title="Ver detalles">
                            <i class="fas fa-external-link-alt"></i>
                        </a>

                        <a href="index.php?route=servicios/edit/${servicio.IdServicio}"
                           class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>

                        ${!<?= $esAsesor ? 'true' : 'false' ?> ? `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-info dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown" title="Cambiar estado">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <ul class="dropdown-menu">
                                ${generarOpcionesEstados(servicio.IdEstadoEnTaller)}
                            </ul>
                        </div>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.html(html);
    mostrarTabla();
}

// Función para generar opciones de estados
function generarOpcionesEstados(estadoActual) {
    const estados = <?= json_encode($estados) ?>;
    let html = '';
    
    if (estados && estados.length > 0) {
        estados.forEach(function(estado) {
            if (estado.id != estadoActual) {
                html += `
                    <li>
                        <a class="dropdown-item" href="#" onclick="changeStatus(${estado.id}, '${estado.descripcion}')">
                            ${estado.descripcion}
                        </a>
                    </li>
                `;
            }
        });
    } else {
        html = '<li><span class="dropdown-item-text text-muted">No hay estados disponibles</span></li>';
    }
    
    return html;
}

// Función para obtener clase de estado
function getStatusClass(idEstado) {
    switch(idEstado) {
        case 1: return 'badge bg-warning';    // En Espera
        case 2: return 'badge bg-info';       // En Mantenimiento
        case 3: return 'badge bg-success';    // Terminado
        case 4: return 'badge bg-danger';     // En Revisión
        case 6: return 'badge bg-info';       // En Autorización
        case 7: return 'badge bg-warning';    // Entregado No Reparado
        default: return 'badge bg-warning';
    }
}

// Función para mostrar tabla
function mostrarTabla() {
    $('.table-container').show();
    $('#estadoInicial').hide();
    $('#estadoCargando').hide();
    $('#estadoNoResultados').hide();
    $('#estadoError').hide();
}

// Función para mostrar estado inicial
function mostrarEstadoInicial() {
    $('.table-container').hide();
    $('#estadoInicial').show();
    $('#estadoCargando').hide();
    $('#estadoNoResultados').hide();
    $('#estadoError').hide();
}

// Función para mostrar estado cargando
function mostrarEstadoCargando() {
    $('.table-container').hide();
    $('#estadoInicial').hide();
    $('#estadoCargando').show();
    $('#estadoNoResultados').hide();
    $('#estadoError').hide();
}

// Función para mostrar no resultados
function mostrarEstadoNoResultados() {
    $('.table-container').hide();
    $('#estadoInicial').hide();
    $('#estadoCargando').hide();
    $('#estadoNoResultados').show();
    $('#estadoError').hide();
}

// Función para mostrar error
function mostrarError(mensaje) {
    $('.table-container').hide();
    $('#estadoInicial').hide();
    $('#estadoCargando').hide();
    $('#estadoNoResultados').hide();
    $('#estadoError').show();
    $('#mensajeError').text(mensaje);
}



// Función para formatear fecha
function formatearFecha(fecha) {
    if (!fecha) return 'Sin fecha';
    
    try {
        const fechaObj = new Date(fecha);
        return fechaObj.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        return fecha;
    }
}

// Función para limpiar búsqueda
function limpiarBusqueda() {
    $('#servicioSearchMejorado').val('');
    mostrarEstadoInicial();
    resultadosActuales = [];
}

// Funciones de utilidad
function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

function strtolower(str) {
    return str.toLowerCase();
}

function substr(str, start, length) {
    return str.substring(start, start + length);
}

// Función para cambiar estado (compatible con el sistema existente)
function changeStatus(servicioId, nuevoEstadoId, descripcion) {
    // Implementar lógica de cambio de estado si es necesario
    console.log('Cambiar estado del servicio', servicioId, 'a', descripcion);
}
</script>
