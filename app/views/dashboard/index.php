<?php
$role = $datos['role'] ?? 'Empleado';
$nombre = $datos['nombre'] ?? '';
$alertas = $datos['alertas'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/dashboard_custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="login-logo"><img src="assets/images/icono_takab.png" alt="logo_TAKAB" width="90" height="55"></div>
            <div>
                <div class="sidebar-title">TAKAB</div>
                <div class="sidebar-desc">Dashboard</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
            <?php if ($role === 'Administrador'): ?>
                <a href="usuarios.php"><i class="fa-solid fa-users-cog"></i> Gestión de Usuarios</a>
                <a href="productos.php"><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</a>
                <a href="inventario_actual.php"><i class="fa-solid fa-list-check"></i> Inventario</a>
                <a href="revisar_solicitudes.php"><i class="fa-solid fa-comment-medical"></i> Solicitudes de Material</a>
                <a href="reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
                <a href="configuracion.php"><i class="fa-solid fa-gear"></i> Configuración</a>
            <?php elseif ($role === 'Almacen'): ?>
                <a href="productos.php"><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</a>
                <a href="inventario_actual.php"><i class="fa-solid fa-list-check"></i> Inventario</a>
                <a href="revisar_solicitudes.php"><i class="fa-solid fa-inbox"></i> Solicitudes de Material</a>
                <a href="reportes.php"><i class="fa-solid fa-chart-line"></i> Reportes</a>
                <a href="configuracion.php"><i class="fa-solid fa-gear"></i> Configuración</a>
            <?php elseif ($role === 'Empleado'): ?>
                <a href="solicitudes_crear.php"><i class="fa-solid fa-plus-square"></i> Solicitar Material</a>
                <a href="mis_solicitudes.php"><i class="fa-solid fa-clipboard-list"></i> Mis Solicitudes</a>
            <?php endif; ?>
            <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
        </nav>
    </aside>

    <div class="content-area">
        <header class="top-header">
            <div></div>
            <div class="top-header-user">
                <span><?= htmlspecialchars($nombre) ?> (<?= htmlspecialchars($role) ?>)</span>
                <i class="fa-solid fa-user-circle"></i>
                <a href="logout.php" class="logout-btn" title="Cerrar sesión"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            </div>
        </header>

        <main class="dashboard-main">
            <div class="dashboard-header-row">
                <div>
                    <h1><?= $role === 'Administrador' ? 'Dashboard administrativo' : ($role === 'Almacen' ? 'Dashboard almacén' : 'Dashboard empleado') ?></h1>
                    <span class="dashboard-desc">
                        <?php if ($role === 'Administrador'): ?>Resumen general del sistema de inventario TAKAB.
                        <?php elseif ($role === 'Almacen'): ?>Panel para gestión de inventario y solicitudes.
                        <?php else: ?>Resumen de tus solicitudes y actividades.
                        <?php endif; ?>
                    </span>
                </div>
                <div class="dashboard-updated">
                    <div>Último actualizado</div>
                    <div><?= htmlspecialchars($datos['last_update']) ?></div>
                </div>
            </div>

            <section class="dashboard-cards-row">
                <?php if ($role === 'Administrador'): ?>
                    <div class="dashboard-card blue">
                        <div class="card-label">Total productos</div>
                        <div class="card-value"><?= number_format($datos['totalProductos'] ?? 0) ?></div>
                        <div class="card-sub">En todos los almacenes</div>
                    </div>
                    <div class="dashboard-card red">
                        <div class="card-label">Stock bajo</div>
                        <div class="card-value"><?= number_format($datos['stockBajo'] ?? 0) ?></div>
                        <div class="card-sub">Productos requieren reposición</div>
                    </div>
                    <div class="dashboard-card yellow">
                        <div class="card-label">Solicitudes pendientes</div>
                        <div class="card-value"><?= number_format($datos['solicitudesPendientes'] ?? 0) ?></div>
                        <div class="card-sub">En espera de aprobación</div>
                    </div>
                    <div class="dashboard-card sky">
                        <div class="card-label">Herramientas prestadas</div>
                        <div class="card-value"><?= number_format($datos['herramientasPrestadas'] ?? 0) ?></div>
                        <div class="card-sub">Actualmente en campo</div>
                    </div>
                <?php elseif ($role === 'Almacen'): ?>
                    <div class="dashboard-card blue">
                        <div class="card-label">Productos registrados</div>
                        <div class="card-value"><?= number_format($datos['productosAlmacen'] ?? 0) ?></div>
                        <div class="card-sub">En este almacén</div>
                    </div>
                    <div class="dashboard-card yellow">
                        <div class="card-label">Solicitudes por gestionar</div>
                        <div class="card-value"><?= number_format($datos['solicitudesAlmacen'] ?? 0) ?></div>
                        <div class="card-sub">Pendientes de atención</div>
                    </div>
                    <div class="dashboard-card red">
                        <div class="card-label">Stock bajo</div>
                        <div class="card-value"><?= number_format($datos['stockBajo'] ?? 0) ?></div>
                        <div elass="card-sub">Productos en alerta</div>
                    </div>
                <?php else: ?>
                    <div class="dashboard-card blue">
                        <div class="card-label">Mis solicitudes</div>
                        <div class="card-value"><?= number_format($datos['solicitudesMias'] ?? 0) ?></div>
                        <div class="card-sub">Totales enviadas</div>
                    </div>
                    <div class="dashboard-card yellow">
                        <div class="card-label">Pendientes de aprobación</div>
                        <div class="card-value"><?= number_format($datos['pendientesAprobar'] ?? 0) ?></div>
                        <div class="card-sub">En espera de almacén</div>
                    </div>
                    <div class="dashboard-card sky">
                        <div class="card-label">Entregadas</div>
                        <div class="card-value"><?= number_format($datos['entregadas'] ?? 0) ?></div>
                        <div class="card-sub">Recibidas correctamente</div>
                    </div>
                <?php endif; ?>
            </section>

            <?php if ($role !== 'Empleado'): ?>
                <section class="dashboard-widget">
                    <div class="widget-title blue"><i class="fa-solid fa-wallet"></i> Valor total del inventario</div>
                    <div class="widget-value">$<?= number_format($datos['valorTotalInventario'] ?? 0, 2) ?></div>
                    <div class="widget-desc">Valor estimado considerando costo de compra.</div>
                </section>
            <?php endif; ?>

            <?php if ($role === 'Administrador'): ?>
                <section class="dashboard-widget">
                    <div class="widget-title sky"><i class="fa-solid fa-history"></i> Últimos movimientos</div>
                    <?php if (!empty($datos['ultimaActualizacion'])): ?>
                        <table class="dashboard-mini-table">
                            <thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Almacén</th></tr></thead>
                            <tbody>
                            <?php foreach ($datos['ultimaActualizacion'] as $mov): ?>
                                <tr>
                                    <td><?= date('d/m H:i', strtotime($mov['fecha'])) ?></td>
                                    <td><?= htmlspecialchars($mov['nombre'] ?? '-') ?></td>
                                    <td><span class="badge badge-tipo <?= strtolower($mov['tipo']) ?>"><?= htmlspecialchars($mov['tipo']) ?></span></td>
                                    <td><?= number_format((float) $mov['cantidad'], 2) ?></td>
                                    <td><?= htmlspecialchars($mov['almacen'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="widget-empty">Sin movimientos registrados recientemente.</p>
                    <?php endif; ?>
                </section>
            <?php elseif ($role === 'Almacen'): ?>
                <section class="dashboard-widget">
                    <div class="widget-title sky"><i class="fa-solid fa-history"></i> Movimientos recientes</div>
                    <?php if (!empty($datos['ultimosMovimientos'])): ?>
                        <table class="dashboard-mini-table">
                            <thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th></tr></thead>
                            <tbody>
                            <?php foreach ($datos['ultimosMovimientos'] as $mov): ?>
                                <tr>
                                    <td><?= date('d/m H:i', strtotime($mov['fecha'])) ?></td>
                                    <td><?= htmlspecialchars($mov['nombre'] ?? '-') ?> <span class="mono">(<?= htmlspecialchars($mov['codigo'] ?? '-') ?>)</span></td>
                                    <td><span class="badge badge-tipo <?= strtolower($mov['tipo']) ?>"><?= htmlspecialchars($mov['tipo']) ?></span></td>
                                    <td><?= number_format((float) $mov['cantidad'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="widget-empty">Sin movimientos recientes en este almacén.</p>
                    <?php endif; ?>
                </section>
            <?php else: ?>
                <section class="dashboard-widget">
                    <div class="widget-title blue"><i class="fa-solid fa-info-circle"></i> Últimas solicitudes</div>
                    <?php if (!empty($alertas)): ?>
                        <ul class="dashboard-alert-list">
                            <?php foreach ($alertas as $al): ?>
                                <li>
                                    <strong><?= htmlspecialchars($al['comentario'] ?? $al[0] ?? '-') ?></strong>
                                    <span><?= htmlspecialchars($al['fecha'] ?? '-') ?> · Estado: <?= htmlspecialchars($al['estado'] ?? '-') ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="widget-empty">Aún no tienes solicitudes recientes.</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <?php if (!empty($alertas) && $role !== 'Empleado'): ?>
                <section class="dashboard-widget">
                    <div class="widget-title red"><i class="fa-solid fa-bell"></i> Alertas del sistema</div>
                    <div class="alertas-list">
                        <?php foreach ($alertas as $alerta): ?>
                            <div class="alerta-row">
                                <span class="alerta-text"><?= htmlspecialchars($alerta[0] ?? '-') ?></span>
                                <span class="alerta-date"><?= htmlspecialchars($alerta[1] ?? '-') ?></span>
                                <span class="alerta-badge <?= htmlspecialchars($alerta[2] ?? 'alta') ?>"><?= htmlspecialchars($alerta[2] ?? 'alta') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
