<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin(['Administrador', 'Almacen']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisión de Solicitud | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/solicitud-aprobar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="aprobar-main">
    <div class="aprobar-title">
        <i class="fa-solid fa-clipboard-check"></i>
        Revisión de Solicitud
    </div>
    <?php if (!empty($msg)): ?>
        <div class="aprobar-msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($solicitud): ?>
        <div class="aprobar-info">
            <div>
                <label>Solicitante (ID):</label> <?= htmlspecialchars($solicitud['usuario_id']) ?>
            </div>
            <div>
                <label>Motivo/Destino:</label> <?= htmlspecialchars($solicitud['comentario']) ?>
            </div>
            <div>
                <label>Observaciones del empleado:</label>
                <span class="aprobar-observ"><?= htmlspecialchars($solicitud['observacion']) ?></span>
            </div>
            <div>
                <label>Tipo de Solicitud:</label> <?= htmlspecialchars($solicitud['tipo_solicitud']) ?>
            </div>
            <div>
                <label>Estado actual:</label>
                <?php
                    $estado = strtolower($solicitud['estado']);
                    $clase = match($estado) {
                        'pendiente' => 'badge-pendiente',
                        'aprobada' => 'badge-aprobada',
                        'entregada' => 'badge-entregada',
                        'cancelada' => 'badge-cancelada',
                        'rechazada' => 'badge-rechazada',
                        default => 'badge'
                    };
                    echo "<span class='badge $clase'>" . ucfirst(htmlspecialchars($solicitud['estado'])) . "</span>";
                ?>
            </div>
        </div>
        <?php if (($solicitud['tipo_solicitud'] ?? 'servicio') !== 'General' && !empty($detalles)): ?>
            <div class="aprobar-section-title">
                <i class="fa fa-box-open"></i> Materiales y Herramientas Solicitados
            </div>
            <table class="aprobar-table">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Observación</th>
                </tr>
                <?php foreach ($detalles as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['producto']) ?></td>
                        <td><?= (float)$d['cantidad'] ?></td>
                        <td><?= htmlspecialchars($d['observacion']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <?php
        if (!empty($solicitud['extras'])):
            $extras = json_decode($solicitud['extras'], true);
            if ($extras && count($extras) > 0): ?>
            <div class="aprobar-section-title">
                <i class="fa fa-shopping-basket"></i> Materiales/Herramientas para Comprar
            </div>
            <table class="aprobar-table">
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Observación</th>
                </tr>
                <?php foreach ($extras as $ex): ?>
                    <tr>
                        <td><?= htmlspecialchars($ex['descripcion'] ?? $ex['producto_nombre']) ?></td>
                        <td><?= (float)$ex['cantidad'] ?></td>
                        <td><?= htmlspecialchars($ex['observacion']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; endif; ?>

        <form method="post" class="aprobar-form" id="aprobarForm">
            <label>
                Observación (opcional, visible para el empleado):
                <input type="text" name="observacion" class="aprobar-input">
            </label>
            <div class="aprobar-actions">
                <button type="submit" name="accion" value="aprobar" class="btn-aprobar" onclick="return confirmarAprobar(event);">
                    <i class="fa fa-check"></i> Aprobar
                </button>
                <button type="submit" name="accion" value="rechazar" class="btn-rechazar" onclick="return confirmarRechazar(event);">
                    <i class="fa fa-times"></i> Rechazar
                </button>
                <a href="revisar_solicitudes.php" class="btn-volver">
                    <i class="fa fa-arrow-left"></i> Volver a lista
                </a>
            </div>
        </form>
        <script>
        function confirmarAprobar(e) {
            if(!confirm("¿Estás seguro de aprobar esta solicitud?")) {
                e.preventDefault();
                return false;
            }
            return true;
        }
        function confirmarRechazar(e) {
            if(!confirm("¿Estás seguro de RECHAZAR esta solicitud?\nEsta acción no se puede deshacer.")) {
                e.preventDefault();
                return false;
            }
            return true;
        }
        </script>
    <?php else: ?>
        <p>Solicitud no encontrada.</p>
    <?php endif; ?>
</div>
</body>
</html>
