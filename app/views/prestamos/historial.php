<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin(['Administrador', 'Almacen']);
require_once __DIR__ . '/../../models/Prestamo.php';
$role = $_SESSION['role'] ?? '';
$nombre = $_SESSION['nombre'] ?? '';

// Parámetros de búsqueda y paginación
$busqueda = trim($_GET['q'] ?? '');
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$porPagina = 9;

$prestamos = Prestamo::historialPaginado($busqueda, $page, $porPagina);
$total = Prestamo::totalHistorial($busqueda);
$totalPages = ceil($total / $porPagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Préstamos de Herramientas | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/prestamo-historial.css">
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
                <a href="revisar_solicitudes.php"><i class="fa-solid fa-comment-medical"></i> Solicitudes de Material</a>
                <a href='prestamos_pendientes.php'>- Préstamos Pendientes</a>
                <a href='prestamos_historial.php' class="active">- Historial de Préstamos</a>
                <a href="reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
                <a href="configuracion.php"><i class="fa-solid fa-gear"></i> Configuración</a>
            <?php elseif ($role === 'Almacen'): ?>
                <a href="productos.php"><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</a>
                <a href="inventario_actual.php"><i class="fa-solid fa-list-check"></i> Inventario</a>
                <a href="revisar_solicitudes.php"><i class="fa-solid fa-inbox"></i> Solicitudes de Material</a>
                <a href='prestamos_pendientes.php'>- Préstamos Pendientes</a>
                <a href='prestamos_historial.php' class="active">- Historial de Préstamos</a>
                <a href="reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
                <a href="configuracion.php"><i class="fa-solid fa-gear"></i> Configuración</a>
            <?php elseif ($role === 'Empleado'): ?>
                <a href="solicitudes_crear.php"><i class="fa-solid fa-plus-square"></i> Solicitar Material</a>
                <a href="mis_solicitudes.php"><i class="fa-solid fa-clipboard-list"></i> Mis Solicitudes</a>
            <?php endif; ?>
            <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
        </nav>


    </aside>
    <div class="historial-main">
        <div class="historial-title">
            <i class="fa-solid fa-history"></i>
            Historial de Préstamos de Herramientas
        </div>
<div class="search-bar">
    <form method="get" action="">
        <input type="text" name="q" value="<?= htmlspecialchars($busqueda) ?>"
            placeholder="Buscar por código o trabajador..." class="takab-search-input">
        <button type="submit" class="takab-search-btn">
            <i class="fa fa-search"></i>
            <span class="hidden-xs">Buscar</span>
        </button>
    </form>
</div>

        <table class="takab-table">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Código</th>
                    <th>Herramienta</th>
                    <th>Fecha Préstamo</th>
                    <th>Fecha Devolución</th>
                    <th>Estado del Préstamo</th>
                    <th>Estado al devolver</th>
                    <th>Observaciones al devolver</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['empleado']) ?></td>
                        <td><?= htmlspecialchars($p['codigo_producto']) ?></td>
                        <td><?= htmlspecialchars($p['producto']) ?></td>
                        <td><?= htmlspecialchars($p['fecha_prestamo']) ?></td>
                        <td><?= htmlspecialchars($p['fecha_devolucion']) ?></td>
                        <td>
                            <?php
                            $estado = strtolower($p['estado']);
                            $clase = match($estado) {
                                'pendiente' => 'badge-pendiente',
                                'devuelto', 'devuelta' => 'badge-devuelto',
                                'vencido' => 'badge-vencido',
                                default => 'badge'
                            };
                            echo "<span class='badge $clase'>" . ucfirst(htmlspecialchars($p['estado'])) . "</span>";
                            ?>
                        </td>
                        <td>
                            <?php
                            $ed = strtolower($p['estado_devolucion']);
                            $claseEd = match($ed) {
                                'bueno' => 'badge-bueno',
                                'dañado' => 'badge-danado',
                                'perdido' => 'badge-perdido',
                                default => 'badge'
                            };
                            echo "<span class='badge $claseEd'>" . ucfirst(htmlspecialchars($p['estado_devolucion'])) . "</span>";
                            ?>
                        </td>
                        <td><?= htmlspecialchars($p['observaciones']) ?></td>
                        <td><?= htmlspecialchars($p['observaciones_devolucion'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?q=<?= urlencode($busqueda) ?>&page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
