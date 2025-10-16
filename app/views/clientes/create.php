<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin(['Administrador', 'Almacen']);
$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Cliente | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
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
            <a href="clientes.php" class="active"><i class="fa fa-building"></i> Clientes</a>
            <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
        </nav>
    </aside>

    <div class="main-content">
        <h2><i class="fa fa-plus"></i> Registrar Cliente</h2>
        <?php if (!empty($msg)) echo "<div class='alert-success'>$msg</div>"; ?>
        <form method="post" class="form-takab">
            <label>Nombre / Razón Social:
                <input type="text" name="nombre" required>
            </label>
            <label>Contacto:
                <input type="text" name="contacto" required>
            </label>
            <label>Teléfono:
                <input type="text" name="telefono">
            </label>
            <label>Email:
                <input type="email" name="email">
            </label>
            <label>Dirección:
                <input type="text" name="direccion">
            </label>
            <button type="submit" class="btn-principal">Registrar</button>
            <a href="clientes.php" class="btn-secundario">Cancelar</a>
        </form>
    </div>
</div>
</body>
</html>
