<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin(['Administrador', 'Almacen']);

$role = $_SESSION['role'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/proveedores.css">
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
    <div class="main-content">
        <div class="prov-header">
            <div>
                <div class="prov-title"><i class="fa-solid fa-truck"></i> Proveedores</div>
                <div class="prov-desc">Listado y gestión de proveedores</div>
            </div>
            <a href="proveedores_create.php" class="btn-principal"><i class="fa fa-plus"></i> Nuevo Proveedor</a>
        </div>
        <div class="prov-table-card">
            <table class="prov-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Condiciones de Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $prov): ?>
                        <tr>
                            <td><?= htmlspecialchars($prov['nombre']) ?></td>
                            <td><?= htmlspecialchars($prov['contacto']) ?></td>
                            <td><?= htmlspecialchars($prov['telefono']) ?></td>
                            <td><?= htmlspecialchars($prov['email']) ?></td>
                            <td><?= htmlspecialchars($prov['direccion']) ?></td>
                            <td><?= htmlspecialchars($prov['condiciones_pago']) ?></td>
                            <td>
                                <a href="proveedores_edit.php?id=<?= $prov['id'] ?>" class="btn-secundario" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="proveedores_delete.php?id=<?= $prov['id'] ?>" 
                                   onclick="return confirm('¿Seguro que deseas eliminar este proveedor?')" 
                                   class="btn-eliminar" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($proveedores)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; color:#9ab;">Sin proveedores registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
