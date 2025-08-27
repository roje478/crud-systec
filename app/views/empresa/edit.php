<?php
// Verificar que las variables estén definidas
$empresa = $empresa ?? [];
?>

<div class="service-detail">

    <!-- Contenido principal -->
    <div class="service-detail__content">
        <div class="service-detail__main">
            <!-- Formulario de información de la empresa -->
            <div class="service-info-card service-info-card--form">
                <div class="service-info-card__body">
                    <div class="form-intro">
                        <div class="form-intro__wrapper">
                            <div class="form-intro__icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="form-intro__content">
                                <h3 class="form-intro__title">Información de la Empresa</h3>
                                <p class="form-intro__description">Actualice la información de contacto y datos de la empresa</p>
                            </div>

                            <div class="form-intro__actions">
                                <button type="button" class="btn btn--outline" id="discardBtn" disabled>
                                    <i class="fas fa-times btn__icon"></i>Descartar Cambios
                                </button>
                                <button type="button" class="btn btn--outline" id="cancelBtn">
                                    <i class="fas fa-ban btn__icon"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn--primary" id="submitBtn" form="editEmpresaForm">
                                    <i class="fas fa-save btn__icon"></i>Actualizar Información
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="editEmpresaForm" class="service-info-grid" action="<?= url('empresa/update') ?>" method="POST" enctype="multipart/form-data">

                        <!-- Logo de la Empresa -->
                        <div class="service-info__field service-info__field--two-columns">
                            <label class="service-info__label" for="logo">
                                Logo de la Empresa
                            </label>
                            <div class="service-info__input">
                                <div class="logo-upload-container">
                                    <!-- Logo actual -->
                                    <div class="logo-preview" id="logoPreview">
                                        <?php if (!empty($empresa['logo']) && file_exists($empresa['logo'])): ?>
                                            <img src="<?= url($empresa['logo']) ?>" alt="Logo actual" class="logo-image">
                                        <?php else: ?>
                                            <div class="logo-placeholder">
                                                <i class="fas fa-building"></i>
                                                <span>Sin logo</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Input de archivo -->
                                    <div class="file-input-container">
                                        <input type="file"
                                               class="form__control file-input"
                                               id="logo"
                                               name="logo"
                                               accept="image/jpeg,image/jpg,image/png,image/gif"
                                               style="display: none;">
                                        <button type="button" class="btn btn--outline btn--small" id="selectLogoBtn">
                                            <i class="fas fa-upload btn__icon"></i>Seleccionar Logo
                                        </button>
                                        <button type="button" class="btn btn--outline btn--small btn--danger" id="removeLogoBtn" style="display: none;">
                                            <i class="fas fa-trash btn__icon"></i>Eliminar
                                        </button>
                                    </div>

                                    <div class="logo-info">
                                        <small>Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-logo"></div>
                        </div>

                        <!-- NIT -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="nit">
                                NIT
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-id-card service-info__icon"></i>
                                <input type="text"
                                       class="form__control"
                                       id="nit"
                                       name="nit"
                                       maxlength="20"
                                       value="<?= htmlspecialchars($empresa['nit']) ?>"
                                       placeholder="Número de identificación tributaria">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-nit"></div>
                        </div>

                        <!-- Nombre de la Empresa -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="nombreempresa">
                                Nombre de la Empresa <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-building service-info__icon"></i>
                                <input type="text"
                                       class="form__control"
                                       id="nombreempresa"
                                       name="nombreempresa"
                                       required
                                       maxlength="250"
                                       value="<?= htmlspecialchars($empresa['nombreempresa']) ?>"
                                       placeholder="Nombre de la empresa">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-nombreempresa"></div>
                        </div>

                        <!-- Correo Electrónico -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="correo">
                                Correo Electrónico <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-envelope service-info__icon"></i>
                                <input type="email"
                                       class="form__control"
                                       id="correo"
                                       name="correo"
                                       required
                                       maxlength="250"
                                       value="<?= htmlspecialchars($empresa['correo']) ?>"
                                       placeholder="correo@empresa.com">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-correo"></div>
                        </div>

                        <!-- Dirección -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="direccion">
                                Dirección <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-map-marker-alt service-info__icon"></i>
                                <input type="text"
                                       class="form__control"
                                       id="direccion"
                                       name="direccion"
                                       required
                                       maxlength="100"
                                       value="<?= htmlspecialchars($empresa['direccion']) ?>"
                                       placeholder="Dirección de la empresa">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-direccion"></div>
                        </div>

                        <!-- Teléfono -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="telefono">
                                Teléfono <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-phone service-info__icon"></i>
                                <input type="text"
                                       class="form__control"
                                       id="telefono"
                                       name="telefono"
                                       required
                                       maxlength="100"
                                       value="<?= htmlspecialchars($empresa['telefono']) ?>"
                                       placeholder="Teléfono de contacto">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-telefono"></div>
                        </div>

                        <!-- Nombre del Representante -->
                        <div class="service-info__field">
                            <label class="service-info__label" for="nombrerepresentante">
                                Nombre del Representante <span class="form__required">*</span>
                            </label>
                            <div class="service-info__input">
                                <i class="fas fa-user-tie service-info__icon"></i>
                                <input type="text"
                                       class="form__control"
                                       id="nombrerepresentante"
                                       name="nombrerepresentante"
                                       required
                                       maxlength="100"
                                       value="<?= htmlspecialchars($empresa['nombrerepresentante']) ?>"
                                       placeholder="Nombre del representante legal">
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-nombrerepresentante"></div>
                        </div>

                        <!-- Descripción -->
                        <div class="service-info__field service-info__field--two-columns">
                            <label class="service-info__label" for="descripcion">
                                Descripción
                            </label>
                            <div class="service-info__input service-info__input--textarea">
                                <i class="fas fa-align-left service-info__icon"></i>
                                <textarea class="form__control"
                                          id="descripcion"
                                          name="descripcion"
                                          rows="4"
                                          maxlength="500"
                                          placeholder="Descripción de la empresa..."><?= htmlspecialchars($empresa['descripcion']) ?></textarea>
                            </div>
                            <div class="form__feedback form__feedback--invalid" id="error-descripcion"></div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let formChanged = false;

// Detectar cambios en el formulario
document.querySelectorAll('#editEmpresaForm input, #editEmpresaForm select, #editEmpresaForm textarea').forEach(element => {
    element.addEventListener('change', function() {
        formChanged = true;
        document.getElementById('discardBtn').disabled = false;
    });
});

// Manejo del logo
const logoInput = document.getElementById('logo');
const logoPreview = document.getElementById('logoPreview');
const selectLogoBtn = document.getElementById('selectLogoBtn');
const removeLogoBtn = document.getElementById('removeLogoBtn');

// Seleccionar logo
selectLogoBtn.addEventListener('click', function() {
    logoInput.click();
});

// Previsualizar logo seleccionado
logoInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        // Validar tipo de archivo
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire('Error', 'Solo se permiten archivos JPG, PNG y GIF', 'error');
            this.value = '';
            return;
        }

        // Validar tamaño (2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'El archivo es demasiado grande. Máximo 2MB', 'error');
            this.value = '';
            return;
        }

        // Mostrar previsualización
        const reader = new FileReader();
        reader.onload = function(e) {
            logoPreview.innerHTML = `<img src="${e.target.result}" alt="Logo seleccionado" class="logo-image">`;
            removeLogoBtn.style.display = 'inline-block';
        };
        reader.readAsDataURL(file);

        formChanged = true;
        document.getElementById('discardBtn').disabled = false;
    }
});

// Eliminar logo
removeLogoBtn.addEventListener('click', function() {
    Swal.fire({
        title: '¿Eliminar logo?',
        text: 'Se eliminará el logo seleccionado',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            logoInput.value = '';
            logoPreview.innerHTML = `
                <div class="logo-placeholder">
                    <i class="fas fa-building"></i>
                    <span>Sin logo</span>
                </div>
            `;
            removeLogoBtn.style.display = 'none';
            formChanged = true;
            document.getElementById('discardBtn').disabled = false;
        }
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
                window.location.href = '<?= url('servicios') ?>';
            }
        });
    } else {
        window.location.href = '<?= url('servicios') ?>';
    }
});

// Validación del formulario
document.getElementById('editEmpresaForm').addEventListener('submit', function(e) {
    const nombreempresa = document.getElementById('nombreempresa').value.trim();
    const correo = document.getElementById('correo').value.trim();
    const direccion = document.getElementById('direccion').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const nombrerepresentante = document.getElementById('nombrerepresentante').value.trim();

    // Limpiar errores anteriores
    document.querySelectorAll('.form__feedback').forEach(feedback => {
        feedback.textContent = '';
        feedback.style.display = 'none';
    });

    let hasErrors = false;

    if (!nombreempresa) {
        document.getElementById('error-nombreempresa').textContent = 'El nombre de la empresa es requerido';
        document.getElementById('error-nombreempresa').style.display = 'block';
        hasErrors = true;
    }

    if (!correo) {
        document.getElementById('error-correo').textContent = 'El correo electrónico es requerido';
        document.getElementById('error-correo').style.display = 'block';
        hasErrors = true;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        document.getElementById('error-correo').textContent = 'El correo electrónico no es válido';
        document.getElementById('error-correo').style.display = 'block';
        hasErrors = true;
    }

    if (!direccion) {
        document.getElementById('error-direccion').textContent = 'La dirección es requerida';
        document.getElementById('error-direccion').style.display = 'block';
        hasErrors = true;
    }

    if (!telefono) {
        document.getElementById('error-telefono').textContent = 'El teléfono es requerido';
        document.getElementById('error-telefono').style.display = 'block';
        hasErrors = true;
    }

    if (!nombrerepresentante) {
        document.getElementById('error-nombrerepresentante').textContent = 'El nombre del representante es requerido';
        document.getElementById('error-nombrerepresentante').style.display = 'block';
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
</script>