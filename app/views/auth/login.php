<?php
// Vista de login - Sin header ni footer para pantalla completa
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Servicios</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .login-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }

        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .demo-credentials {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 12px;
        }

        .demo-credentials h6 {
            color: #6c757d;
            margin-bottom: 10px;
        }

        .demo-credentials ul {
            margin: 0;
            padding-left: 20px;
        }

        .demo-credentials li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-shield-alt"></i> Sistema de Servicios</h1>
            <p>Inicia sesión para continuar</p>
        </div>

        <div class="login-body">
            <?php
            // Mostrar mensajes flash
            $flash = $this->getFlash();
            if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <i class="fas fa-<?= $flash['type'] === 'error' ? 'exclamation-triangle' : 'check-circle' ?>"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('auth/authenticate') ?>" id="loginForm">
                <div class="form-group">
                    <label for="identificacion" class="form-label">
                        <i class="fas fa-id-card"></i> Número de Identificación
                    </label>
                    <input type="text"
                           class="form-control"
                           id="identificacion"
                           name="identificacion"
                           placeholder="Ej: 1115064013"
                           required>
                </div>

                <div class="form-group">
                    <label for="contrasenia" class="form-label">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="password"
                               class="form-control"
                               id="contrasenia"
                               name="contrasenia"
                               placeholder="Ingresa tu contraseña"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>
        </div>
    </div>

    <script>
    // Validación del formulario
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const identificacion = document.getElementById('identificacion').value.trim();
        const contrasenia = document.getElementById('contrasenia').value.trim();

        if (!identificacion || !contrasenia) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Campos Requeridos',
                text: 'Por favor completa todos los campos',
                confirmButtonColor: '#007bff'
            });
        }
    });

    // Auto-completar para pruebas
    document.addEventListener('DOMContentLoaded', function() {
        // Si estamos en desarrollo, auto-completar con credenciales de prueba
        if (window.location.hostname === 'localhost') {
            document.getElementById('identificacion').value = '1115064013';
            document.getElementById('contrasenia').value = 'admin123';
        }
    });
    </script>
</body>
</html>