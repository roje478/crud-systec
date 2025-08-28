<?php
// Verificar que las variables estén definidas
$clientes = $clientes ?? [];
?>

<!-- Barra de búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <div class="form-group">
            <label for="clienteSearchMejorado" class="form-label fw-bold">
                <i class="fas fa-search text-primary"></i>
                Buscar Clientes
            </label>
            <div class="input-group">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       id="clienteSearchMejorado" 
                       placeholder="Escriba ID, nombres, apellidos, teléfono o dirección..."
                       autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" onclick="limpiarBusqueda()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="form-text">
                <i class="fas fa-info-circle text-info"></i>
                Puede buscar por: número de identificación, nombres, apellidos, teléfono o dirección
            </div>
        </div>
    </div>
</div>

<!-- Table Container -->
<div class="table-container">
    <table class="table table-striped table-hover" id="clientesTable">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Fecha Registro</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody id="clientesTableBody">
            <!-- Los resultados se cargarán dinámicamente aquí -->
        </tbody>
    </table>
</div>

<!-- Estados de la tabla -->
<div id="estadoInicial" class="text-center py-5">
    <i class="fas fa-search fa-3x text-primary mb-3"></i>
    <h5 class="text-primary">Búsqueda de Clientes</h5>
    <p class="text-muted">Escribe en el campo de búsqueda para comenzar</p>
</div>

<div id="estadoCargando" class="text-center py-5" style="display: none;">
    <div class="spinner-border text-primary mb-3" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
    <p class="text-muted">Buscando clientes...</p>
</div>

<div id="estadoNoResultados" class="text-center py-5" style="display: none;">
    <i class="fas fa-search fa-3x text-muted mb-3"></i>
    <h5 class="text-muted">No se encontraron clientes</h5>
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
    console.log('=== BÚSQUEDA DE CLIENTES MEJORADA INICIADA ===');
    
    // Evento de búsqueda principal
    $('#clienteSearchMejorado').on('input', function() {
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
            buscarClientesMejorado(query);
        }, 300);
    });

    // Evento Enter para búsqueda
    $('#clienteSearchMejorado').on('keypress', function(e) {
        if (e.which === 13) {
            const query = $(this).val().trim();
            if (query.length > 0) {
                buscarClientesMejorado(query);
            }
        }
    });
});

// Función de búsqueda mejorada
function buscarClientesMejorado(query) {
    if (isLoading) return;
    
    isLoading = true;
    console.log('Buscando clientes:', query);
    
    $.ajax({
        url: 'index.php?route=clientes/buscar-clientes',
        method: 'GET',
        data: { q: query },
        dataType: 'json',
        beforeSend: function() {
            mostrarEstadoCargando();
        },
        success: function(data) {
            isLoading = false;
            
            if (data.success) {
                console.log('Búsqueda exitosa:', data.clientes.length, 'clientes');
                resultadosActuales = data.clientes;
                mostrarResultadosEnTabla(data.clientes);
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
function mostrarResultadosEnTabla(clientes) {
    const tbody = $('#clientesTableBody');
    
    if (clientes.length === 0) {
        mostrarEstadoNoResultados();
        return;
    }
    
    let html = '';
    clientes.forEach(function(cliente) {
        const fechaFormateada = formatearFecha(cliente.fecha_registro);
        
        html += `
            <tr>
                <td class="fw-bold"><code>${cliente.no_identificacion || 'N/A'}</code></td>
                <td><strong>${ucfirst(strtolower(cliente.nombres || 'N/A'))}</strong></td>
                <td>${ucfirst(strtolower(cliente.apellidos || 'N/A'))}</td>
                <td>${cliente.telefono || 'N/A'}</td>
                <td>${ucfirst(strtolower(cliente.direccion || 'N/A'))}</td>
                <td>${fechaFormateada}</td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <a href="index.php?route=clientes/view/${cliente.no_identificacion}"
                           class="btn btn-sm btn-outline-primary" title="Ver detalles">
                            <i class="fas fa-external-link-alt"></i>
                        </a>

                        <a href="index.php?route=clientes/edit/${cliente.no_identificacion}"
                           class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.html(html);
    mostrarTabla();
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
    if (!fecha) return 'N/A';
    
    try {
        const fechaObj = new Date(fecha);
        return fechaObj.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    } catch (e) {
        return fecha;
    }
}

// Función para limpiar búsqueda
function limpiarBusqueda() {
    $('#clienteSearchMejorado').val('');
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
</script>
