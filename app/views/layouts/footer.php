    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- JavaScript global -->
    <script>
        // Función para cambiar estado via AJAX (actualizada)
        function changeStatus(servicioId, newStatus, estadoNombre) {
            Swal.fire({
                title: '¿Cambiar estado?',
                text: `¿Estás seguro de cambiar el estado del servicio a "${estadoNombre}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= url('servicios/changeStatus/') ?>' + servicioId,
                        method: 'POST',
                        data: JSON.stringify({ estado: newStatus }),
                        contentType: 'application/json',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('¡Éxito!', `Estado cambiado exitosamente a "${estadoNombre}"`, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error AJAX:', xhr.responseText);
                            Swal.fire('Error', 'Error de conexión: ' + error, 'error');
                        }
                    });
                }
            });
        }

        // Manejar estado activo del menú
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname + window.location.search;
            const pageTitleMain = document.getElementById('pageTitleMain');
            const breadcrumbMain = document.getElementById('breadcrumbMain');

            // Actualizar título y breadcrumb según la página
            if (currentPath.includes('route=servicios/create')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Nuevo Servicio';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Servicios / Nuevo Servicio';
                }
            } else if (currentPath.includes('route=servicios/view/')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Detalle del Servicio';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Servicios / Detalle del Servicio';
                }
            } else if (currentPath.includes('route=servicios/buscar')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Buscar Servicios';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Servicios / Buscar Servicios';
                }
            } else if (currentPath.includes('route=servicios')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Lista de Servicios';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Servicios';
                }
            } else if (currentPath.includes('route=clientes/create')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Nuevo Cliente';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Clientes / Nuevo Cliente';
                }
            } else if (currentPath.includes('route=clientes/view/')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Detalle del Cliente';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Clientes / Detalle del Cliente';
                }
            } else if (currentPath.includes('route=clientes')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Lista de Clientes';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Clientes';
                }
            } else if (currentPath.includes('route=usuarios/create')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Nuevo Usuario';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Usuarios / Nuevo Usuario';
                }
            } else if (currentPath.includes('route=usuarios/view/')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Detalle del Usuario';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Usuarios / Detalle del Usuario';
                }
            } else if (currentPath.includes('route=usuarios/edit/')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Editar Usuario';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Usuarios / Editar Usuario';
                }
            } else if (currentPath.includes('route=usuarios')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Lista de Usuarios';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard / Lista de Usuarios';
                }
            } else if (currentPath.includes('route=permisos/create')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Crear Perfil';
                if (breadcrumbMain) {
                    breadcrumbMain.innerHTML = '<a href="<?= url('') ?>">Dashboard</a><span>/</span><a href="<?= url('permisos') ?>">Permisos</a><span>/</span><span>Crear Perfil</span>';
                }
            } else if (currentPath.includes('route=permisos/asignar/')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Asignar Permisos';
                if (breadcrumbMain) {
                    breadcrumbMain.innerHTML = '<a href="<?= url('') ?>">Dashboard</a><span>/</span><a href="<?= url('permisos') ?>">Permisos</a><span>/</span><span>Asignar Permisos</span>';
                }
            } else if (currentPath.includes('route=permisos')) {
                if (pageTitleMain) pageTitleMain.textContent = 'Gestión de Permisos';
                if (breadcrumbMain) {
                    breadcrumbMain.innerHTML = '<a href="<?= url('') ?>">Dashboard</a><span>/</span><span>Permisos</span>';
                }
            } else {
                if (pageTitleMain) pageTitleMain.textContent = 'Dashboard';
                if (breadcrumbMain) {
                    breadcrumbMain.textContent = 'Dashboard';
                }
            }

            // Verificar si estamos en una página de servicios
            if (currentPath.includes('route=servicios')) {
                // Expandir el submenú de servicios
                const serviciosSubmenu = document.getElementById('serviciosSubmenu');
                if (serviciosSubmenu) {
                    serviciosSubmenu.classList.add('show');
                }

                // Marcar el item correspondiente como activo
                if (currentPath.includes('route=servicios/create')) {
                    // Estamos en crear servicio
                    const createLink = document.querySelector('a[href*="servicios/create"]');
                    if (createLink) {
                        createLink.classList.add('active');
                    }
                } else {
                    // Estamos en listar servicios (página principal de servicios)
                    const listLink = document.querySelector('a[href*="route=servicios"]:not([href*="create"])');
                    if (listLink) {
                        listLink.classList.add('active');
                    }
                }
            }

            // Verificar si estamos en una página de clientes
            if (currentPath.includes('route=clientes')) {
                // Expandir el submenú de clientes
                const clientesSubmenu = document.getElementById('clientesSubmenu');
                if (clientesSubmenu) {
                    clientesSubmenu.classList.add('show');
                }

                // Marcar el item correspondiente como activo
                if (currentPath.includes('route=clientes/create')) {
                    // Estamos en crear cliente
                    const createLink = document.querySelector('a[href*="clientes/create"]');
                    if (createLink) {
                        createLink.classList.add('active');
                    }
                } else {
                    // Estamos en listar clientes (página principal de clientes)
                    const listLink = document.querySelector('a[href*="route=clientes"]:not([href*="create"])');
                    if (listLink) {
                        listLink.classList.add('active');
                    }
                }
            }

            // Verificar si estamos en una página de usuarios
            if (currentPath.includes('route=usuarios')) {
                // Expandir el submenú de usuarios
                const usuariosSubmenu = document.getElementById('usuariosSubmenu');
                if (usuariosSubmenu) {
                    usuariosSubmenu.classList.add('show');
                }

                // Marcar el item correspondiente como activo
                if (currentPath.includes('route=usuarios/create')) {
                    // Estamos en crear usuario
                    const createLink = document.querySelector('a[href*="usuarios/create"]');
                    if (createLink) {
                        createLink.classList.add('active');
                    }
                } else {
                    // Estamos en listar usuarios (página principal de usuarios)
                    const listLink = document.querySelector('a[href*="route=usuarios"]:not([href*="create"])');
                    if (listLink) {
                        listLink.classList.add('active');
                    }
                }
            }
        });

        // Toggle del sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const main = document.querySelector('.main');

            if (sidebarToggle && sidebar && main) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    main.classList.toggle('expanded');
                });
            }

            // Funcionalidad del botón de logout
            const logoutBtn = document.querySelector('.btn--logout');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '¿Cerrar sesión?',
                        text: '¿Estás seguro de que quieres cerrar sesión?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cerrar sesión',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = this.href;
                        }
                    });
                });
            }

            // Funcionalidad del botón de login
            const loginBtn = document.querySelector('.btn--login');
            if (loginBtn) {
                loginBtn.addEventListener('click', function(e) {
                    // No prevenir el comportamiento por defecto para el login
                    // Solo agregar una transición suave
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            }
        });
    </script>
</body>
</html>