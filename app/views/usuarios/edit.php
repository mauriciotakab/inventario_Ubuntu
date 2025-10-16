<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="login-logo"><img src="/assets/images/icono_takab.png" alt="logo_TAKAB" width="90" height="55""></div>
            <div>
                <div class="sidebar-title">TAKAB</div>
                <div class="sidebar-desc">Gestión de Usuarios</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="solicitudes.php"><i class="fa-solid fa-inbox"></i> Solicitudes</a>
            <a href="productos.php"><i class="fa-solid fa-boxes-stacked"></i> Productos</a>
            <a href="inventario_actual.php"><i class="fa-solid fa-list-check"></i> Inventario</a>
            <a href="usuarios.php" class="active"><i class="fa-solid fa-users"></i> Usuarios</a>
            <a href="reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
            <a href="categorias.php"><i class="fa-solid fa-tags"></i> Categorías</a>
            <a href="almacenes.php"><i class="fa-solid fa-warehouse"></i> Almacenes</a>
            <a href="unidades.php"><i class="fa-solid fa-balance-scale"></i> Unidades</a>
            <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
        </nav>
    </aside>
    <div class="content-area">
        <!-- Topbar -->
        <header class="top-header">
            <div></div>
            <div class="top-header-user">
                <span><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Admin TAKAB'); ?></span>
                <i class="fa-solid fa-user-circle"></i>
                <a href="logout.php" class="logout-btn" title="Cerrar sesión">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </header>
        <!-- Formulario de edición -->
        <main class="dashboard-main">
            <div class="usuarios-header-row2">
                <h1>Editar Usuario</h1>
                <a class="btn-main" href="usuarios.php"><i class="fa fa-arrow-left"></i> Volver al listado</a>
            </div>
            <div class="form-box">
                <?php if (!empty($error)) echo "<p class='form-error'>$error</p>"; ?>
                <form class="usuario-form" method="post" action="">
                    <label>Nombre completo:</label>
                    <input type="text" name="nombre_completo" value="<?= htmlspecialchars($usuario['nombre_completo']) ?>" required>
                    <label>Usuario:</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required>
                    <label>Contraseña:</label>
                    <input type="password" name="password" placeholder="Dejar en blanco para no cambiar">
                    <label>Rol:</label>
                    <select name="role" required>
                        <option value="Empleado" <?= $usuario['role'] === 'Empleado' ? 'selected' : '' ?>>Empleado</option>
                        <option value="Almacen" <?= $usuario['role'] === 'Almacen' ? 'selected' : '' ?>>Almacén</option>
                        <option value="Administrador" <?= $usuario['role'] === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                    <label class="check-label">
                        <input type="checkbox" name="activo" <?= $usuario['activo'] ? 'checked' : '' ?>>
                        Activo
                    </label>
                    <button type="submit" class="btn-main"><i class="fa fa-save"></i> Guardar</button>
                </form>
            </div>
        </main>
    </div>
</div>
</body>
</html>
