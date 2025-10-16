<?php
$role = $_SESSION['role'] ?? 'Almacen';
$nombre = $_SESSION['nombre'] ?? '';
$mostrarCostos = $role === 'Administrador';
$filters = $filters ?? [];

$fechaInicio = htmlspecialchars($filters['from'] ?? date('Y-m-01'), ENT_QUOTES, 'UTF-8');
$fechaFin = htmlspecialchars($filters['to'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8');
$movTipo = htmlspecialchars($filters['mov_tipo'] ?? '', ENT_QUOTES, 'UTF-8');

$buildQuery = function(array $overrides = []) {
    $params = array_merge($_GET, $overrides);
    foreach ($params as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
        }
    }
    return $params ? ('?' . http_build_query($params)) : '?';
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/reportes.css">
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
            <a href="revisar_solicitudes.php"><i class="fa-solid fa-comment-medical"></i> Solicitudes de Material</a>
            <a href="reportes.php" class="active"><i class="fa-solid fa-chart-line"></i> Reportes</a>
            <a href="configuracion.php"><i class="fa-solid fa-gear"></i> Configuración</a>
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

        <main class="dashboard-main reportes-main">
            <div class="reportes-header">
                <div>
                    <h1>Centro de reportes</h1>
                    <p class="reportes-desc">Analiza el estado del inventario, movimientos y préstamos. Exporta la información a CSV para respaldo o análisis externo.</p>
                </div>
                <a class="btn-secondary" href="<?= $buildQuery(['export' => 'csv', 'section' => 'movimientos']) ?>"><i class="fa fa-download"></i> Exportar movimientos</a>
            </div>

            <section class="reportes-summary-grid">
                <div class="reportes-summary-card primary">
                    <span class="label">Productos registrados</span>
                    <span class="value"><?= number_format($inventarioResumen['total']) ?></span>
                    <span class="foot">Activos: <?= number_format($inventarioResumen['activos']) ?> | Inactivos: <?= number_format($inventarioResumen['inactivos']) ?></span>
                </div>
                <div class="reportes-summary-card warning">
                    <span class="label">Stock bajo</span>
                    <span class="value"><?= number_format($inventarioResumen['stock_bajo']) ?></span>
                    <span class="foot">Sin stock: <?= number_format($inventarioResumen['sin_stock']) ?></span>
                </div>
                <div class="reportes-summary-card sky">
                    <span class="label">Herramientas prestadas</span>
                    <span class="value"><?= number_format($inventarioResumen['prestamos_pendientes']) ?></span>
                    <span class="foot">Vencidas: <?= number_format($inventarioResumen['prestamos_vencidos']) ?></span>
                </div>
                <?php if ($mostrarCostos): ?>
                    <div class="reportes-summary-card success">
                        <span class="label">Valor estimado del inventario</span>
                        <span class="value">$<?= number_format($inventarioResumen['valor_total'], 2) ?></span>
                        <span class="foot">Considera stock actual * costo compra</span>
                    </div>
                <?php endif; ?>
            </section>

            <section class="reportes-filter-card">
                <form method="get" class="reportes-filter-form">
                    <div class="filter-row">
                        <div class="filter-field">
                            <label for="from">Desde</label>
                            <input type="date" id="from" name="from" value="<?= $fechaInicio ?>">
                        </div>
                        <div class="filter-field">
                            <label for="to">Hasta</label>
                            <input type="date" id="to" name="to" value="<?= $fechaFin ?>">
                        </div>
                        <div class="filter-field">
                            <label for="mov_tipo">Tipo de movimiento</label>
                            <select id="mov_tipo" name="mov_tipo">
                                <option value="">Todos</option>
                                <option value="Entrada" <?= $movTipo === 'Entrada' ? 'selected' : '' ?>>Entrada</option>
                                <option value="Salida" <?= $movTipo === 'Salida' ? 'selected' : '' ?>>Salida</option>
                                <option value="Transferencia" <?= $movTipo === 'Transferencia' ? 'selected' : '' ?>>Transferencia</option>
                            </select>
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn-main"><i class="fa fa-filter"></i> Actualizar</button>
                            <a class="btn-ghost" href="reportes.php"><i class="fa fa-eraser"></i> Limpiar</a>
                        </div>
                    </div>
                </form>
            </section>

            <section class="reportes-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fa fa-triangle-exclamation"></i> Inventario bajo</h2>
                        <span class="section-sub">Productos con existencias por debajo del mínimo definido.</span>
                    </div>
                    <a class="btn-ghost" href="<?= $buildQuery(['export' => 'csv', 'section' => 'inventario_bajo']) ?>"><i class="fa fa-download"></i> Exportar CSV</a>
                </div>
                <?php if (empty($inventarioBajo)): ?>
                    <div class="reportes-empty">
                        <i class="fa fa-check-circle"></i>
                        <p>No hay alertas de stock bajo en este momento.</p>
                    </div>
                <?php else: ?>
                    <div class="reportes-table-wrapper">
                        <table class="reportes-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Categoría</th>
                                    <th>Almacén</th>
                                    <th>Stock actual</th>
                                    <th>Stock mínimo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inventarioBajo as $row): ?>
                                    <tr>
                                        <td><span class="mono"><?= htmlspecialchars($row['codigo']) ?></span></td>
                                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                                        <td><span class="badge badge-tipo <?= strtolower($row['tipo']) ?>"><?= htmlspecialchars($row['tipo']) ?></span></td>
                                        <td><?= htmlspecialchars($row['categoria']) ?></td>
                                        <td><?= htmlspecialchars($row['almacen']) ?></td>
                                        <td><?= number_format((float) $row['stock_actual'], 2) ?> <?= htmlspecialchars($row['unidad']) ?></td>
                                        <td><?= number_format((float) $row['stock_minimo'], 2) ?> <?= htmlspecialchars($row['unidad']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <?php if ($mostrarCostos): ?>
            <section class="reportes-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fa fa-warehouse"></i> Valor del inventario por almacén</h2>
                        <span class="section-sub">Suma del stock actual multiplicado por el costo de compra.</span>
                    </div>
                    <a class="btn-ghost" href="<?= $buildQuery(['export' => 'csv', 'section' => 'valor_almacen']) ?>"><i class="fa fa-download"></i> Exportar CSV</a>
                </div>
                <div class="reportes-table-wrapper">
                    <table class="reportes-table">
                        <thead>
                            <tr>
                                <th>Almacén</th>
                                <th>Productos</th>
                                <th>Unidades</th>
                                <th>Valor estimado (MXN)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($valorPorAlmacen as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['almacen']) ?></td>
                                    <td><?= number_format((int) $row['productos']) ?></td>
                                    <td><?= number_format((float) $row['unidades'], 2) ?></td>
                                    <td>$<?= number_format((float) $row['valor_total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <?php endif; ?>

            <section class="reportes-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fa fa-exchange-alt"></i> Movimientos de inventario</h2>
                        <span class="section-sub">Entradas, salidas y transferencias registradas entre <?= $fechaInicio ?> y <?= $fechaFin ?>.</span>
                    </div>
                    <a class="btn-ghost" href="<?= $buildQuery(['export' => 'csv', 'section' => 'movimientos']) ?>"><i class="fa fa-download"></i> Exportar CSV</a>
                </div>
                <?php if (empty($movimientos)): ?>
                    <div class="reportes-empty">
                        <i class="fa fa-info-circle"></i>
                        <p>No se registraron movimientos en el periodo seleccionado.</p>
                    </div>
                <?php else: ?>
                    <div class="reportes-table-wrapper">
                        <table class="reportes-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Almacén origen</th>
                                    <th>Almacén destino</th>
                                    <th>Usuario</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientos as $mov): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></td>
                                        <td><span class="badge badge-tipo <?= strtolower($mov['tipo']) ?>"><?= htmlspecialchars($mov['tipo']) ?></span></td>
                                        <td><span class="mono"><?= htmlspecialchars($mov['codigo']) ?></span></td>
                                        <td><?= htmlspecialchars($mov['producto']) ?></td>
                                        <td><?= number_format((float) $mov['cantidad'], 2) ?></td>
                                        <td><?= htmlspecialchars($mov['almacen_origen'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($mov['almacen_destino'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($mov['usuario'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($mov['observaciones'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="reportes-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fa fa-toolbox"></i> Herramientas prestadas</h2>
                        <span class="section-sub">Préstamos activos dentro del rango seleccionado.</span>
                    </div>
                    <a class="btn-ghost" href="<?= $buildQuery(['export' => 'csv', 'section' => 'prestamos_abiertos']) ?>"><i class="fa fa-download"></i> Exportar CSV</a>
                </div>
                <?php if (empty($prestamosAbiertos)): ?>
                    <div class="reportes-empty">
                        <i class="fa fa-info-circle"></i>
                        <p>No hay préstamos activos en el periodo.</p>
                    </div>
                <?php else: ?>
                    <div class="reportes-table-wrapper">
                        <table class="reportes-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha préstamo</th>
                                    <th>Fecha estimada</th>
                                    <th>Producto</th>
                                    <th>Empleado</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prestamosAbiertos as $prestamo): ?>
                                    <tr>
                                        <td class="mono">#<?= (int) $prestamo['id'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($prestamo['fecha_prestamo'])) ?></td>
                                        <td><?= $prestamo['fecha_estimada_devolucion'] ? date('d/m/Y', strtotime($prestamo['fecha_estimada_devolucion'])) : '-' ?></td>
                                        <td><?= htmlspecialchars($prestamo['producto']) ?> <span class="mono">(<?= htmlspecialchars($prestamo['codigo']) ?>)</span></td>
                                        <td><?= htmlspecialchars($prestamo['empleado']) ?></td>
                                        <td><?= htmlspecialchars($prestamo['observaciones'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="reportes-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fa fa-circle-exclamation"></i> Préstamos vencidos</h2>
                        <span class="section-sub">Herramientas que superaron la fecha estimada de devolución.</span>
                    </div>
                    <a class="btn-ghost" href="<?= $buildQuery(['export' => 'csv', 'section' => 'prestamos_vencidos']) ?>"><i class="fa fa-download"></i> Exportar CSV</a>
                </div>
                <?php if (empty($prestamosVencidos)): ?>
                    <div class="reportes-empty">
                        <i class="fa fa-check-circle"></i>
                        <p>No se encontraron préstamos vencidos.</p>
                    </div>
                <?php else: ?>
                    <div class="reportes-table-wrapper">
                        <table class="reportes-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha préstamo</th>
                                    <th>Fecha estimada</th>
                                    <th>Días vencidos</th>
                                    <th>Producto</th>
                                    <th>Empleado</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prestamosVencidos as $prestamo): ?>
                                    <tr>
                                        <td class="mono">#<?= (int) $prestamo['id'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($prestamo['fecha_estimada_devolucion'])) ?></td>
                                        <td><span class="badge badge-warning">+<?= (int) $prestamo['dias_vencidos'] ?> días</span></td>
                                        <td><?= htmlspecialchars($prestamo['producto']) ?> <span class="mono">(<?= htmlspecialchars($prestamo['codigo']) ?>)</span></td>
                                        <td><?= htmlspecialchars($prestamo['empleado']) ?></td>
                                        <td><?= htmlspecialchars($prestamo['observaciones'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="reportes-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fa fa-chart-bar"></i> Productos con mayor salida</h2>
                        <span class="section-sub">Top 10 artículos con más movimientos de salida en el periodo.</span>
                    </div>
                    <a class="btn-ghost" href="<?= $buildQuery(['export' => 'csv', 'section' => 'top_salidas']) ?>"><i class="fa fa-download"></i> Exportar CSV</a>
                </div>
                <?php if (empty($topSalidas)): ?>
                    <div class="reportes-empty">
                        <i class="fa fa-info-circle"></i>
                        <p>Aún no se registran salidas en el periodo.</p>
                    </div>
                <?php else: ?>
                    <div class="reportes-table-wrapper">
                        <table class="reportes-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Cantidad salida</th>
                                    <?php if ($mostrarCostos): ?><th>Costo estimado (MXN)</th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topSalidas as $row): ?>
                                    <tr>
                                        <td><span class="mono"><?= htmlspecialchars($row['codigo']) ?></span></td>
                                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                                        <td><?= number_format((float) $row['total_salidas'], 2) ?></td>
                                        <?php if ($mostrarCostos): ?><td>$<?= number_format((float) $row['costo_estimado'], 2) ?></td><?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="reportes-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fa fa-layer-group"></i> Estado de inventario</h2>
                        <span class="section-sub">Distribución de productos por estado físico.</span>
                    </div>
                    <a class="btn-ghost" href="<?= $buildQuery(['export' => 'csv', 'section' => 'estado_inventario']) ?>"><i class="fa fa-download"></i> Exportar CSV</a>
                </div>
                <div class="reportes-table-wrapper">
                    <table class="reportes-table">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Productos</th>
                                <th>Unidades</th>
                                <?php if ($mostrarCostos): ?><th>Valor estimado (MXN)</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estadoInventario as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['estado']) ?></td>
                                    <td><?= number_format((int) $row['cantidad']) ?></td>
                                    <td><?= number_format((float) $row['unidades'], 2) ?></td>
                                    <?php if ($mostrarCostos): ?><td>$<?= number_format((float) $row['valor'], 2) ?></td><?php endif; ?>
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
