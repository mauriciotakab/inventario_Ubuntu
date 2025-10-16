<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin(['Administrador', 'Almacen']);
$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="login-logo"><img src="/assets/images/icono_takab.png" alt="logo_TAKAB" width="90" height="55"></div>
            <div>
                <div class="sidebar-title">TAKAB</div>
                <div class="sidebar-desc">Dashboard</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <?php if ($role === 'Administrador'): ?>
                <a href="usuarios.php"><i class="fa-solid fa-users-cog"></i> Gestión de Usuarios</a>
            <?php endif; ?>
            <a href="productos.php"><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</a>
            <a href="inventario_actual.php"><i class="fa-solid fa-list-check"></i> Inventario</a>
            <a href="proveedores.php"><i class="fa fa-truck"></i> Proveedores</a>
            <a href="clientes.php" class="active"><i class="fa fa-building"></i> Clientes</a>
            <a href="revisar_solicitudes.php"><i class="fa-solid fa-comment-medical"></i> Solicitudes de Material</a>
            <a href="prestamos_pendientes.php"><i class="fa-solid fa-screwdriver-wrench"></i> Préstamos Pendientes</a>
            <a href="prestamos_historial.php"><i class="fa-solid fa-history"></i> Historial de Préstamos</a>
            <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
        </nav>
    </aside>

    <div class="main-content">
        <div class="page-title">
            <i class="fa fa-building"></i> Clientes
            <a href="clientes_create.php" class="btn-principal" style="float:right;"><i class="fa fa-plus"></i> Nuevo Cliente</a>
        </div>

        <table class="takab-table">
            <thead>
                <tr>
                    <th>Nombre / Razón Social</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th style="width:160px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                        <td><?= htmlspecialchars($c['contacto']) ?></td>
                        <td><?= htmlspecialchars($c['telefono']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['direccion']) ?></td>
                        <td>
                            <a class="btn-secundario" href="clientes_edit.php?id=<?= $c['id'] ?>"><i class="fa fa-edit"></i> Editar</a>
                            <a class="btn-eliminar" href="clientes_delete.php?id=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar este cliente?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($clientes)): ?>
                    <tr><td colspan="6" style="text-align:center;">No hay clientes registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
