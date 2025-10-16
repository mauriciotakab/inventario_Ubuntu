<?php
$role = $_SESSION['role'] ?? '';
$nombre = $_SESSION['nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/config.css">
    <link rel="stylesheet" href="/assets/css/productos.css">
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
            <?php if ($role === 'Administrador'): ?>
                <a href="usuarios.php"><i class="fa-solid fa-users-cog"></i> Gestión de Usuarios</a>
            <?php endif; ?>
            <a href="productos.php" class="active"><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</a>
            <a href="inventario_actual.php"><i class="fa-solid fa-list-check"></i> Inventario</a>
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

        <main class="dashboard-main productos-main">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="productos-header">
                <div>
                    <h1>Editar producto</h1>
                    <p class="productos-header-desc">Actualiza los datos del artículo seleccionado.</p>
                </div>
                <div class="productos-header-actions">
                    <a class="btn-secondary" href="productos.php"><i class="fa fa-arrow-left"></i> Volver al listado</a>
                    <a class="btn-secondary" href="productos_view.php?id=<?= $producto['id'] ?>"><i class="fa fa-eye"></i> Ver detalle</a>
                </div>
            </div>

            <form method="post" enctype="multipart/form-data" autocomplete="off">
                <section class="productos-form-card">
                    <h2><i class="fa fa-info-circle"></i> Información general</h2>
                    <div class="productos-form-grid">
                        <div class="productos-form-field">
                            <label for="codigo">Código interno *</label>
                            <input type="text" id="codigo" name="codigo" value="<?= htmlspecialchars($_POST['codigo'] ?? $producto['codigo']) ?>" required>
                        </div>
                        <div class="productos-form-field">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? $producto['nombre']) ?>" required>
                        </div>
                        <div class="productos-form-field">
                            <label for="tipo">Tipo *</label>
                            <select id="tipo" name="tipo" required>
                                <?php foreach ($tiposProducto as $tipo): ?>
                                    <option value="<?= $tipo ?>" <?= (($_POST['tipo'] ?? $producto['tipo']) === $tipo) ? 'selected' : '' ?>><?= $tipo ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="productos-form-field">
                            <label for="estado">Estado físico *</label>
                            <select id="estado" name="estado" required>
                                <?php foreach ($estadosProducto as $estado): ?>
                                    <option value="<?= $estado ?>" <?= (($_POST['estado'] ?? $producto['estado']) === $estado) ? 'selected' : '' ?>><?= $estado ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="productos-form-field">
                            <label for="categoria_id">Categoría *</label>
                            <select id="categoria_id" name="categoria_id" required>
                                <option value="">Selecciona una categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>" <?= (($_POST['categoria_id'] ?? $producto['categoria_id']) == $categoria['id']) ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="productos-form-field">
                            <label for="proveedor_id">Proveedor *</label>
                            <select id="proveedor_id" name="proveedor_id" required>
                                <option value="">Selecciona un proveedor</option>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <option value="<?= $proveedor['id'] ?>" <?= (($_POST['proveedor_id'] ?? $producto['proveedor_id']) == $proveedor['id']) ? 'selected' : '' ?>><?= htmlspecialchars($proveedor['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="productos-form-field">
                            <label for="tags">Tags</label>
                            <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($_POST['tags'] ?? $producto['tags']) ?>">
                        </div>
                        <div class="productos-form-field">
                            <label for="clase_categoria">Clase/Categoría interna</label>
                            <input type="text" id="clase_categoria" name="clase_categoria" value="<?= htmlspecialchars($_POST['clase_categoria'] ?? $producto['clase_categoria']) ?>">
                        </div>
                    </div>

                    <div class="productos-form-section">
                        <h3>Descripción y especificaciones</h3>
                        <div class="productos-form-grid">
                            <div class="productos-form-field" style="grid-column: span 2;">
                                <label for="descripcion">Descripción</label>
                                <textarea id="descripcion" name="descripcion"><?= htmlspecialchars($_POST['descripcion'] ?? $producto['descripcion']) ?></textarea>
                            </div>
                            <div class="productos-form-field" style="grid-column: span 2;">
                                <label for="especificaciones_tecnicas">Especificaciones técnicas</label>
                                <textarea id="especificaciones_tecnicas" name="especificaciones_tecnicas"><?= htmlspecialchars($_POST['especificaciones_tecnicas'] ?? $producto['especificaciones_tecnicas']) ?></textarea>
                            </div>
                            <div class="productos-form-field">
                                <label for="marca">Marca</label>
                                <input type="text" id="marca" name="marca" value="<?= htmlspecialchars($_POST['marca'] ?? $producto['marca']) ?>">
                            </div>
                            <div class="productos-form-field">
                                <label for="color">Color</label>
                                <input type="text" id="color" name="color" value="<?= htmlspecialchars($_POST['color'] ?? $producto['color']) ?>">
                            </div>
                            <div class="productos-form-field">
                                <label for="forma">Forma</label>
                                <input type="text" id="forma" name="forma" value="<?= htmlspecialchars($_POST['forma'] ?? $producto['forma']) ?>">
                            </div>
                            <div class="productos-form-field">
                                <label for="origen">País de origen</label>
                                <input type="text" id="origen" name="origen" value="<?= htmlspecialchars($_POST['origen'] ?? $producto['origen']) ?>">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="productos-form-card">
                    <h2><i class="fa fa-ruler-combined"></i> Dimensiones y unidades</h2>
                    <div class="productos-form-grid">
                        <div class="productos-form-field">
                            <label for="peso">Peso (kg)</label>
                            <input type="number" step="0.01" id="peso" name="peso" value="<?= htmlspecialchars($_POST['peso'] ?? $producto['peso']) ?>">
                        </div>
                        <div class="productos-form-field">
                            <label for="ancho">Ancho (cm)</label>
                            <input type="number" step="0.01" id="ancho" name="ancho" value="<?= htmlspecialchars($_POST['ancho'] ?? $producto['ancho']) ?>">
                        </div>
                        <div class="productos-form-field">
                            <label for="alto">Alto (cm)</label>
                            <input type="number" step="0.01" id="alto" name="alto" value="<?= htmlspecialchars($_POST['alto'] ?? $producto['alto']) ?>">
                        </div>
                        <div class="productos-form-field">
                            <label for="profundidad">Profundidad (cm)</label>
                            <input type="number" step="0.01" id="profundidad" name="profundidad" value="<?= htmlspecialchars($_POST['profundidad'] ?? $producto['profundidad']) ?>">
                        </div>
                        <div class="productos-form-field">
                            <label for="unidad_medida_id">Unidad de medida</label>
                            <select id="unidad_medida_id" name="unidad_medida_id">
                                <option value="">Selecciona una unidad</option>
                                <?php foreach ($unidades as $unidad): ?>
                                    <option value="<?= $unidad['id'] ?>" <?= (($_POST['unidad_medida_id'] ?? $producto['unidad_medida_id']) == $unidad['id']) ? 'selected' : '' ?>><?= htmlspecialchars($unidad['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </section>

                <section class="productos-form-card">
                    <h2><i class="fa fa-warehouse"></i> Inventario y costos</h2>
                    <div class="productos-form-grid">
                        <div class="productos-form-field">
                            <label for="almacen_id">Almacén asignado *</label>
                            <select id="almacen_id" name="almacen_id" required>
                                <option value="">Selecciona un almacén</option>
                                <?php foreach ($almacenes as $almacen): ?>
                                    <option value="<?= $almacen['id'] ?>" <?= (($_POST['almacen_id'] ?? $producto['almacen_id']) == $almacen['id']) ? 'selected' : '' ?>><?= htmlspecialchars($almacen['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="productos-form-field">
                            <label for="stock_actual">Stock actual *</label>
                            <input type="number" id="stock_actual" name="stock_actual" min="0" value="<?= htmlspecialchars($_POST['stock_actual'] ?? $producto['stock_actual']) ?>" required>
                        </div>
                        <div class="productos-form-field">
                            <label for="stock_minimo">Stock mínimo *</label>
                            <input type="number" id="stock_minimo" name="stock_minimo" min="0" value="<?= htmlspecialchars($_POST['stock_minimo'] ?? $producto['stock_minimo']) ?>" required>
                        </div>
                        <div class="productos-form-field">
                            <label for="costo_compra">Costo de compra (MXN)</label>
                            <input type="number" step="0.01" id="costo_compra" name="costo_compra" value="<?= htmlspecialchars($_POST['costo_compra'] ?? $producto['costo_compra']) ?>">
                        </div>
                        <div class="productos-form-field">
                            <label for="precio_venta">Precio de venta (MXN)</label>
                            <input type="number" step="0.01" id="precio_venta" name="precio_venta" value="<?= htmlspecialchars($_POST['precio_venta'] ?? $producto['precio_venta']) ?>">
                        </div>
                    </div>
                </section>

                <section class="productos-form-card">
                    <h2><i class="fa fa-image"></i> Imagen y archivos</h2>
                    <div class="productos-form-grid">
                        <div class="productos-form-field current-image">
                            <label>Imagen actual</label>
                            <?php if (!empty($producto['imagen_url'])): ?>
                                <img src="../public/<?= htmlspecialchars($producto['imagen_url']) ?>" alt="Imagen actual del producto">
                            <?php else: ?>
                                <span class="productos-form-note">Este producto aún no tiene imagen.</span>
                            <?php endif; ?>
                        </div>
                        <div class="productos-form-field">
                            <label for="imagen_url">Actualizar imagen</label>
                            <input type="file" id="imagen_url" name="imagen_url" accept="image/*">
                            <span class="productos-form-note">Si no seleccionas ningún archivo se conservará la imagen actual.</span>
                        </div>
                    </div>
                </section>

                <input type="hidden" name="last_requested_by_user_id" value="<?= htmlspecialchars($producto['last_requested_by_user_id']) ?>">
                <input type="hidden" name="last_request_date" value="<?= htmlspecialchars($producto['last_request_date']) ?>">

                <div class="form-actions">
                    <a class="btn-secondary" href="productos.php"><i class="fa fa-arrow-left"></i> Cancelar</a>
                    <button type="submit" class="btn-main"><i class="fa fa-save"></i> Guardar cambios</button>
                </div>
            </form>
        </main>
    </div>
</div>
</body>
</html>
