<?php
// Verificar que las variables estén definidas
$tipos = $tipos ?? [];
?>

<!-- Header de la página -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-plus text-success me-2"></i>
            Nueva Cláusula
        </h1>
        <p class="text-muted mb-0">Crea una nueva cláusula para el sistema</p>
    </div>
    <div>
        <a href="<?= url('clausulas') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Volver
        </a>
    </div>
</div>

<!-- Formulario de creación -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-contract text-primary me-2"></i>
                    Información de la Cláusula
                </h5>
            </div>
            <div class="card-body">
                <form id="formClausula" action="<?= url('clausulas/store') ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">
                                    <i class="fas fa-heading text-primary me-1"></i>
                                    Título <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="titulo"
                                       name="titulo"
                                       required
                                       maxlength="100"
                                       placeholder="Ej: Accesorios no registrados">
                                <div class="form-text">Título descriptivo de la cláusula</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">
                                    <i class="fas fa-tag text-primary me-1"></i>
                                    Tipo <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <?php foreach ($tipos as $valor => $nombre): ?>
                                        <option value="<?= $valor ?>"><?= $nombre ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Categoría de la cláusula</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left text-primary me-1"></i>
                            Descripción <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="descripcion"
                                  name="descripcion"
                                  rows="8"
                                  required
                                  placeholder="Escribe aquí el contenido de la cláusula..."></textarea>
                        <div class="form-text">
                            Contenido completo de la cláusula. Puedes usar HTML básico.
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="orden" class="form-label">
                                    <i class="fas fa-sort-numeric-up text-primary me-1"></i>
                                    Orden
                                </label>
                                <input type="number"
                                       class="form-control"
                                       id="orden"
                                       name="orden"
                                       min="1"
                                       placeholder="Orden de aparición">
                                <div class="form-text">Orden en que aparecerá la cláusula (opcional)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo" class="form-label">
                                    <i class="fas fa-code text-primary me-1"></i>
                                    Código
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="codigo"
                                       name="codigo"
                                       maxlength="20"
                                       placeholder="Código único (opcional)">
                                <div class="form-text">Código único para identificar la cláusula</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </button>
                        <div>
                            <button type="button" class="btn btn-outline-info me-2" onclick="previewClausula()">
                                <i class="fas fa-eye me-2"></i>
                                Vista Previa
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>
                                Guardar Cláusula
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Panel de ayuda -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-question-circle text-info me-2"></i>
                    Ayuda
                </h6>
            </div>
            <div class="card-body">
                <h6 class="text-primary">Tipos de Cláusulas:</h6>
                <ul class="list-unstyled">
                    <li><span class="badge bg-primary me-2">General</span> Cláusulas generales</li>
                    <li><span class="badge bg-info me-2">Servicio</span> Condiciones del servicio</li>
                    <li><span class="badge bg-warning me-2">Garantía</span> Términos de garantía</li>
                    <li><span class="badge bg-success me-2">Entrega</span> Condiciones de entrega</li>
                    <li><span class="badge bg-danger me-2">Pago</span> Términos de pago</li>
                </ul>

                <hr>

                <h6 class="text-primary">Consejos:</h6>
                <ul class="small">
                    <li>Usa títulos claros y descriptivos</li>
                    <li>Escribe el contenido de forma clara y profesional</li>
                    <li>Puedes usar HTML básico para formato</li>
                    <li>El orden determina la secuencia de aparición</li>
                    <li>El código es opcional, se genera automáticamente</li>
                </ul>
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
// Validación del formulario
document.getElementById('formClausula').addEventListener('submit', function(e) {
    const titulo = document.getElementById('titulo').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const tipo = document.getElementById('tipo').value;

    if (!titulo || !descripcion || !tipo) {
        e.preventDefault();
        Swal.fire('Error', 'Por favor complete todos los campos requeridos', 'error');
        return false;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Guardando...',
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

// Auto-generar código
document.getElementById('titulo').addEventListener('input', function() {
    const titulo = this.value.trim();
    const codigoInput = document.getElementById('codigo');

    if (codigoInput.value === '') {
        // Generar código basado en el título
        const codigo = titulo
            .toUpperCase()
            .replace(/[^A-Z0-9]/g, '_')
            .replace(/_+/g, '_')
            .replace(/^_|_$/g, '');

        if (codigo.length > 0) {
            codigoInput.value = codigo;
        }
    }
});

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