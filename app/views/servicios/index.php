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
        <tbody>
            <?php if (empty($servicios)): ?>
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">No hay servicios para mostrar.</p>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($servicios as $servicio): ?>
            <tr>
                <td class="fw-bold"><?= $servicio['IdServicio'] ?></td>
                <td><?= ucfirst(strtolower(htmlspecialchars($servicio['cliente_nombre'] ?? 'N/A'))) ?></td>
                <td><?= ucfirst(strtolower(htmlspecialchars($servicio['Equipo'] ?? '-'))) ?></td>
                <td>
                    <?= ucfirst(strtolower(htmlspecialchars(substr($servicio['Problema'] ?? '', 0, 30)))) ?>
                    <?= strlen($servicio['Problema'] ?? '') > 30 ? '...' : '' ?>
                </td>
                <td>
                    <?php
                    $statusClass = 'badge bg-warning';
                    switch($servicio['IdEstadoEnTaller']) {
                        case 1: $statusClass = 'badge bg-warning'; break;    // En Espera
                        case 2: $statusClass = 'badge bg-info'; break;       // En Mantenimiento
                        case 3: $statusClass = 'badge bg-success'; break;    // Terminado
                        case 4: $statusClass = 'badge bg-danger'; break;     // En Revisión
                        case 6: $statusClass = 'badge bg-info'; break;    // En Autorización
                        case 7: $statusClass = 'badge bg-warning'; break;       // Entregado No Reparado
                        default: $statusClass = 'badge bg-warning'; break;
                    }
                    ?>
                    <span class="<?= $statusClass ?>">
                        <?= ucfirst(strtolower(htmlspecialchars($servicio['estado_descripcion'] ?? 'N/A'))) ?>
                    </span>
                </td>
                <td><?= ucfirst(strtolower(htmlspecialchars($servicio['tecnico_nombre'] ?? 'Sin asignar'))) ?></td>
                <td><?= DateHelper::extractDate($servicio['FechaIngreso']) ?></td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <a href="<?= url('servicios/view/' . $servicio['IdServicio']) ?>"
                           class="btn btn-sm btn-outline-primary" title="Ver detalles">
                            <i class="fas fa-external-link-alt"></i>
                        </a>

                        <a href="<?= url('servicios/edit/' . $servicio['IdServicio']) ?>"
                           class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>

                        <?php if (!$esAsesor && !$esTecnico): ?>
                        <!-- Botón de cambiar estado - NO visible para asesores ni técnicos -->
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-info dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown" title="Cambiar estado">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <?php if (!empty($estados)): ?>
                                    <?php foreach ($estados as $estado): ?>
                                        <?php if ($estado['id'] != $servicio['IdEstadoEnTaller']): ?>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="changeStatus(<?= $servicio['IdServicio'] ?>, <?= $estado['id'] ?>, '<?= htmlspecialchars($estado['descripcion']) ?>')">
                                                    <?= htmlspecialchars($estado['descripcion']) ?>
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
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

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
// Inicializar DataTables
$(document).ready(function() {
    $('#serviciosTable').DataTable({
        // Configuración en español
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },

        // Configuración responsive
        responsive: true,

        // Configuración de columnas
        columnDefs: [
            {
                targets: [0], // Columna ID
                type: 'num'
            },
            {
                targets: [7], // Columna Acciones
                orderable: false,
                searchable: false
            }
        ],

        // Configuración de ordenamiento inicial
        order: [[0, 'desc']], // Ordenar por ID descendente

        // Configuración de paginación - 20 productos por página
        pageLength: 20,
        lengthMenu: [[10, 20, 50], [10, 20, 50]],

        // Configuración de búsqueda
        search: {
            smart: true,
            regex: false,
            caseInsensitive: true
        },

        // Configuración de exportación
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        buttons: [
            {
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

    console.log('DataTables inicializado correctamente');

    // Personalizar el campo de búsqueda
    $('.dataTables_filter input').attr('placeholder', 'Buscar en todos los campos...');
});

// Funciones JavaScript existentes
function changeStatus(id, newStatus, estadoNombre) {
    const mensaje = `¿Está seguro de cambiar el estado de este servicio a "${estadoNombre}"?`;

    if (confirm(mensaje)) {
        const button = event.target.closest('.dropdown').querySelector('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        const url = `<?= url('servicios/changeStatus/') ?>${id}`;
        const data = {estado: newStatus};

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);

                if (data.success) {
                    showSuccessMessage(`Estado cambiado exitosamente a "${estadoNombre}"`);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    alert('Error: ' + (data.message || 'Error desconocido'));
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            } catch (e) {
                console.error('Error parsing JSON:', e);
                alert('Error al procesar la respuesta del servidor');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            alert('Error al cambiar el estado. Por favor intente nuevamente. Error: ' + error.message);
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

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


</script>