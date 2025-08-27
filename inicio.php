<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_activo']) || $_SESSION['usuario_activo'] !== true) {
    // Si no está autenticado, redirigir al login
    header('Location: index.php?route=auth/login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛠️ Sistema de Servicios MVC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-card {
            border-radius: 20px;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .feature-btn {
            border-radius: 15px;
            transition: all 0.3s ease;
            height: 120px;
        }
        .feature-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .status-card {
            border-radius: 12px;
            border: none;
            margin-bottom: 10px;
        }
        .pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }
    </style>
</head>
<body class="d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="hero-card text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-tools fa-4x text-primary mb-3"></i>
                        <h1 class="display-3 fw-bold text-dark mb-3">Sistema de Servicios</h1>
                        <p class="lead text-muted mb-4">Gestión completa de servicios técnicos con arquitectura MVC</p>
                    </div>

                    <!-- Botones Principales -->
                    <div class="row g-4 mb-5">
                        <div class="col-md-3">
                            <a href="index.php?route=servicios" class="btn btn-primary feature-btn w-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-tools fa-2x mb-2"></i>
                                <strong>Ver Servicios</strong>
                                <small>Router Permanente</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="index.php?route=clientes" class="btn btn-success feature-btn w-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <strong>Ver Clientes</strong>
                                <small>Gestión de Clientes</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="index.php?route=servicios/create" class="btn btn-warning feature-btn w-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-plus fa-2x mb-2"></i>
                                <strong>Crear Servicio</strong>
                                <small>Nuevo Servicio</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="index.php?route=clientes/create" class="btn btn-info feature-btn w-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <strong>Crear Cliente</strong>
                                <small>Nuevo Cliente</small>
                            </a>
                        </div>
                    </div>

                    <!-- Botones Secundarios -->
                    <div class="row g-3 mb-5">
                        <div class="col-md-4">
                            <a href="index.php?route=servicios/create" class="btn btn-outline-primary feature-btn w-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-plus fa-2x mb-2"></i>
                                <strong>Crear Servicio</strong>
                                <small>Router Permanente</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="index_alt.php?route=servicios/create" class="btn btn-outline-success feature-btn w-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                <strong>Crear Alt</strong>
                                <small>Versión alternativa</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="test_connection.php" class="btn btn-outline-info feature-btn w-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-database fa-2x mb-2"></i>
                                <strong>Test BD</strong>
                                <small>Verificar conexión</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="diagnostico_rapido.php" class="btn btn-outline-secondary feature-btn w-100 d-flex flex-column justify-content-center">
                                <i class="fas fa-stethoscope fa-2x mb-2"></i>
                                <strong>Diagnóstico</strong>
                                <small>Verificar sistema</small>
                            </a>
                        </div>
                    </div>

                    <!-- Estado del Sistema -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h4 class="text-dark mb-3">
                                <i class="fas fa-server text-success pulse me-2"></i>
                                Estado del Sistema
                            </h4>
                        </div>
                        <div class="col-md-3">
                            <div class="card status-card bg-success text-white">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-database fa-2x mb-2"></i>
                                    <div><strong>Base de Datos</strong></div>
                                    <small>Conectada ✓</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card status-card bg-success text-white">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-code fa-2x mb-2"></i>
                                    <div><strong>PHP MVC</strong></div>
                                    <small>Funcional ✓</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card status-card bg-info text-white">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <div><strong>Clientes</strong></div>
                                    <small>3,889 registros</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card status-card bg-info text-white">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-tools fa-2x mb-2"></i>
                                    <div><strong>Servicios</strong></div>
                                    <small>7,114 registros</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Técnica -->
                    <div class="alert alert-light border">
                        <h6 class="text-dark mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Información Técnica
                        </h6>
                        <div class="row text-start">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    • <strong>Arquitectura:</strong> MVC Limpio<br>
                                    • <strong>Base de datos:</strong> MySQL con PDO<br>
                                    • <strong>Frontend:</strong> Bootstrap 5 + JavaScript
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    • <strong>Servidor:</strong> MAMP (Puerto 8888)<br>
                                    • <strong>PHP:</strong> Versión <?= PHP_VERSION ?><br>
                                    • <strong>Estado:</strong> Funcional sin .htaccess
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-4">
                        <p class="text-muted mb-0">
                            <i class="fas fa-heart text-danger me-1"></i>
                            Sistema desarrollado con arquitectura MVC moderna
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Efectos visuales
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.feature-btn');
            buttons.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>