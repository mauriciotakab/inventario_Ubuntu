<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña - TAKAB</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
    <div class="login-bg">
        <div class="login-container">
            <div class="login-logo"><img src="/assets/images/icono_takab.png" alt="logo_TAKAB" width="90" height="70""></div>
            <h1 class="login-title">TAKAB</h1>
            <p class="login-subtitle">Recuperar Contraseña</p>
            <p class="login-desc">Ingresa tu usuario para restaurar el acceso</p>
            <div class="login-form-area">
                <h2><i class="fa-solid fa-key"></i> Recuperar Contraseña</h2>
                <?php if (isset($mensaje)) echo "<p class='login-info'>$mensaje</p>"; ?>
                <form method="POST" action="">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required autofocus placeholder="Ingresa tu usuario">
                    <button type="submit">Recuperar</button>
                </form>
                <a class="forgot-link" href="login.php"><i class="fa-solid fa-arrow-left"></i> Volver a login</a>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/8b82fe7e0b.js" crossorigin="anonymous"></script>
</body>
</html>
