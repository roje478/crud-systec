<?php
// Vista principal de permisos
?>

<div class="page-actions">
    <div class="page-actions__search">
        <input type="text" class="page-actions__search-input" placeholder="Buscar perfiles...">
        <i class="fas fa-search page-actions__search-icon"></i>
    </div>
    <a href="<?= url('permisos/create') ?>" class="btn btn--primary">
        <i class="fas fa-user-plus"></i>
        Crear Perfil
    </a>

</div>

<div class="row">
    <!-- Lista de Perfiles -->
    <div class="col-md-12">
        <div class="card">
            <div class="card__header">
                <h3 class="card__title">
                    <i class="fas fa-users"></i>
                    Perfiles del Sistema
                </h3>
            </div>
            <div class="card__body">
                <?php if (!empty($perfiles)): ?>
                    <div class="table-container">
                        <table class="table">
                            <thead class="table__header">
                                <tr>
                                    <th class="table__header-cell">ID</th>
                                    <th class="table__header-cell">Perfil</th>
                                    <th class="table__header-cell">Estado</th>
                                    <th class="table__header-cell">Opciones Asignadas</th>
                                    <th class="table__header-cell">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($perfiles as $perfil): ?>
                                    <tr class="table__body-row">
                                        <td class="table__body-cell"><?= $perfil['id'] ?></td>
                                        <td class="table__body-cell">
                                            <strong><?= htmlspecialchars($perfil['descripcion']) ?></strong>
                                        </td>
                                        <td class="table__body-cell">
                                            <span class="badge badge--<?= $perfil['activo'] ? 'success' : 'danger' ?>">
                                                <?= $perfil['activo'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td class="table__body-cell">
                                            <span class="badge badge--info">-</span>
                                        </td>
                                        <td class="table__body-cell">
                                            <div class="table__actions">
                                                <a href="<?= url('permisos/asignar/' . $perfil['id']) ?>"
                                                   class="table__action-btn table__action-btn--primary"
                                                   title="Asignar Permisos">
                                                    <i class="fas fa-key"></i>
                                                </a>
                                                <a href="<?= url('permisos/edit/' . $perfil['id']) ?>"
                                                   class="table__action-btn table__action-btn--secondary"
                                                   title="Editar Perfil">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                        class="table__action-btn table__action-btn--danger"
                                                        onclick="confirmarEliminacion(<?= $perfil['id'] ?>, '<?= htmlspecialchars($perfil['descripcion']) ?>')"
                                                        title="Eliminar Perfil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert--info">
                        <i class="fas fa-info-circle"></i>
                        No hay perfiles disponibles.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Funcionalidad de búsqueda
document.querySelector('.page-actions__search-input').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.table__body-row');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Función para confirmar eliminación
function confirmarEliminacion(perfilId, descripcion) {
    if (confirm(`¿Está seguro de que desea eliminar el perfil "${descripcion}"?\n\nEsta acción no se puede deshacer.`)) {
        // Realizar petición AJAX para eliminar
        fetch(`<?= url('permisos/delete/') ?>${perfilId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Perfil eliminado correctamente');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el perfil');
        });
    }
}
</script>