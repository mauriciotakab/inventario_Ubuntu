<?php
Session::requireLogin();

$role = $_SESSION['role'];
$nombre = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías - TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/config.css">
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
                <div class="sidebar-desc">Dashboard</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <?php if ($role === 'Administrador'): ?>
                <a href="usuarios.php"><i class="fa-solid fa-users-cog"></i> Gestión de Usuarios</a>
                <a href="productos.php"><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</a>
                <a href="inventario_actual.php"><i class="fa-solid fa-list-check"></i> Inventario</a>
                <a href='revisar_solicitudes.php'><i class="fa-solid fa-comment-medical"></i>Solicitudes de Material</a>
                <a href="reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
                <a href="configuracion.php" class="active"><i class="fa-solid fa-gear"></i> Configuración</a>

            <?php elseif ($role === 'Almacen'): ?>
                <a href="productos.php"><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</a>
                <a href="solicitudes.php"><i class="fa-solid fa-inbox"></i> Gestionar Solicitudes</a>
                <a href='revisar_solicitudes.php'>Solicitudes de Material</a>
                <a href="configuracion_almacen">Configuración</a>

            <?php elseif ($role === 'Empleado'): ?>
                <a href="solicitudes_crear.php"><i class="fa-solid fa-plus-square"></i> Solicitar Material</a>
                <a href="mis_solicitudes.php"><i class="fa-solid fa-clipboard-list"></i> Mis Solicitudes</a>
                <a href='solicitar_material_general.php'><i class="bi bi-gear"></i> Solicitar Material/Herramienta General</a>


                <?php endif; ?>
            <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
        </nav>
    </aside>
    <div class="content-area">
        <header class="top-header">
            <div></div>
            <div class="top-header-user">
                <span><?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin TAKAB'); ?></span>
                <i class="fa-solid fa-user-circle"></i>
                <a href="../logout.php" class="logout-btn" title="Cerrar sesión">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </header>
        <main class="dashboard-main">
            <div class="main-table-card">
                <div class="main-table-header-row">
                    <div class="main-table-title">Categorías</div>
                    <a class="btn-main" href="categorias_create.php"><i class="fa fa-plus"></i> Agregar Categoría</a>
                </div>
                <?php if (isset($_GET['success'])) echo "<div class='form-success'>Operación exitosa.</div>"; ?>
                <?php if (isset($_GET['deleted'])) echo "<div class='form-error'>Categoría eliminada.</div>"; ?>
                <table class="takab-table">
                    <thead>
                        <tr>
                            <th>Nombre</th><th>Descripción</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categorias as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nombre']) ?></td>
                            <td><?= htmlspecialchars($c['descripcion']) ?></td>
                            <td class="table-actions">
                                <a href="categorias_edit.php?id=<?= $c['id'] ?>"><i class="fa fa-pen"></i> Editar</a>
                                <a href="categorias_delete.php?id=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar categoría?');" class="btn-danger"><i class="fa fa-trash"></i> Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
</body>
</html>
