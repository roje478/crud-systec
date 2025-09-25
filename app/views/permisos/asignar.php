<?php
// Vista para asignar permisos a un perfil
?>

<div class="page-actions">
    <a href="<?= url('permisos') ?>" class="btn btn--secondary">
        <i class="fas fa-arrow-left"></i>
        Volver a Permisos
    </a>
    <button type="submit" form="formPermisos" class="btn btn--primary">
        <i class="fas fa-save"></i>
        Guardar Permisos
    </button>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card__header">
                <h3 class="card__title">
                    <i class="fas fa-user-tag"></i>
                    Perfil: <?= htmlspecialchars($perfil['descripcion']) ?>
                </h3>
            </div>
            <div class="card__body">
                <form id="formPermisos" method="POST" action="<?= url('permisos/guardar-asignacion/' . $perfil['id']) ?>">
                    <!-- Campos ocultos para menús principales -->
                    <?php
                    $submenus = [];
                    foreach ($opciones as $opcion) {
                        if ($opcion['submenu'] == 1) {
                            $submenus[$opcion['codigo']] = $opcion;
                        }
                    }

                    // Crear campos ocultos para menús principales
                    foreach ($submenus as $codigo => $menuPrincipal) {
                        echo '<input type="hidden" name="menus_principales[]" value="' . $codigo . '" id="menu_' . $codigo . '">';
                    }
                    ?>

                    <div class="form__section">
                        <h4 class="form__section-title">
                            <i class="fas fa-list"></i>
                            Opciones Disponibles
                        </h4>

                        <?php if (!empty($opciones)): ?>
                            <div class="row">
                                <?php foreach ($submenus as $codigo => $menuPrincipal): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card card--outline">
                                            <div class="card__header">
                                                <h5 class="card__title">
                                                    <i class="<?= $menuPrincipal['icono'] ?: 'fas fa-folder' ?>"></i>
                                                    <?= htmlspecialchars($menuPrincipal['descripcion']) ?>
                                                </h5>
                                            </div>
                                            <div class="card__body">
                                                <?php
                                                // Filtrar subopciones excluyendo las opciones de editar
                                                $subopciones = array_filter($opciones, function($opcion) use ($codigo) {
                                                    // Excluir opciones de editar: 0203 (Editar Usuario), 0303 (Editar Cliente), 0403 (Editar Perfil)
                                                    $excluir = ['0203', '0303', '0403'];
                                                    return $opcion['submenu'] == 0 &&
                                                           strpos($opcion['codigo'], $codigo) === 0 &&
                                                           !in_array($opcion['codigo'], $excluir);
                                                });
                                                ?>

                                                <?php foreach ($subopciones as $subopcion): ?>
                                                    <div class="form__group">
                                                        <label class="form__label">
                                                            <input type="checkbox"
                                                                   name="opciones[]"
                                                                   value="<?= $subopcion['codigo'] ?>"
                                                                   class="form__control subopcion-checkbox"
                                                                   data-menu-principal="<?= $codigo ?>"
                                                                   <?= in_array($subopcion['codigo'], $codigosAsignados) ? 'checked' : '' ?>>
                                                            <span class="ml-2">
                                                                <?= htmlspecialchars($subopcion['descripcion']) ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Opciones sin submenu -->
                                <?php
                                // Filtrar opciones sin submenu excluyendo las opciones de editar
                                $opcionesSinSubmenu = array_filter($opciones, function($opcion) {
                                    // Excluir opciones de editar: 0203 (Editar Usuario), 0303 (Editar Cliente), 0403 (Editar Perfil)
                                    $excluir = ['0203', '0303', '0403'];
                                    return $opcion['submenu'] == 0 &&
                                           strlen($opcion['codigo']) <= 2 &&
                                           !in_array($opcion['codigo'], $excluir);
                                });
                                ?>

                                <?php if (!empty($opcionesSinSubmenu)): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card card--outline">
                                            <div class="card__header">
                                                <h5 class="card__title">
                                                    <i class="fas fa-link"></i>
                                                    Opciones Directas
                                                </h5>
                                            </div>
                                            <div class="card__body">
                                                <?php foreach ($opcionesSinSubmenu as $opcion): ?>
                                                    <div class="form__group">
                                                        <label class="form__label">
                                                            <input type="checkbox"
                                                                   name="opciones[]"
                                                                   value="<?= $opcion['codigo'] ?>"
                                                                   class="form__control"
                                                                   <?= in_array($opcion['codigo'], $codigosAsignados) ? 'checked' : '' ?>>
                                                            <span class="ml-2">
                                                                <i class="fas fa-link text-muted"></i>
                                                                <?= htmlspecialchars($opcion['descripcion']) ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Opciones especiales (códigos largos) -->
                                <?php
                                // Crear lista de descripciones ya mostradas en submenus
                                $descripcionesMostradas = [];
                                foreach ($submenus as $codigo => $menuPrincipal) {
                                    $subopciones = array_filter($opciones, function($opcion) use ($codigo) {
                                        return $opcion['submenu'] == 0 &&
                                               strpos($opcion['codigo'], $codigo) === 0;
                                    });
                                    foreach ($subopciones as $subopcion) {
                                        $descripcionesMostradas[] = $subopcion['descripcion'];
                                    }
                                }
                                
                                // Filtrar opciones especiales (códigos largos que no son subopciones)
                                $opcionesEspeciales = array_filter($opciones, function($opcion) use ($descripcionesMostradas) {
                                    // Excluir opciones específicas que aparecen en otras secciones
                                    $excluir = [
                                        'estados_servicio',
                                        'gestionar_clausulas', 
                                        'informacion_empresa',
                                        'tipos_servicio'
                                    ];
                                    
                                    return $opcion['submenu'] == 0 &&
                                           strlen($opcion['codigo']) > 2 &&
                                           !preg_match('/^\d{2}/', $opcion['codigo']) && // No empieza con 2 dígitos
                                           !in_array($opcion['codigo'], $excluir) && // No está en la lista de exclusión
                                           !in_array($opcion['descripcion'], $descripcionesMostradas); // No está ya mostrada en submenus
                                });
                                ?>

                                <?php if (!empty($opcionesEspeciales)): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card card--outline">
                                            <div class="card__header">
                                                <h5 class="card__title">
                                                    <i class="fas fa-cogs"></i>
                                                    Opciones Especiales
                                                </h5>
                                            </div>
                                            <div class="card__body">
                                                <?php foreach ($opcionesEspeciales as $opcion): ?>
                                                    <div class="form__group">
                                                        <label class="form__label">
                                                            <input type="checkbox"
                                                                   name="opciones[]"
                                                                   value="<?= $opcion['codigo'] ?>"
                                                                   class="form__control"
                                                                   <?= in_array($opcion['codigo'], $codigosAsignados) ? 'checked' : '' ?>>
                                                            <span class="ml-2">
                                                                <i class="<?= $opcion['icono'] ?: 'fas fa-cog' ?> text-muted"></i>
                                                                <?= htmlspecialchars($opcion['descripcion']) ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert--info">
                                <i class="fas fa-info-circle"></i>
                                No hay opciones disponibles para asignar.
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Funcionalidad para seleccionar/deseleccionar todos
document.addEventListener('DOMContentLoaded', function() {
    // Agregar botones de "Seleccionar Todo" por sección
    const cards = document.querySelectorAll('.card--outline');

    cards.forEach(card => {
        const header = card.querySelector('.card__header');
        const checkboxes = card.querySelectorAll('input[type="checkbox"]');

        if (checkboxes.length > 0) {
            const selectAllBtn = document.createElement('button');
            selectAllBtn.type = 'button';
            selectAllBtn.className = 'btn btn--sm btn--outline';
            selectAllBtn.innerHTML = '<i class="fas fa-check-square"></i> Seleccionar Todo';
            selectAllBtn.style.marginLeft = '10px';

            selectAllBtn.addEventListener('click', function() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
                this.innerHTML = allChecked ?
                    '<i class="fas fa-square"></i> Deseleccionar Todo' :
                    '<i class="fas fa-check-square"></i> Seleccionar Todo';
            });

            header.appendChild(selectAllBtn);
        }
    });

    // Funcionalidad para incluir automáticamente menús principales
    const subopcionCheckboxes = document.querySelectorAll('.subopcion-checkbox');

    function actualizarMenuPrincipal(menuPrincipal) {
        const menuPrincipalInput = document.getElementById('menu_' + menuPrincipal);
        if (menuPrincipalInput) {
            // Verificar si hay alguna subopción marcada para este menú principal
            const subopcionesDelMenu = document.querySelectorAll(`[data-menu-principal="${menuPrincipal}"]`);
            const haySubopcionesMarcadas = Array.from(subopcionesDelMenu).some(cb => cb.checked);

            if (haySubopcionesMarcadas) {
                // Si hay subopciones marcadas, incluir el menú principal
                menuPrincipalInput.setAttribute('data-included', 'true');
                menuPrincipalInput.style.display = 'none';
            } else {
                // Si no hay subopciones marcadas, quitar el menú principal
                menuPrincipalInput.removeAttribute('data-included');
            }
        }
    }

    // Inicializar estado de menús principales
    const menusPrincipales = document.querySelectorAll('input[name="menus_principales[]"]');
    menusPrincipales.forEach(input => {
        const menuPrincipal = input.value;
        actualizarMenuPrincipal(menuPrincipal);
    });

    // Event listeners para cambios en subopciones
    subopcionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const menuPrincipal = this.getAttribute('data-menu-principal');
            actualizarMenuPrincipal(menuPrincipal);
        });
    });
});

// Confirmación antes de guardar
document.getElementById('formPermisos').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('input[name="opciones[]"]:checked');
    const menuPrincipalInputs = document.querySelectorAll('input[name="menus_principales[]"][data-included]');

    console.log('Formulario enviado - Checkboxes marcados:', checkedBoxes.length);
    console.log('Menús principales incluidos:', menuPrincipalInputs.length);

    // Permitir el envío del formulario sin restricciones
    console.log('Enviando formulario...');
    return true;
});
</script>