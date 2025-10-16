<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin('Empleado');
$role = $_SESSION['role'];
$nombre = $_SESSION['nombre'];
require_once __DIR__ . '/../../models/SolicitudMaterial.php';

$estadoFiltro = $_GET['estado'] ?? '';

// Agrega productos a cada solicitud (siempre tendrás 'items')
foreach ($solicitudes as &$s) {
    $s['items'] = SolicitudMaterial::detalles($s['id']);
}
unset($s);

// Filtrar por estado si aplica
if ($estadoFiltro) {
    $solicitudes = array_filter($solicitudes, function($s) use ($estadoFiltro) {
        return strtolower($s['estado']) === strtolower($estadoFiltro);
    });
}




require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin('Empleado');
$role = $_SESSION['role'];
$nombre = $_SESSION['nombre'];
$estadoFiltro = $_GET['estado'] ?? '';
$tabs = [
    'Todas' => '',
    'Pendientes' => 'pendiente',
    'Aprobadas' => 'aprobada',
    'Entregadas' => 'entregada',
    'Rechazadas' => 'rechazada',
];


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Solicitudes de Material/Herramienta | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/config.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #f6f7fa; }
        .sol-main { max-width: 1200px; margin: 0 auto; padding: 38px 18px 34px 18px; }
        .sol-header-title { font-size: 2.1rem; font-weight: 800; margin-bottom: 5px; color: #232a4d; }
        .sol-header-desc { color: #7b89b0; font-size: 1.08rem; margin-bottom: 28px;}
        .sol-tabs { display: flex; gap: 10px; margin-bottom: 25px; }
        .sol-tab {
            background: #f3f6fb; border-radius: 8px; font-weight: 600; color: #37498e;
            padding: 9px 28px; border: none; font-size: 1.07rem; cursor: pointer;
            transition: background 0.15s, color 0.15s; text-decoration: none;
        }
        .sol-tab.active, .sol-tab:hover { background: #e8edfa; color: #1741a6; }
        .sol-nueva-btn {
            float: right; margin-top: -12px; margin-bottom: 23px; background: #1741a6; color: #fff;
            font-size: 1.08rem; border-radius: 8px; border: none; padding: 10px 25px; font-weight: 700;
            box-shadow: 0 2px 12px 0 rgba(23,44,87,0.08); transition: background 0.18s;
            text-decoration: none; display: inline-flex; align-items: center; gap: 10px;
        }
        .sol-nueva-btn:hover { background: #2563eb; color: #fff;}
        .sol-card {
            background: #fff; border-radius: 13px; box-shadow: 0 2px 11px 0 rgba(23,44,87,0.07);
            border: 1.2px solid #eef2f8; margin-bottom: 22px; padding: 28px 36px 22px 36px; position: relative;
        }
        .sol-card-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            gap: 18px; margin-bottom: 10px;
        }
        .sol-card-title { font-size: 1.22rem; font-weight: 700; color: #162049; margin-bottom: 2px; }
        .sol-card-meta {
            font-size: 1rem; color: #6b7db6; display: flex; align-items: center; gap: 19px; margin-bottom: 2px;
        }
        .sol-badges { display: flex; gap: 7px; margin-top: 5px; }
        .sol-badge { display: inline-block; font-size: .98rem; font-weight: 600; padding: 3px 13px 4px 13px;
            border-radius: 11px; background: #f3f6fb; color: #5363a3; text-transform: lowercase;
        }
        .badge-pendiente { background: #fff7e1; color: #b78e17; }
        .badge-aprobada, .badge-aprobado { background: #e6f1ff; color: #2563eb; }
        .badge-entregada, .badge-entregado { background: #e9faef; color: #11a158;}
        .badge-cancelada { background: #f3f3f3; color: #888; }
        .badge-rechazada, .badge-rechazado { background: #ffeaea; color: #e12d39;}
        .sol-card-section { margin-top: 13px; }
        .sol-card-section h4 {
            font-size: 1.03rem; font-weight: 700; margin-bottom: 8px; color: #23315a;
            display: flex; align-items: center; gap: 6px;
        }
        .sol-items-list {
            background: #f7f8fa; border-radius: 8px; padding: 13px 24px; margin-bottom: 10px; margin-top: 2px;
        }
        .sol-items-list-row {
            display: flex; justify-content: space-between;
            padding: 3px 0; color: #27345a; font-size: 1.04rem;
        }
        .sol-detalle-btn {
            display: inline-block; margin-top: 18px; padding: 8px 20px;
            background: #2563eb; color: #fff; font-weight: 600; border-radius: 8px;
            text-decoration: none; font-size: 1.01rem; transition: background 0.13s;
        }
        .sol-detalle-btn:hover { background: #1741a6; color: #fff;}
        @media (max-width:900px){
            .sol-card { padding: 15px 4vw; }
            .sol-main { padding: 19px 5vw 24px 5vw;}
        }
    </style>
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

            <?php if ($role === 'Empleado'): ?>
                <a href="solicitudes_crear.php"><i class="fa-solid fa-plus-square"></i> Solicitar Material para Servicio</a>
                <a href='solicitar_material_general.php'><i class="fa-solid fa-comment-medical"></i> Solicitar Material en General</a>
                <a href="mis_solicitudes.php" class="active"><i class="fa-solid fa-clipboard-list"></i> Mis Solicitudes</a>


                <?php endif; ?>
            <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
        </nav>
    </aside>
    <div class="content-area">
        <header class="top-header">
            <div></div>
            <div class="top-header-user">
                <span><?= htmlspecialchars($_SESSION['nombre'] ?? 'TAKAB'); ?></span>
                <i class="fa-solid fa-user-circle"></i>
                <a href="../logout.php" class="logout-btn" title="Cerrar sesión">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </header>
        <main class="dashboard-main sol-main">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:18px;">
                <div>
                    <div class="sol-header-title">Mis Solicitudes de Material/Herramienta</div>
                    <div class="sol-header-desc">Historial de solicitudes que has realizado</div>
                </div>
                <a href="solicitudes_crear.php" class="sol-nueva-btn">
                    <i class="fa fa-plus"></i> Nueva Solicitud
                </a>
            </div>
            <div class="sol-tabs">
                <?php foreach ($tabs as $tabNombre => $tabEstado): 
                    $active = ($estadoFiltro === $tabEstado) ? 'active' : '';
                    $url = $tabEstado ? "?estado=$tabEstado" : "mis_solicitudes.php";
                ?>
                    <a href="<?= $url ?>" class="sol-tab <?= $active ?>"><?= $tabNombre ?></a>
                <?php endforeach; ?>
            </div>
            <?php if (empty($solicitudes)): ?>
                <div style="margin-top:40px;text-align:center;color:#9ab;">No hay solicitudes.</div>
            <?php endif; ?>
            <?php foreach ($solicitudes as $s): ?>
                <div class="sol-card">
                    <div class="sol-card-header">
                        <div>
                            <div class="sol-card-title"><?= htmlspecialchars($s['comentario'] ?? '(Sin comentario)') ?></div>
                            <div class="sol-card-meta">
                                <span><i class="fa fa-calendar"></i> <?= htmlspecialchars($s['fecha_solicitud'] ?? '') ?></span>
                                <span><i class="fa fa-layer-group"></i> <?= htmlspecialchars($s['tipo_solicitud'] ?? '') ?></span>
                                <span><i class="fa fa-cubes"></i> <?= (int)$s['total_productos'] ?> productos</span>
                                <span><i class="fa fa-cubes"></i> <?= (int)$s['total_productos'] ?> productos</span>

                            </div>
                        </div>
                        <div class="sol-badges">
                            <?php
                                $estado = strtolower($s['estado']);
                                $clase = match($estado) {
                                    'pendiente' => 'badge-pendiente',
                                    'aprobada', 'aprobado' => 'badge-aprobada',
                                    'entregada', 'entregado' => 'badge-entregada',
                                    'cancelada' => 'badge-cancelada',
                                    'rechazada', 'rechazado' => 'badge-rechazada',
                                    default => 'sol-badge'
                                };
                                echo "<span class='sol-badge $clase'>" . ucfirst(htmlspecialchars($s['estado'])) . "</span>";
                            ?>
                        </div>
                    </div>
                    <?php
                        $items = (!empty($s['items']) && is_array($s['items'])) ? $s['items'] : [];
                        if (!empty($items)):
                    ?>
                    <div class="sol-card-section">
                        <h4><i class="fa fa-box-open"></i> Herramientas/Material Solicitado</h4>
                        <div class="sol-items-list">
                            <?php foreach ($items as $item): ?>
                                <div class="sol-items-list-row">
                                    <span><?= htmlspecialchars($item['producto'] ?? $item['nombre'] ?? '') ?></span>
                                    <span style="color:#7281a8;">
                                        <?= htmlspecialchars($item['cantidad'] ?? '') ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <a href="solicitud_detalle.php?id=<?= $s['id'] ?>" class="sol-detalle-btn">
                        <i class="fa fa-eye"></i> Ver Detalle
                    </a>
                </div>
            <?php endforeach; ?>
        </main>
    </div>
</div>
</body>
</html>
<?php

// Controlador antes de cargar la vista (historial.php)
$estadoFiltro = $_GET['estado'] ?? ''; // '' para todas
$solicitudes = SolicitudMaterial::historialPorUsuario($usuario_id);
if ($estadoFiltro) {
    $solicitudes = array_filter($solicitudes, function($s) use ($estadoFiltro) {
        return strtolower($s['estado']) === strtolower($estadoFiltro);
    });
}
// A cada solicitud, agrega los items/herramientas:
foreach ($solicitudes as &$sol) {
    $sol['items'] = SolicitudMaterial::detalles($sol['id']);
}
unset($sol);

?>