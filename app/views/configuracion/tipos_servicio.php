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
            order: [
                [0, 'asc']
            ],
            responsive: true,
            columnDefs: [{
                orderable: false,
                targets: 3
            }]
        });
    });
</script>