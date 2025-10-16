<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin(['Administrador', 'Almacen']);

$role = $_SESSION['role'] ?? 'Administrador';
$nombre = $_SESSION['nombre'] ?? '';
$selectedProducto = $_POST['producto_id'] ?? '';
$selectedAlmacen = $_POST['almacen_id'] ?? '';
$cantidadSolicitada = $_POST['cantidad'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Salida de Inventario | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/productos.css">
    <link rel="stylesheet" href="/assets/css/inventario_form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="login-logo"><img src="/assets/images/icono_takab.png" alt="logo_TAKAB" width="90" height="55"></div>
            <div>
                <div class="sidebar-title">TAKAB</div>
                <div class="sidebar-desc">Panel de control</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="productos.php"><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</a>
            <a href="inventario_actual.php" class="active"><i class="fa-solid fa-list-check"></i> Inventario</a>
            <a href="revisar_solicitudes.php"><i class="fa-solid fa-comment-medical"></i> Solicitudes de Material</a>
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

        <main class="dashboard-main inventario-form-main">
            <div class="inventario-form-header">
                <div>
                    <h1><i class="fa fa-arrow-up"></i> Registrar salida de inventario</h1>
                    <p class="form-desc">Descarga material del almacén y actualiza el stock disponible para los proyectos.</p>
                </div>
                <a class="btn-secondary" href="inventario_actual.php"><i class="fa fa-arrow-left"></i> Volver al inventario</a>
            </div>

            <?php if (!empty($msg)): ?>
                <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <div class="inventario-form-grid">
                <section class="inventario-form-card">
                    <h2><i class="fa fa-clipboard"></i> Detalles de la salida</h2>
                    <form method="post" autocomplete="off" class="inventario-entry-form">
                        <div class="form-field">
                            <label for="producto_id">Producto *</label>
                            <select id="producto_id" name="producto_id" required>
                                <option value="">Selecciona un producto...</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= $producto['id'] ?>"
                                            data-stock="<?= (float) ($producto['stock_actual'] ?? 0) ?>"
                                            data-min="<?= (float) ($producto['stock_minimo'] ?? 0) ?>"
                                            data-unidad="<?= htmlspecialchars($producto['unidad_abreviacion'] ?? '') ?>"
                                            data-almacen="<?= (int) ($producto['almacen_id'] ?? 0) ?>"
                                            <?= $selectedProducto == $producto['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($producto['nombre']) ?> (<?= htmlspecialchars($producto['codigo']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="almacen_id">Almacén origen *</label>
                            <select id="almacen_id" name="almacen_id" required>
                                <option value="">Selecciona un almacén...</option>
                                <?php foreach ($almacenes as $almacen): ?>
                                    <option value="<?= $almacen['id'] ?>" <?= $selectedAlmacen == $almacen['id'] ? 'selected' : '' ?>><?= htmlspecialchars($almacen['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="cantidad">Cantidad *</label>
                            <input type="number" id="cantidad" name="cantidad" min="0" step="0.01" placeholder="Ej. 10" value="<?= htmlspecialchars($cantidadSolicitada) ?>" required>
                        </div>

                        <div class="form-field">
                            <label for="observaciones">Observaciones</label>
                            <textarea id="observaciones" name="observaciones" placeholder="Motivo de la salida, proyecto, folio..." rows="3"><?= htmlspecialchars($observaciones) ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-main"><i class="fa fa-upload"></i> Registrar salida</button>
                        </div>
                    </form>
                </section>

                <section class="inventario-form-card form-summary">
                    <h2><i class="fa fa-circle-info"></i> Resumen del producto</h2>
                    <div class="summary-placeholder" id="summary-placeholder">
                        <i class="fa fa-box"></i>
                        <p>Selecciona un producto para consultar su stock disponible.</p>
                    </div>
                    <div class="summary-content" id="summary-content" hidden>
                        <div class="summary-item">
                            <span class="label">Producto</span>
                            <span class="value" id="summary-nombre">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Stock actual</span>
                            <span class="value" id="summary-stock">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Stock mínimo</span>
                            <span class="value" id="summary-min">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Unidad</span>
                            <span class="value" id="summary-unidad">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Almacén asignado</span>
                            <span class="value" id="summary-almacen">-</span>
                        </div>
                    </div>
                </section>
            </div>

            <section class="inventario-form-card inventario-recents">
                <div class="recents-header">
                    <h2><i class="fa fa-clock"></i> Últimas salidas registradas</h2>
                    <span class="recents-sub">Ayuda a verificar duplicidades o confirmar capturas recientes</span>
                </div>
                <?php if (empty($movimientosRecientes)): ?>
                    <div class="inventario-empty">
                        <i class="fa fa-inbox"></i>
                        <p>Aún no se registran salidas de inventario.</p>
                    </div>
                <?php else: ?>
                    <div class="recents-table-wrapper">
                        <table class="recents-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Almacén origen</th>
                                    <th>Registró</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientosRecientes as $mov): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></td>
                                        <td><?= htmlspecialchars($mov['producto'] ?? '-') ?> <span class="mono">(<?= htmlspecialchars($mov['codigo_producto'] ?? '-') ?>)</span></td>
                                        <td><?= rtrim(rtrim(number_format((float) ($mov['cantidad'] ?? 0), 2), '0'), '.') ?></td>
                                        <td><?= htmlspecialchars($mov['almacen_origen'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($mov['usuario'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($mov['observaciones'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</div>

<script>
const productosSelect = document.getElementById('producto_id');
const almacenSelect = document.getElementById('almacen_id');
const summaryPlaceholder = document.getElementById('summary-placeholder');
const summaryContent = document.getElementById('summary-content');
const summaryNombre = document.getElementById('summary-nombre');
const summaryStock = document.getElementById('summary-stock');
const summaryMin = document.getElementById('summary-min');
const summaryUnidad = document.getElementById('summary-unidad');
const summaryAlmacen = document.getElementById('summary-almacen');
const almacenesMap = new Map([
    <?php foreach ($almacenes as $almacen): ?>
    [<?= (int) $almacen['id'] ?>, "<?= addslashes($almacen['nombre']) ?>"],
    <?php endforeach; ?>
]);

function actualizarResumen() {
    const option = productosSelect.options[productosSelect.selectedIndex];
    if (!option || !option.value) {
        summaryPlaceholder.hidden = false;
        summaryContent.hidden = true;
        return;
    }

    const stock = option.dataset.stock ? parseFloat(option.dataset.stock) : 0;
    const min = option.dataset.min ? parseFloat(option.dataset.min) : 0;
    const unidad = option.dataset.unidad || '';
    const almacenId = option.dataset.almacen ? parseInt(option.dataset.almacen, 10) : 0;

    summaryNombre.textContent = option.textContent.trim();
    summaryStock.textContent = `${Number.isNaN(stock) ? '-' : stock.toLocaleString(undefined, { maximumFractionDigits: 2 })} ${unidad}`;
    summaryMin.textContent = Number.isNaN(min) ? '-' : min.toLocaleString(undefined, { maximumFractionDigits: 2 });
    summaryUnidad.textContent = unidad || '-';
    summaryAlmacen.textContent = almacenesMap.get(almacenId) || 'Sin asignar';

    summaryPlaceholder.hidden = true;
    summaryContent.hidden = false;
}

productosSelect.addEventListener('change', actualizarResumen);

if (productosSelect.value) {
    actualizarResumen();
}
</script>
</body>
</html>
