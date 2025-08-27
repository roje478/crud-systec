<?php
/**
 * Vista Index de Clientes
 */

// Verificar que las variables estén definidas
$clientes = $clientes ?? [];
$pagination = $pagination ?? [];
$search = $search ?? '';
$estadisticas = $estadisticas ?? [];
$totalResults = $totalResults ?? null;
?>

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
        <tbody>
            <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">No hay clientes para mostrar.</p>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td class="fw-bold"><code><?= htmlspecialchars($cliente['no_identificacion']) ?></code></td>
                <td><strong><?= htmlspecialchars(ucfirst(strtolower($cliente['nombres']))) ?></strong></td>
                <td><?= htmlspecialchars(ucfirst(strtolower($cliente['apellidos']))) ?></td>
                <td><?= htmlspecialchars($cliente['telefono'] ?: 'N/A') ?></td>
                <td><?= htmlspecialchars(ucfirst(strtolower($cliente['direccion'] ?: 'N/A'))) ?></td>
                <td>
                    <?php if ($cliente['fecha_registro']): ?>
                        <?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <a href="<?= url('clientes/view/' . $cliente['no_identificacion']) ?>"
                           class="btn btn-sm btn-outline-primary" title="Ver detalles">
                            <i class="fas fa-external-link-alt"></i>
                        </a>

                        <a href="<?= url('clientes/edit/' . $cliente['no_identificacion']) ?>"
                           class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
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
// Inicializar DataTable
$(document).ready(function() {
    $('#clientesTable').DataTable({
        responsive: true,
        // Configuración en español
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },

        // Configuración de paginación - 20 clientes por página
        pageLength: 20,
        lengthMenu: [[10, 20, 50], [10, 20, 50]],

        // Configuración de ordenamiento inicial
        order: [[1, 'asc'], [2, 'asc']], // Ordenar por nombres y apellidos alfabéticamente

        // Configuración de columnas
        columnDefs: [
            {
                targets: [0], // Columna ID
                type: 'string'
            },
            {
                targets: [6], // Columna Acciones
                orderable: false,
                searchable: false
            }
        ],

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



// Función para mostrar mensajes de éxito/error
function showMessage(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
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

// Mostrar mensajes flash si existen
<?php if (isset($_SESSION['flash'])): ?>
    showMessage('<?= $_SESSION['flash']['message'] ?>', '<?= $_SESSION['flash']['type'] ?>');
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
</script>