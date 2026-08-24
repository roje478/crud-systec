<?php
/**
 * Modal de alta rápida de técnico externo + JS compartido del formulario de órdenes.
 * Se incluye FUERA del <form> para no romper la rejilla de campos.
 */
?>
<div class="modal fade" id="modalNuevoTecnico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Nuevo Técnico Externo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="form__group mb-3">
                    <label class="form__label" for="nuevoNombre">
                        Nombre <span class="form__required">*</span>
                    </label>
                    <input type="text" class="form__control" id="nuevoNombre" maxlength="150">
                </div>
                <div class="form__group mb-3">
                    <label class="form__label" for="nuevoTaller">Taller / Empresa</label>
                    <input type="text" class="form__control" id="nuevoTaller" maxlength="150">
                </div>
                <div class="form__group mb-3">
                    <label class="form__label" for="nuevoTelefono">Teléfono</label>
                    <input type="text" class="form__control" id="nuevoTelefono" maxlength="50">
                </div>
                <div class="form__group mb-3">
                    <label class="form__label" for="nuevoDocumento">Documento</label>
                    <input type="text" class="form__control" id="nuevoDocumento" maxlength="30">
                </div>
                <div class="alert alert--info mb-0">
                    Podrás completar el resto de los datos desde
                    <strong>Técnicos Externos &rarr; Editar</strong>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--outline" data-bs-dismiss="modal">
                    <i class="fas fa-ban btn__icon"></i>Cancelar
                </button>
                <button type="button" class="btn btn--primary" id="btnGuardarTecnicoRapido">
                    <i class="fas fa-save btn__icon"></i>Guardar y seleccionar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // Contador de caracteres del detalle del producto
    var detalle = document.getElementById('DetalleProducto');
    var contador = document.getElementById('contadorDetalle');

    function actualizarContador() {
        if (detalle && contador) { contador.textContent = detalle.value.length; }
    }

    if (detalle) {
        detalle.addEventListener('input', actualizarContador);
        actualizarContador();
    }

    // Si se elige quién recibe y no hay fecha, sugerir hoy
    var quienRecibe = document.getElementById('QuienRecibe');
    var fechaRecibe = document.getElementById('FechaRecibe');

    if (quienRecibe && fechaRecibe) {
        quienRecibe.addEventListener('change', function () {
            if (this.value && !fechaRecibe.value) {
                fechaRecibe.value = new Date().toISOString().slice(0, 10);
            }
            if (!this.value) { fechaRecibe.value = ''; }
        });
    }

    // Abrir el modal
    var btnAbrir = document.getElementById('btnAbrirModalTecnico');
    if (btnAbrir) {
        btnAbrir.addEventListener('click', function () {
            var modal = new bootstrap.Modal(document.getElementById('modalNuevoTecnico'));
            modal.show();
        });
    }

    // Alta rápida de técnico externo
    var btnGuardar = document.getElementById('btnGuardarTecnicoRapido');

    if (btnGuardar) {
        btnGuardar.addEventListener('click', function () {
            var nombre = document.getElementById('nuevoNombre').value.trim();

            if (!nombre) {
                Swal.fire('Falta el nombre', 'El nombre del técnico es obligatorio.', 'warning');
                return;
            }

            var payload = {
                nombre: nombre,
                taller: document.getElementById('nuevoTaller').value.trim(),
                telefono: document.getElementById('nuevoTelefono').value.trim(),
                documento: document.getElementById('nuevoDocumento').value.trim()
            };

            btnGuardar.disabled = true;

            fetch('<?= url('tecnicos-externos/store-ajax') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btnGuardar.disabled = false;

                if (!data.success) {
                    Swal.fire('No se pudo crear', data.message, 'error');
                    return;
                }

                var select = document.getElementById('IdTecnicoExterno');
                var option = document.createElement('option');
                option.value = data.tecnico.id;
                option.textContent = data.tecnico.nombre + (data.tecnico.taller ? ' — ' + data.tecnico.taller : '');
                option.selected = true;
                select.appendChild(option);

                var modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoTecnico'));
                if (modal) { modal.hide(); }

                document.getElementById('nuevoNombre').value = '';
                document.getElementById('nuevoTaller').value = '';
                document.getElementById('nuevoTelefono').value = '';
                document.getElementById('nuevoDocumento').value = '';

                Swal.fire({ icon: 'success', title: data.message, timer: 1400, showConfirmButton: false });
            })
            .catch(function () {
                btnGuardar.disabled = false;
                Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
            });
        });
    }
})();
</script>
