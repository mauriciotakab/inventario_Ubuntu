<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin(['Administrador', 'Almacen']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proveedor | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/proveedores.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-layout">

    <div class="main-content">
        <div class="form-card">
            <div class="form-title"><i class="fa fa-edit"></i> Editar Proveedor</div>
            <?php if (!empty($msg)) echo "<div class='alert-success'>$msg</div>"; ?>
            <form method="post" class="form-takab">
                <div class="form-row">
                    <label>Nombre:<input type="text" name="nombre" required value="<?= htmlspecialchars($proveedor['nombre']) ?>"></label>
                    <label>Contacto:<input type="text" name="contacto" required value="<?= htmlspecialchars($proveedor['contacto']) ?>"></label>
                </div>
                <div class="form-row">
                    <label>Teléfono:<input type="text" name="telefono" value="<?= htmlspecialchars($proveedor['telefono']) ?>"></label>
                    <label>Email:<input type="email" name="email" value="<?= htmlspecialchars($proveedor['email']) ?>"></label>
                </div>
                <label>Dirección:<input type="text" name="direccion" value="<?= htmlspecialchars($proveedor['direccion']) ?>"></label>
                <label>Condiciones de Pago:<input type="text" name="condiciones_pago" value="<?= htmlspecialchars($proveedor['condiciones_pago']) ?>"></label>
                <div class="form-actions">
                    <button type="submit" class="btn-principal">Guardar Cambios</button>
                    <a href="proveedores.php" class="btn-secundario">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
