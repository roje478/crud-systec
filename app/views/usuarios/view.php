<?php

/**
 * Vista Ver Usuario
 */

// Verificar que las variables estén definidas
$usuario = $usuario ?? [];

if (!$usuario) {
    echo '<div class="alert alert-danger">Usuario no encontrado</div>';
    return;
}
?>

<div class="service-detail">


    <!-- Información principal -->
    <div class="service-detail__content">
        <!-- Columna principal -->
        <div class="service-detail__main">
            <!-- Información del Cliente -->
            <div class="service-info-card">
                <div class="service-info-card__header">
                    <div class="form-intro__wrapper">
                        <div class="service-info-card__header-left">
                            <div class="form-intro__icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <h5 class="service-info-card__title">Información Personal</h5>
                            <?php if ($usuario['activo']): ?>
                                <span class="status-badge status-badge--success">
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="status-badge status-badge--danger">
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="service-detail__header-actions">
                            <button class="btn btn--outline" onclick="editUsuario(<?= $usuario['no_identificacion'] ?>)">
                                <i class="fas fa-edit btn__icon"></i>Editar
                            </button>
                            <button class="btn btn--outline" onclick="changeStatus(<?= $usuario['no_identificacion'] ?>, <?= $usuario['activo'] ? 0 : 1 ?>)">
                                <i class="fas fa-toggle-on btn__icon"></i><?= $usuario['activo'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                            <button class="btn btn--outline" onclick="window.print()">
                                <i class="fas fa-print btn__icon"></i>Imprimir
                            </button>
                        </div>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="service-info-grid">
                        <!-- Número de Identificación -->
                        <div class="service-info__field">
                            <label class="service-info__label">Número de Identificación</label>
                            <div class="service-info__input">
                                <i class="fas fa-id-card service-info__icon"></i>
                                <?= htmlspecialchars($usuario['no_identificacion']) ?>
                            </div>
                        </div>

                        <!-- Nombres -->
                        <div class="service-info__field">
                            <label class="service-info__label">Nombres</label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <?= htmlspecialchars(ucfirst(strtolower($usuario['nombres'] ?: 'N/A'))) ?>
                            </div>
                        </div>

                        <!-- Apellidos -->
                        <div class="service-info__field">
                            <label class="service-info__label">Apellidos</label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <?= htmlspecialchars(ucfirst(strtolower($usuario['apellidos'] ?: 'N/A'))) ?>
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="service-info__field">
                            <label class="service-info__label">Teléfono</label>
                            <div class="service-info__input">
                                <i class="fas fa-phone service-info__icon"></i>
                                <?= htmlspecialchars($usuario['telefono'] ?: 'N/A') ?>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="service-info__field service-info__field--two-columns">
                            <label class="service-info__label">Dirección</label>
                            <div class="service-info__input">
                                <i class="fas fa-map-marker-alt service-info__icon"></i>
                                <?= htmlspecialchars($usuario['direccion'] ?: 'N/A') ?>
                            </div>
                        </div>

                        <!-- Fecha de Registro -->
                        <div class="service-info__field">
                            <label class="service-info__label">Fecha de Registro</label>
                            <div class="service-info__input">
                                <i class="fas fa-calendar-plus service-info__icon"></i>
                                <?= isset($usuario['fecha_registro']) && $usuario['fecha_registro'] ? date('d/m/Y', strtotime($usuario['fecha_registro'])) : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Usuario -->
            <div class="service-info-card">
                <div class="service-info-card__header">
                    <div class="service-info-card__header-left">
                        <div class="form-intro__icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <h5 class="service-info-card__title">Información de Acceso</h5>
                        <?php if ($usuario['perfil_activo']): ?>
                            <span class="status-badge status-badge--success">
                                Perfil Activo
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-badge--warning">
                                Perfil Inactivo
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="service-info-card__body">
                    <div class="service-info-grid">
                        <!-- Perfil -->
                        <div class="service-info__field">
                            <label class="service-info__label">Perfil</label>
                            <div class="service-info__input">
                                <i class="fas fa-user-tag service-info__icon"></i>
                                <?= htmlspecialchars(ucfirst(strtolower($usuario['perfil_descripcion'] ?: 'N/A'))) ?>
                            </div>
                        </div>

                        <!-- Estado del Usuario -->
                        <div class="service-info__field">
                            <label class="service-info__label">Estado del Usuario</label>
                            <div class="service-info__input">
                                <i class="fas fa-toggle-on service-info__icon"></i>
                                <?php if ($usuario['activo']): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Inactivo
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Estado del Perfil -->
                        <div class="service-info__field">
                            <label class="service-info__label">Estado del Perfil</label>
                            <div class="service-info__input">
                                <i class="fas fa-shield-alt service-info__icon"></i>
                                <?php if ($usuario['perfil_activo']): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Perfil Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Perfil Inactivo
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Editar usuario
    function editUsuario(id) {
        window.location.href = '<?= url('usuarios/edit/') ?>' + id;
    }

    // Cambiar estado de usuario
    function changeStatus(id, status) {
        const action = status ? 'activar' : 'desactivar';
        if (confirm(`¿Está seguro de que desea ${action} este usuario?`)) {
            fetch('<?= url('usuarios/change-status/') ?>' + id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage(data.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showErrorMessage(data.message || 'Error al cambiar el estado');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('Error de conexión');
                });
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