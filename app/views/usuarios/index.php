<?php
// Verificar que las variables estén definidas
$usuarios = $usuarios ?? [];
$pagination = $pagination ?? [];
$estadisticas = $estadisticas ?? [];
?>

<div class="service-detail">
    <!-- Header minimalista -->
    <div class="service-detail__header">
        <div class="service-detail__header-left">
            <div class="service-detail__title-section">
                <h1 class="service-detail__title">
                    Gestión de Usuarios
                </h1>
            </div>
        </div>
        <div class="service-detail__header-actions">
            <a href="<?= url('usuarios/create') ?>" class="btn btn--primary">
                <i class="fas fa-plus btn__icon"></i>Nuevo Usuario
            </a>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="service-detail__content">
        <div class="service-detail__main">

            <!-- Tabla de usuarios -->
            <div class="service-info-card">
                <div class="service-info-card__header">
                    <h3 class="service-info-card__title">
                        <i class="fas fa-list service-info-card__icon"></i>
                        Lista de Usuarios
                    </h3>
                </div>
                <div class="service-info-card__body">
                    <!-- Mensajes Flash -->
                    <?php if (isset($_SESSION['flash_messages'])): ?>
                        <?php foreach ($_SESSION['flash_messages'] as $type => $message): ?>
                            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?= $type === 'error' ? 'exclamation-triangle' : ($type === 'success' ? 'check-circle' : 'info-circle') ?> me-2"></i>
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endforeach; ?>
                        <?php unset($_SESSION['flash_messages']); ?>
                    <?php endif; ?>
                    
                    <div class="table-container">
                        <table class="table table-striped table-hover" id="usuariosTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Perfil</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">No hay usuarios para mostrar.</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td class="fw-bold"><?= $usuario['no_identificacion'] ?></td>
                                    <td>
                                        <?= ucfirst(strtolower(htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']))) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= htmlspecialchars($usuario['perfil_descripcion']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($usuario['telefono'] ?: 'N/A') ?></td>
                                    <td>
                                        <?php if ($usuario['activo']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?= url('usuarios/view/' . $usuario['no_identificacion']) ?>"
                                               class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="<?= url('usuarios/edit/' . $usuario['no_identificacion']) ?>"
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

                    <!-- Paginación -->
                    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="pagination-info">
                            Mostrando <?= $pagination['start_record'] ?> a <?= $pagination['end_record'] ?>
                            de <?= $pagination['total'] ?> usuarios
                        </div>
                        <nav>
                            <ul class="pagination">
                                <?php if ($pagination['has_previous']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?route=usuarios&page=<?= $pagination['current_page'] - 1 ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="?route=usuarios&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($pagination['has_next']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?route=usuarios&page=<?= $pagination['current_page'] + 1 ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Cambiar estado de usuario
function changeStatus(id, status) {
    const action = status ? 'activar' : 'desactivar';
    console.log(`Iniciando cambio de estado para usuario ${id} a ${action}`);
    
    if (confirm(`¿Está seguro de que desea ${action} este usuario?`)) {
        const url = '<?= url('usuarios/change-status/') ?>' + id;
        console.log('URL de la petición:', url);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Respuesta del servidor:', response);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos de respuesta:', data);
            if (data.success) {
                showSuccessMessage(data.message);
                console.log('Estado cambiado exitosamente, recargando página...');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                console.error('Error en la respuesta:', data.message);
                showErrorMessage(data.message || 'Error al cambiar el estado');
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            showErrorMessage('Error de conexión: ' + error.message);
        });
    } else {
        console.log('Operación cancelada por el usuario');
    }
}



// Funciones de mensajes
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

function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
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
</script>
</script>