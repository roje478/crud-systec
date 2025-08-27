<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Servicios MVC</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery (requerido para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/assets/css/main.css">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar__header">
            <a href="https://www.systecsoluciones.com.co/app/index.php?route=clientes" class="sidebar__brand">
                <?php
                // Incluir EmpresaHelper si no está cargado
                if (!class_exists('EmpresaHelper')) {
                    require_once __DIR__ . '/../../app/helpers/EmpresaHelper.php';
                }

                $logoUrl = EmpresaHelper::getLogoUrl();
                $nombreEmpresa = EmpresaHelper::getNombre();
                ?>

                <?php if ($logoUrl): ?>
                    <div class="sidebar__brand-logo">
                        <img src="<?= $logoUrl ?>" alt="<?= htmlspecialchars($nombreEmpresa) ?>" class="sidebar__logo">
                    </div>
                <?php endif; ?>

            </a>
        </div>

        <nav class="sidebar__nav">
            <div class="sidebar__section">
                <ul class="sidebar__item">
                    <?php
                    // Incluir PermisoHelper si no está cargado
                    if (!class_exists('PermisoHelper')) {
                        require_once __DIR__ . '/../../app/helpers/PermisoHelper.php';
                    }

                    // Menú dinámico basado en permisos
                    $usuarioId = $_SESSION['usuario_id'] ?? null;

                    if ($usuarioId && class_exists('PermisoHelper')) {
                        // Usar menú dinámico
                        echo PermisoHelper::generarMenuHTML($usuarioId);
                    } else {
                        // Menú estático como fallback
                    ?>
                            <li>
                                <a href="<?= url('') ?>" class="sidebar__link">
                                    <i class="fas fa-home sidebar__link-icon"></i>
                                    <span class="sidebar__link-text">Dashboard</span>
                                </a>
                            </li>
                            <li class="sidebar__item--dropdown">
                                <a href="#" class="sidebar__link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#serviciosSubmenu">
                                    <i class="fas fa-tools sidebar__link-icon"></i>
                                    <span class="sidebar__link-text">Servicios</span>
                                    <i class="fas fa-chevron-down sidebar__link-arrow"></i>
                                </a>
                                <div class="collapse" id="serviciosSubmenu">
                                    <ul class="sidebar__submenu">
                                        <li class="sidebar__submenu-item">
                                            <a href="<?= url('servicios') ?>" class="sidebar__submenu-link">
                                                <i class="fas fa-list sidebar__submenu-icon"></i>
                                                <span>Listar Servicios</span>
                                            </a>
                                        </li>
                                        <li class="sidebar__submenu-item">
                                            <a href="<?= url('servicios/lista-completa') ?>" class="sidebar__submenu-link">
                                                <i class="fas fa-list-alt sidebar__submenu-icon"></i>
                                                <span>Lista Completa</span>
                                            </a>
                                        </li>
                                        <li class="sidebar__submenu-item">
                                            <a href="<?= url('servicios/create') ?>" class="sidebar__submenu-link">
                                                <i class="fas fa-plus sidebar__submenu-icon"></i>
                                                <span>Nuevo Servicio</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="sidebar__item--dropdown">
                                <a href="#" class="sidebar__link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#clientesSubmenu">
                                    <i class="fas fa-users sidebar__link-icon"></i>
                                    <span class="sidebar__link-text">Clientes</span>
                                    <i class="fas fa-chevron-down sidebar__link-arrow"></i>
                                </a>
                                <div class="collapse" id="clientesSubmenu">
                                    <ul class="sidebar__submenu">
                                        <li class="sidebar__submenu-item">
                                            <a href="<?= url('clientes') ?>" class="sidebar__submenu-link">
                                                <i class="fas fa-list sidebar__submenu-icon"></i>
                                                <span>Listar Clientes</span>
                                            </a>
                                        </li>
                                        <li class="sidebar__submenu-item">
                                            <a href="<?= url('clientes/create') ?>" class="sidebar__submenu-link">
                                                <i class="fas fa-plus sidebar__submenu-icon"></i>
                                                <span>Nuevo Cliente</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="sidebar__item--dropdown">
                                <a href="#" class="sidebar__link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#usuariosSubmenu">
                                    <i class="fas fa-user-cog sidebar__link-icon"></i>
                                    <span class="sidebar__link-text">Usuarios</span>
                                    <i class="fas fa-chevron-down sidebar__link-arrow"></i>
                                </a>
                                <div class="collapse" id="usuariosSubmenu">
                                    <ul class="sidebar__submenu">
                                        <li class="sidebar__submenu-item">
                                            <a href="<?= url('usuarios') ?>" class="sidebar__submenu-link">
                                                <i class="fas fa-list sidebar__submenu-icon"></i>
                                                <span>Listar Usuarios</span>
                                            </a>
                                        </li>
                                        <li class="sidebar__submenu-item">
                                            <a href="<?= url('usuarios/create') ?>" class="sidebar__submenu-link">
                                                <i class="fas fa-plus sidebar__submenu-icon"></i>
                                                <span>Nuevo Usuario</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <?php if (class_exists('PermisoHelper') && PermisoHelper::tienePermiso('permisos')): ?>
                                <li class="sidebar__item--dropdown">
                                    <a href="#" class="sidebar__link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#permisosSubmenu">
                                        <i class="fas fa-shield-alt sidebar__link-icon"></i>
                                        <span class="sidebar__link-text">Permisos</span>
                                        <i class="fas fa-chevron-down sidebar__link-arrow"></i>
                                    </a>
                                    <div class="collapse" id="permisosSubmenu">
                                        <ul class="sidebar__submenu">
                                            <li class="sidebar__submenu-item">
                                                <a href="<?= url('permisos') ?>" class="sidebar__submenu-link">
                                                    <i class="fas fa-list sidebar__submenu-icon"></i>
                                                    <span>Gestionar Permisos</span>
                                                </a>
                                            </li>
                                            <li class="sidebar__submenu-item">

                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>
                            <li class="sidebar__item--dropdown">
                                <a href="#" class="sidebar__link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#configuracionSubmenu">
                                    <i class="fas fa-cog sidebar__link-icon"></i>
                                    <span class="sidebar__link-text">Configuración</span>
                                    <i class="fas fa-chevron-down sidebar__link-arrow"></i>
                                </a>
                                <div class="collapse" id="configuracionSubmenu">
                                    <ul class="sidebar__submenu">
                                        <li class="sidebar__submenu-item">
                                            <a href="<?= url('clausulas') ?>" class="sidebar__submenu-link">
                                                <i class="fas fa-file-contract sidebar__submenu-icon"></i>
                                                <span>Gestionar Cláusulas</span>
                                            </a>
                                        </li>
                                        <li class="sidebar__submenu-item">
                                            <a href="<?= url('configuracion') ?>" class="sidebar__submenu-link">
                                                <i class="fas fa-cogs sidebar__submenu-icon"></i>
                                                <span>Configuración General</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        <?php
                    } else {
                        // Menú para usuarios no autenticados
                        ?>
                        <li>
                            <a href="<?= url('') ?>" class="sidebar__link">
                                <i class="fas fa-home sidebar__link-icon"></i>
                                <span class="sidebar__link-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= url('servicios') ?>" class="sidebar__link">
                                <i class="fas fa-tools sidebar__link-icon"></i>
                                <span class="sidebar__link-text">Servicios</span>
                            </a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <main class="main">
        <!-- Unified Header Container -->
        <div class="unified-header">
            <div class="unified-header__left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="unified-header__content">
                    <h1 class="unified-header__title" id="pageTitleMain">Lista de Servicios</h1>
                    <nav class="unified-header__breadcrumb" id="breadcrumbMain">
                        <a href="<?= url('') ?>">Dashboard</a>
                        <span>/</span>
                        <span>Lista de Servicios</span>
                    </nav>
                </div>
            </div>

            <div class="unified-header__right">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <div class="user-info">
                        <span class="user-info__name">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($_SESSION['usuario_nombre_completo'] ?? $_SESSION['usuario_id']) ?>
                        </span>
                        <span class="user-info__role">
                            <i class="fas fa-user-tag"></i>
                            <?= htmlspecialchars($_SESSION['usuario_perfil_nombre'] ?? 'Sin Perfil') ?>
                        </span>
                    </div>

                    <div class="user-actions">
                        <a href="<?= url('auth/logout') ?>" class="btn btn--logout" title="Cerrar Sesión">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="auth-actions">
                        <a href="<?= url('auth/login') ?>" class="btn btn--login">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Iniciar Sesión</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mensajes flash -->
        <?php if (isset($_SESSION['flash'])): ?>
            <?php $flash = $_SESSION['flash'];
            unset($_SESSION['flash']); ?>
            <div class="alert alert--<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="alert__close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif;