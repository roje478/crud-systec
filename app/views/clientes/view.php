<?php
/**
 * Vista Ver Cliente
 */

// Verificar que las variables estén definidas
$cliente = $cliente ?? [];
?>

<div class="service-detail">


    <!-- Información principal -->
    <div class="service-detail__content">
        <!-- Columna principal -->
        <div class="service-detail__main">
            <!-- Información del cliente -->
            <div class="service-info-card">
                <div class="service-info-card__header">
                    <div class="form-intro__wrapper">
                        <div class="service-info-card__header-left">
                            <div class="form-intro__icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <h5 class="service-info-card__title">Información Personal</h5>
                            <span class="status-badge status-badge--success">
                                Activo
                            </span>
                        </div>
                        <div class="form-intro__actions">
                            <button class="btn btn--outline" onclick="editCliente(<?= $cliente['no_identificacion'] ?>)">
                                <i class="fas fa-edit btn__icon"></i>Editar
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
                                <?= htmlspecialchars($cliente['no_identificacion']) ?>
                            </div>
                        </div>

                        <!-- Tipo de Identificación -->
                        <div class="service-info__field">
                            <label class="service-info__label">Tipo de Identificación</label>
                            <div class="service-info__input">
                                <i class="fas fa-credit-card service-info__icon"></i>
                                <?= htmlspecialchars(ucfirst(strtolower($cliente['tipo_id']))) ?>
                            </div>
                        </div>

                        <!-- Nombres -->
                        <div class="service-info__field">
                            <label class="service-info__label">Nombres</label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <?= htmlspecialchars(ucfirst(strtolower($cliente['nombres']))) ?>
                            </div>
                        </div>

                        <!-- Apellidos -->
                        <div class="service-info__field">
                            <label class="service-info__label">Apellidos</label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <?= htmlspecialchars(ucfirst(strtolower($cliente['apellidos']))) ?>
                            </div>
                        </div>

                        <!-- Nombre Completo -->
                        <div class="service-info__field service-info__field--two-columns">
                            <label class="service-info__label">Nombre Completo</label>
                            <div class="service-info__input">
                                <i class="fas fa-user service-info__icon"></i>
                                <?= htmlspecialchars(ucfirst(strtolower($cliente['nombres'] . ' ' . $cliente['apellidos']))) ?>
                            </div>
                        </div>

                        <!-- Género -->
                        <div class="service-info__field">
                            <label class="service-info__label">Género</label>
                            <div class="service-info__input">
                                <i class="fas fa-venus-mars service-info__icon"></i>
                                <?php
                                $generoText = '';
                                switch ($cliente['genero']) {
                                    case 'M':
                                        $generoText = 'Masculino';
                                        break;
                                    case 'F':
                                        $generoText = 'Femenino';
                                        break;
                                    default:
                                        $generoText = 'No especificado';
                                }
                                ?>
                                <?= htmlspecialchars($generoText) ?>
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="service-info__field">
                            <label class="service-info__label">Teléfono</label>
                            <div class="service-info__input">
                                <i class="fas fa-phone service-info__icon"></i>
                                <?= htmlspecialchars($cliente['telefono'] ?: 'N/A') ?>
                            </div>
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="service-info__field">
                            <label class="service-info__label">Fecha de Nacimiento</label>
                            <div class="service-info__input">
                                <i class="fas fa-calendar-alt service-info__icon"></i>
                                <?= $cliente['fecha_nacimiento'] ? date('d/m/Y', strtotime($cliente['fecha_nacimiento'])) : 'N/A' ?>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="service-info__field service-info__field--full-width">
                            <label class="service-info__label">Dirección</label>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-map-marker-alt service-info__icon"></i>
                                <?= htmlspecialchars(ucfirst(strtolower($cliente['direccion'] ?: 'N/A'))) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para las acciones -->
<script>
// Editar cliente
function editCliente(id) {
    window.location.href = '<?= url('clientes/edit/') ?>' + id;
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