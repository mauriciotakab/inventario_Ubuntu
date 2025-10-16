<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - TAKAB</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
    <div class="login-bg">
        <div class="login-container">
            <div class="login-logo"><img src="/assets/images/icono_takab.png" alt="logo_TAKAB" width="90" height="70""></div>
            <h1 class="login-title">TAKAB</h1>
            <p class="login-subtitle">Sistema de Inventario</p>
            <div class="login-form-area">
                <h2><i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión</h2>
                <p class="login-instructions">Ingresa tus credenciales para acceder al sistema</p>
                <?php if (isset($error)) echo "<p class='login-error'>$error</p>"; ?>
                <form method="POST" action="">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required autofocus placeholder="Ingresa tu usuario">
                    
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">
                    
                    <button type="submit">Iniciar Sesión</button>
                    <a class="forgot-link" href="forgot.php">Olvidé mi contraseña</a>

                </form>
                <div class="login-test-users">
                    <span>Usuarios de prueba:</span>
                    <ul>
                        <li><b>Admin:</b> admin / 123456</li>
                        <li><b>Almacén:</b> almacen / 123456</li>
                        <li><b>Empleado:</b> empleado / 123456</li>
                        <li><b>Empleado Prueba:</b> mau / 123456</li>
                        
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Font Awesome para el icono (opcional) -->
    <script src="https://kit.fontawesome.com/8b82fe7e0b.js" crossorigin="anonymous"></script>
</body>
</html>
