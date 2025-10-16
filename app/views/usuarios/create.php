<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario - TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-layout">
    <div class="content-area">
        <header class="top-header">
            <div></div>
            <div class="top-header-user">
                <span>Administrador</span>
                <i class="fa-solid fa-user-circle"></i>
            </div>
        </header>
        <main class="dashboard-main">
            <div class="usuarios-header-row">
                <h1>Agregar Usuario</h1>
                <a class="btn-primary" href="usuarios.php"><i class="fa fa-arrow-left"></i> Volver al listado</a>
            </div>
            <div class="form-box">
                <?php if (!empty($error)) echo "<p class='form-error'>$error</p>"; ?>
                <form class="usuario-form" method="post" action="">
                    <label>Nombre completo:</label>
                    <input type="text" name="nombre_completo" required>
                    <label>Usuario:</label>
                    <input type="text" name="username" required>
                    <label>Contraseña:</label>
                    <input type="password" name="password" required>
                    <label>Rol:</label>
                    <select name="role" required>
                        <option value="Empleado">Empleado</option>
                        <option value="Almacen">Almacén</option>
                        <option value="Administrador">Administrador</option>
                    </select>
                    <label class="check-label">
                        <input type="checkbox" name="activo" checked>
                        Activo
                    </label>
                    <button type="submit" class="btn-primary"><i class="fa fa-save"></i> Guardar</button>
                </form>
            </div>
        </main>
    </div>
</div>
</body>
</html>
