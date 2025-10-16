<?php
Session::requireLogin();

$role = $_SESSION['role'];
$nombre = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - TAKAB</title>
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
                <div class="sidebar-desc">Dashboard</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <?php if ($role === 'Administrador'): ?>
                <a href="usuarios.php" class="active"><i class="fa-solid fa-users-cog"></i> Gestión de Usuarios</a>
                <a href="productos.php"><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</a>
                <a href="inventario_actual.php"><i class="fa-solid fa-list-check"></i> Inventario</a>
                <a href='revisar_solicitudes.php'><i class="fa-solid fa-comment-medical"></i>Solicitudes de Material</a>
                <a href="reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
                <a href="configuracion.php"><i class="fa-solid fa-gear"></i> Configuración</a>

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
        <!-- Topbar -->
        <header class="top-header">
            <div></div>
            <div class="top-header-user">
                <span><?= htmlspecialchars($nombre) ?> (<?= htmlspecialchars($role) ?>)</span>
                <i class="fa-solid fa-user-circle"></i>
                <a href="logout.php" class="logout-btn" title="Cerrar sesión">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </header>
        <main class="dashboard-main">
            <!-- Header de gestión -->
            <div class="usuarios-header-row2">
                <h1>Gestión de Usuarios</h1>
                <a class="btn-main" href="usuarios_create.php">
                    <i class="fa fa-plus"></i> Nuevo Usuario
                </a>
            </div>

            <!-- Usuarios activos -->
            <section class="usuarios-card">
                <div class="card-title">
                    <div>
                        <h2>Usuarios Activos</h2>
                        <span class="card-subtitle">Gestiona los usuarios del sistema</span>
                    </div>
                </div>
                <div class="usuarios-table-responsive">
                <table class="usuarios-flat-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$usuarios_activos = [];
$usuarios_inactivos = [];
foreach ($usuarios as $u) {
    if (!empty($u['activo'])) {
        $usuarios_activos[] = $u;
    } else {
        $usuarios_inactivos[] = $u;
    }
}
?>

                    <?php foreach($usuarios_activos as $u): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                            <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                            <td>
                                <span class="role-badge role-<?= strtolower($u['role']) ?>">
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge active">Activo</span>
                            </td>
                            <td>
                                <a class="btn-table" title="Editar" href="usuarios_edit.php?id=<?= $u['id'] ?>"><i class="fa fa-pen"></i></a>
                                <a class="btn-table" title="Desactivar" href="usuarios_setactive.php?id=<?= $u['id'] ?>&active=0"><i class="fa fa-user-slash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </section>

            <!-- Extrabajadores -->
            <section class="usuarios-card">
                <div class="card-title">
                    <div>
                        <h2>Extrabajadores</h2>
                        <span class="card-subtitle">Usuarios que ya no forman parte del sistema</span>
                    </div>
                </div>
                <div class="usuarios-table-responsive">
                <table class="usuarios-flat-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($usuarios_inactivos as $u): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                            <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                            <td>
                                <span class="role-badge role-<?= strtolower($u['role']) ?>">
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge inactive">Inactivo</span>
                            </td>
                            <td>
                                <a class="btn-table" title="Activar" href="usuarios_setactive.php?id=<?= $u['id'] ?>&active=1"><i class="fa fa-user-check"></i></a>
                                <a class="btn-table btn-danger" title="Eliminar" href="usuarios_delete.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar usuario?');"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </section>
        </main>
    </div>
</div>
</body>
</html>
