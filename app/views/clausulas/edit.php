<?php
// Verificar que las variables estén definidas
$clausula = $clausula ?? [];
$tipos = $tipos ?? [];
?>

<div class="service-detail">

    <!-- Contenido principal -->
    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Formulario de información de la cláusula -->
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Editar Cláusula</h3>
                                <p class="form-intro__description">Modifique la información de la cláusula según sea necesario</p>
                            </div>

                            <div class="form-intro__actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <button type="button" class="btn btn--outline" id="cancelBtn">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="editClausulaForm">
                                    <i class="fas fa-save btn__icon"></i>Actualizar Cláusula
                                </button>
                            </div>
                        </div>
                    </div>

                                        <form id="editClausulaForm" class="service-info-grid" action="<?= url('clausulas/update/' . $clausula['id']) ?>" method="POST">

                        <!-- Código -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="codigo">
                                Código
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-code service-info__icon"></i>
                                <input type="text"
                                       class="form__control"
                                       id="codigo"
                                       name="codigo"
                                       maxlength="20"
                                       value="<?= htmlspecialchars($clausula['codigo']) ?>"
                                       placeholder="Código único">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-codigo"></div>
                        </div>

                        <!-- Título -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="titulo">
                                Título <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-heading service-info__icon"></i>
                                <input type="text"
                                       class="form__control"
                                       id="titulo"
                                       name="titulo"
                                       required
                                       maxlength="100"
                                       value="<?= htmlspecialchars($clausula['titulo']) ?>"
                                       placeholder="Ej: Accesorios no registrados">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-titulo"></div>
                        </div>

                        <!-- Tipo -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="tipo">
                                Tipo <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-tag service-info__icon"></i>
                                <select class="form__control" id="tipo" name="tipo" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <?php foreach ($tipos as $valor => $nombre): ?>
                                        <option value="<?= $valor ?>" <?= $clausula['tipo'] === $valor ? 'selected' : '' ?>>
                                            <?= $nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-tipo"></div>
                        </div>

                        <!-- Descripción (2 columnas) -->
                        <div class="service-info__field service-info__field--two-columns">
                            <label class="service-info__label" for="descripcion">
                                Descripción <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-align-left service-info__icon"></i>
                                <textarea class="form__control"
                                          id="descripcion"
                                          name="descripcion"
                                          rows="8"
                                          required
                                          placeholder="Escribe aquí el contenido de la cláusula..."><?= htmlspecialchars($clausula['descripcion']) ?></textarea>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-descripcion"></div>
                        </div>

                        <!-- Orden -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="orden">
                                Orden
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-sort-numeric-up service-info__icon"></i>
                                <input type="number"
                                       class="form__control"
                                       id="orden"
                                       name="orden"
                                       min="1"
                                       value="<?= $clausula['orden'] ?>"
                                       placeholder="Orden de aparición">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-orden"></div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="service-info-card service-info-card--info">
                        <div class="service-info-card__header">
                            <h4 class="service-info-card__title">
                                <i class="fas fa-info-circle service-info-card__icon"></i>
                                Información de la Cláusula
                            </h4>
                        </div>
                        <div class="service-info-card__body">
                            <div class="service-info-grid">
                                <div class="service-info__field">
                                    <div class="service-info__value">
                                        <i class="fas fa-calendar service-info__icon"></i>
                                        <span><strong>Creada:</strong> <?= date('d/m/Y H:i', strtotime($clausula['fecha_creacion'])) ?></span>
                                    </div>
                                </div>
                                <div class="service-info__field">
                                    <div class="service-info__value">
                                        <i class="fas fa-clock service-info__icon"></i>
                                        <span><strong>Última modificación:</strong> <?= date('d/m/Y H:i', strtotime($clausula['fecha_modificacion'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>

        <!-- Vista previa -->
        <div class="card mt-3" id="previewCard" style="display: none;">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-eye text-info me-2"></i>
                    Vista Previa
                </h6>
            </div>
            <div class="card-body" id="previewContent">
                <!-- Contenido de vista previa -->
            </div>
        </div>
    </div>
</div>

<!-- Modal de vista previa -->
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye text-info me-2"></i>
                    Vista Previa de la Cláusula
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalPreviewContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let formChanged = false;

// Detectar cambios en el formulario
document.querySelectorAll('#editClausulaForm input, #editClausulaForm select, #editClausulaForm textarea').forEach(element => {
    element.addEventListener('change', function() {
        formChanged = true;
        document.getElementById('discardBtn').disabled = false;
    });
});

// Botón descartar cambios
document.getElementById('discardBtn').addEventListener('click', function() {
    if (formChanged) {
        Swal.fire({
            title: '¿Descartar cambios?',
            text: 'Los cambios no guardados se perderán',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    }
});

// Botón cancelar
document.getElementById('cancelBtn').addEventListener('click', function() {
    if (formChanged) {
        Swal.fire({
            title: '¿Cancelar edición?',
            text: 'Los cambios no guardados se perderán',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'Continuar editando'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= url('clausulas') ?>';
            }
        });
    } else {
        window.location.href = '<?= url('clausulas') ?>';
    }
});

// Validación del formulario
document.getElementById('editClausulaForm').addEventListener('submit', function(e) {
    const titulo = document.getElementById('titulo').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const tipo = document.getElementById('tipo').value;

    // Limpiar errores anteriores
    document.querySelectorAll('.form__feedback').forEach(feedback => {
        feedback.textContent = '';
        feedback.style.display = 'none';
    });

    let hasErrors = false;

    if (!titulo) {
        document.getElementById('error-titulo').textContent = 'El título es requerido';
        document.getElementById('error-titulo').style.display = 'block';
        hasErrors = true;
    }

    if (!descripcion) {
        document.getElementById('error-descripcion').textContent = 'La descripción es requerida';
        document.getElementById('error-descripcion').style.display = 'block';
        hasErrors = true;
    }

    if (!tipo) {
        document.getElementById('error-tipo').textContent = 'El tipo es requerido';
        document.getElementById('error-tipo').style.display = 'block';
        hasErrors = true;
    }

    if (hasErrors) {
        e.preventDefault();
        Swal.fire('Error', 'Por favor complete todos los campos requeridos', 'error');
        return false;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Actualizando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
});

// Vista previa
function previewClausula() {
    const titulo = document.getElementById('titulo').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const tipo = document.getElementById('tipo').value;

    if (!titulo || !descripcion || !tipo) {
        Swal.fire('Error', 'Por favor complete título, descripción y tipo para ver la vista previa', 'error');
        return;
    }

    // Generar vista previa
    const previewContent = `
        <div class="border rounded p-3 bg-light">
            <h5 class="text-primary mb-3">
                <i class="fas fa-file-contract me-2"></i>
                ${titulo}
            </h5>
            <div class="mb-2">
                <span class="badge bg-info">${tipo}</span>
            </div>
            <div class="border-top pt-3">
                ${descripcion}
            </div>
        </div>
    `;

    // Mostrar en modal
    document.getElementById('modalPreviewContent').innerHTML = previewContent;
    new bootstrap.Modal(document.getElementById('modalPreview')).show();
}

// Cambiar estado
function cambiarEstado(id, nuevoEstado) {
    const accion = nuevoEstado ? 'activar' : 'desactivar';

    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas ${accion} esta cláusula?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= url('clausulas/cambiar-estado/') ?>${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ activo: nuevoEstado })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Éxito', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al cambiar estado', 'error');
            });
        }
    });
}

// Eliminar cláusula
function eliminarClausula(id, titulo) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas eliminar la cláusula "${titulo}"? Esta acción no se puede deshacer.`,
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

// Contador de caracteres
document.getElementById('titulo').addEventListener('input', function() {
    const maxLength = 100;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;

    if (remaining < 20) {
        this.classList.add('is-warning');
    } else {
        this.classList.remove('is-warning');
    }

    if (remaining < 0) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});

// Validación en tiempo real
document.getElementById('descripcion').addEventListener('input', function() {
    if (this.value.trim().length < 10) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>