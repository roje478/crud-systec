<?php
// Verificar que las variables estén definidas
$tipos = $tipos ?? [];
$stats = $stats ?? [];
?>

<div class="service-detail">
    <!-- Contenido principal -->
    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Header de la página -->
            <div class="service-info-card">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Tipos de Servicio</h3>
                                <p class="form-intro__description">Gestiona los tipos de servicios que ofrece el taller</p>
                            </div>

                            <div class="form-intro__actions">
                                <a href="<?= url('configuracion') ?>" class="btn btn--outline">
                                    <i class="fas fa-arrow-left btn__icon"></i>Volver
                                </a>
                                <a href="<?= url('configuracion/create-tipo-servicio') ?>" class="btn btn--primary">
                                    <i class="fas fa-plus btn__icon"></i>Nuevo Tipo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <?php if (!empty($stats)): ?>
            <div class="service-info-card">
                <div class="service-info-card__header">
                    <h5 class="service-info-card__title">
                        <i class="fas fa-chart-bar"></i> Estadísticas de Uso
                    </h5>
                </div>
                <div class="service-info-card__body">
                    <div class="row">
                        <?php foreach ($stats as $stat): ?>
                        <div class="col-md-3 mb-3">
                            <div class="service-info-card service-info-card--info">
                                <div class="service-info-card__body text-center">
                                    <h3 class="text-success mb-2"><?= $stat['total_servicios'] ?></h3>
                                    <p class="mb-1"><strong><?= htmlspecialchars($stat['Descripcion']) ?></strong></p>
                                    <?php if ($stat['servicios_mes'] > 0): ?>
                                    <small class="text-success"><?= $stat['servicios_mes'] ?> este mes</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tabla de tipos -->
            <div class="service-info-card">
                <div class="service-info-card__header">
                    <h5 class="service-info-card__title">
                        <i class="fas fa-list"></i> Lista de Tipos de Servicio
                    </h5>
                </div>
                <div class="service-info-card__body">
                    <?php if (empty($tipos)): ?>
                    <div class="text-center py-5">
                        <div class="service-info-card service-info-card--info">
                            <div class="service-info-card__body">
                                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay tipos de servicio configurados</h5>
                                <p class="text-muted">Comienza creando el primer tipo de servicio del sistema.</p>
                                <a href="<?= url('configuracion/create-tipo-servicio') ?>" class="btn btn--primary">
                                    <i class="fas fa-plus btn__icon"></i>Crear Primer Tipo
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="table-container">
                        <table class="table" id="tiposTable">
                            <thead class="table__header">
                                <tr>
                                    <th class="table__header-cell">#</th>
                                    <th class="table__header-cell">Descripción</th>
                                    <th class="table__header-cell">Valor Base</th>
                                    <th class="table__header-cell">Servicios Asociados</th>
                                    <th class="table__header-cell text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tipos as $tipo): ?>
                                <tr class="table__body-row">
                                    <td class="table__body-cell fw-bold"><?= $tipo['IdTipoServicio'] ?></td>
                                    <td class="table__body-cell"><?= htmlspecialchars($tipo['Descripcion']) ?></td>
                                    <td class="table__body-cell">
                                        <?php if (!empty($tipo['CostoAproximado'])): ?>
                                            <span class="status-badge status-badge--info">
                                                $<?= number_format($tipo['CostoAproximado'], 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">No establecido</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="table__body-cell">
                                        <?php
                                        $serviciosCount = 0;
                                        foreach ($stats as $stat) {
                                            if ($stat['IdTipoServicio'] == $tipo['IdTipoServicio']) {
                                                $serviciosCount = $stat['total_servicios'];
                                                break;
                                            }
                                        }
                                        ?>
                                        <span class="status-badge status-badge--success"><?= $serviciosCount ?> servicios</span>
                                    </td>
                                    <td class="table__body-cell">
                                        <div class="table__actions">
                                            <a href="<?= url('configuracion/edit-tipo-servicio/' . $tipo['IdTipoServicio']) ?>"
                                               class="table__action-btn table__action-btn--primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <?php if ($serviciosCount == 0): ?>
                                            <button type="button" class="table__action-btn table__action-btn--danger"
                                                    onclick="deleteTipoServicio(<?= $tipo['IdTipoServicio'] ?>, '<?= htmlspecialchars($tipo['Descripcion']) ?>')"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php else: ?>
                                            <button type="button" class="table__action-btn table__action-btn--secondary" disabled
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

        <!-- Panel lateral con información -->
        <div class="service-detail__sidebar">
            <div class="service-info-card service-info-card--info">
                <div class="service-info-card__header">
                    <h5 class="service-info-card__title">
                        <i class="fas fa-info-circle"></i> Información Importante
                    </h5>
                </div>
                <div class="service-info-card__body">
                    <div class="alert alert--info">
                        <h6 class="alert__heading">
                            <i class="fas fa-lightbulb"></i> Gestión de Tipos
                        </h6>
                        <ul class="mb-0">
                            <li>Los tipos de servicio definen las categorías de trabajo</li>
                            <li>Cada servicio debe tener un tipo asignado</li>
                            <li>Los tipos con servicios asociados no se pueden eliminar</li>
                            <li>Puedes editar la descripción en cualquier momento</li>
                        </ul>
                    </div>

                    <div class="alert alert--warning">
                        <h6 class="alert__heading">
                            <i class="fas fa-exclamation-triangle"></i> Importante
                        </h6>
                        <p class="mb-0">
                            Solo se pueden eliminar tipos que no tengan servicios asociados.
                            Esto previene la pérdida de datos importantes.
                        </p>
                    </div>

                    <div class="alert alert--success">
                        <h6 class="alert__heading">
                            <i class="fas fa-check-circle"></i> Consejos
                        </h6>
                        <ul class="mb-0">
                            <li>Usa nombres descriptivos y específicos</li>
                            <li>Mantén una estructura organizada</li>
                            <li>Revisa las estadísticas de uso regularmente</li>
                            <li>Considera consolidar tipos similares</li>
                        </ul>
                    </div>

                    <?php if (!empty($stats)): ?>
                    <div class="alert alert--info">
                        <h6 class="alert__heading">
                            <i class="fas fa-chart-line"></i> Resumen
                        </h6>
                        <p class="mb-0">
                            <strong>Total de tipos:</strong> <?= count($tipos) ?><br>
                            <strong>Tipos en uso:</strong> <?= count(array_filter($stats, function($stat) { return $stat['total_servicios'] > 0; })) ?><br>
                            <strong>Total servicios:</strong> <?= array_sum(array_column($stats, 'total_servicios')) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteTipoServicio(id, descripcion) {
    if (confirm(`¿Estás seguro de que quieres eliminar el tipo de servicio "${descripcion}"?`)) {
        fetch(`<?= url('configuracion/delete-tipo-servicio/') ?>${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage('Tipo de servicio eliminado exitosamente');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showErrorMessage(data.message || 'Error al eliminar el tipo de servicio');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('Error de conexión al eliminar el tipo de servicio');
        });
    }
}

// Funciones de mensajes
function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert--success alert-dismissible fade show position-fixed';
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
    alertDiv.className = 'alert alert--danger alert-dismissible fade show position-fixed';
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

// Inicializar DataTable
$(document).ready(function() {
    $('#tiposTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[0, 'asc']],
        responsive: true,
        columnDefs: [
            { orderable: false, targets: 3 }
        ]
    });
});
</script>