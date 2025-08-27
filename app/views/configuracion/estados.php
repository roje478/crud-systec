<?php
// Verificar que las variables estén definidas
$estados = $estados ?? [];
$stats = $stats ?? [];
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-list text-primary"></i>
                Estados de Servicio
            </h1>
            <p class="text-muted">Gestiona los estados que pueden tener los servicios</p>
        </div>
        <div>
            <a href="<?= url('configuracion') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="<?= url('configuracion/create-estado') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo Estado
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <?php if (!empty($stats)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Estadísticas de Uso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($stats as $stat): ?>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3 text-center">
                                <h5 class="text-primary"><?= $stat['total_servicios'] ?></h5>
                                <small class="text-muted"><?= htmlspecialchars($stat['Descripcion']) ?></small>
                                <?php if ($stat['servicios_mes'] > 0): ?>
                                <br><small class="text-success"><?= $stat['servicios_mes'] ?> este mes</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabla de estados -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Lista de Estados
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($estados)): ?>
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay estados configurados</h5>
                <p class="text-muted">Comienza creando el primer estado del sistema.</p>
                <a href="<?= url('configuracion/create-estado') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primer Estado
                </a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="estadosTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Servicios Asociados</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estados as $estado): ?>
                        <tr>
                            <td class="fw-bold"><?= $estado['IdEstadoEnTaller'] ?></td>
                            <td><?= htmlspecialchars($estado['Descripcion']) ?></td>
                            <td>
                                <?php
                                $serviciosCount = 0;
                                foreach ($stats as $stat) {
                                    if ($stat['IdEstadoEnTaller'] == $estado['IdEstadoEnTaller']) {
                                        $serviciosCount = $stat['total_servicios'];
                                        break;
                                    }
                                }
                                ?>
                                <span class="badge bg-info"><?= $serviciosCount ?> servicios</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="<?= url('configuracion/edit-estado/' . $estado['IdEstadoEnTaller']) ?>"
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <?php if ($serviciosCount == 0): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteEstado(<?= $estado['IdEstadoEnTaller'] ?>, '<?= htmlspecialchars($estado['Descripcion']) ?>')"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                            title="No se puede eliminar - tiene servicios asociados">
                                        <i class="fas fa-lock"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function deleteEstado(id, descripcion) {
    if (confirm(`¿Estás seguro de que quieres eliminar el estado "${descripcion}"?`)) {
        fetch(`<?= url('configuracion/delete-estado/') ?>${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Estado eliminado exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el estado');
        });
    }
}

// Inicializar DataTable
$(document).ready(function() {
    $('#estadosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[0, 'asc']]
    });
});
</script>