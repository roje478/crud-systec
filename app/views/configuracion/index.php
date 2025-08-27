<?php
// Verificar que las variables estén definidas
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs text-primary"></i>
                Configuración del Sistema
            </h1>
            <p class="text-muted">Gestiona los parámetros del sistema</p>
        </div>
    </div>

    <!-- Tarjetas de configuración -->
    <div class="row">
        <!-- Estados de Servicio -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Estados de Servicio
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Gestionar estados
                            </div>
                            <p class="text-muted small mt-2">
                                Configura los diferentes estados que pueden tener los servicios en el sistema
                            </p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="<?= url('configuracion/estados') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-cog"></i> Gestionar Estados
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tipos de Servicio -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tipos de Servicio
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Gestionar tipos
                            </div>
                            <p class="text-muted small mt-2">
                                Configura los diferentes tipos de servicios que ofrece el taller
                            </p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="<?= url('configuracion/tipos-servicio') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-cog"></i> Gestionar Tipos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-download"></i> Crear Datos por Defecto
                                </h6>
                                <p class="mb-2">Crea automáticamente los estados y tipos de servicio básicos del sistema.</p>
                                <button type="button" class="btn btn-info btn-sm" onclick="createDefaults()">
                                    <i class="fas fa-magic"></i> Crear Datos por Defecto
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle"></i> Información
                                </h6>
                                <p class="mb-2">Los cambios en la configuración afectarán a todos los servicios del sistema.</p>
                                <small class="text-muted">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Ten cuidado al eliminar estados o tipos que estén en uso.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createDefaults() {
    if (confirm('¿Estás seguro de que quieres crear los datos por defecto? Esto solo creará los que no existan.')) {
        fetch('<?= url('configuracion/create-defaults') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Datos por defecto creados exitosamente:\n' +
                      'Estados: ' + data.estados_creados.join(', ') + '\n' +
                      'Tipos: ' + data.tipos_creados.join(', '));
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al crear los datos por defecto');
        });
    }
}
</script>