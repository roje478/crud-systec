<?php
// Verificar que las variables estén definidas
$clausulas = $clausulas ?? [];
$tipos = $tipos ?? [];
?>

<!-- Header de la página -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-file-contract text-primary me-2"></i>
            Gestión de Cláusulas
        </h1>
        <p class="text-muted mb-0">Administra las cláusulas del sistema</p>
    </div>
    <div>
        <a href="<?= url('clausulas/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nueva Cláusula
        </a>
    </div>
</div>



<!-- Tabla de cláusulas -->
<div class="card">
    <div class="card-body">
        <?php if (empty($clausulas)): ?>
            <div class="text-center py-5">
                <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay cláusulas</h5>
                <p class="text-muted">Crea tu primera cláusula para comenzar</p>
                <a href="<?= url('clausulas/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Crear Cláusula
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="clausulasTable">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 100px;">Orden</th>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th style="width: 200px;">Estado</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="clausulasTableBody">
                        <?php foreach ($clausulas as $clausula): ?>
                            <tr data-id="<?= $clausula['id'] ?>" data-tipo="<?= $clausula['tipo'] ?>">
                                <td class="fw-bold"><?= $clausula['id'] ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= $clausula['orden'] ?></span>
                                </td>
                                <td>
                                    <code class="text-primary"><?= htmlspecialchars($clausula['codigo']) ?></code>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($clausula['titulo']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars(substr($clausula['descripcion'], 0, 80)) ?>
                                        <?= strlen($clausula['descripcion']) > 80 ? '...' : '' ?>
                                    </small>
                                </td>
                                <td>
                                    <?php
                                    $tipoClass = 'badge bg-info';
                                    switch($clausula['tipo']) {
                                        case 'general': $tipoClass = 'badge bg-primary'; break;
                                        case 'servicio': $tipoClass = 'badge bg-info'; break;
                                        case 'garantia': $tipoClass = 'badge bg-warning'; break;
                                        case 'entrega': $tipoClass = 'badge bg-success'; break;
                                        case 'pago': $tipoClass = 'badge bg-danger'; break;
                                    }
                                    ?>
                                    <span class="<?= $tipoClass ?>">
                                        <?= ucfirst($clausula['tipo']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input toggle-estado"
                                               type="checkbox"
                                               data-id="<?= $clausula['id'] ?>"
                                               <?= $clausula['activo'] ? 'checked' : '' ?>>
                                        <label class="form-check-label">
                                            <?= $clausula['activo'] ? 'Activa' : 'Inactiva' ?>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                onclick="verClausula(<?= $clausula['id'] ?>)"
                                                title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="<?= url('clausulas/edit/' . $clausula['id']) ?>"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="eliminarClausula(<?= $clausula['id'] ?>, '<?= htmlspecialchars($clausula['titulo']) ?>')"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Modal para ver cláusula -->
<div class="modal fade" id="modalClausula" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-contract text-primary me-2"></i>
                    Detalles de la Cláusula
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalClausulaBody">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnEditarClausula">Editar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let clausulaSeleccionada = null;

// Toggle estado
document.querySelectorAll('.toggle-estado').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const id = this.dataset.id;
        const activo = this.checked ? 1 : 0;
        cambiarEstadoClausula(id, activo);
    });
});

function cambiarEstadoClausula(id, activo) {
    fetch(`<?= url('clausulas/cambiar-estado/') ?>${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ activo: activo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Éxito', data.message, 'success');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al cambiar estado', 'error');
    });
}

function verClausula(id) {
    // Aquí podrías cargar los detalles de la cláusula via AJAX
    // Por ahora, mostraremos un mensaje
    Swal.fire({
        title: 'Ver Cláusula',
        text: 'Funcionalidad de vista detallada en desarrollo',
        icon: 'info'
    });
}

function eliminarClausula(id, titulo) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas eliminar la cláusula "${titulo}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?= url('clausulas/delete/') ?>${id}`;
        }
    });
}


</script>