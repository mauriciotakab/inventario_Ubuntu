<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin(['Administrador', 'Almacen']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrega de Solicitud | TAKAB</title>
    <link rel="stylesheet" href="/assets/css/solicitud-entregar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="entregar-main">
    <div class="entregar-title">
        <i class="fa-solid fa-box-open"></i>
        Entrega de Solicitud
    </div>
    <?php if (!empty($msg)): ?>
        <div class="entregar-msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($solicitud): ?>
        <div class="entregar-info">
            <div>
                <label>Solicitante (ID):</label> <?= htmlspecialchars($solicitud['usuario_id']) ?>
            </div>
            <div>
                <label>Motivo/Destino:</label> <?= htmlspecialchars($solicitud['comentario']) ?>
            </div>
            <div>
                <label>Observaciones del empleado:</label>
                <span class="entregar-observ"><?= htmlspecialchars($solicitud['observacion']) ?></span>
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
            <div class="entregar-section-title">
                <i class="fa fa-dolly"></i> Materiales y Herramientas a Entregar
            </div>
            <table class="entregar-table">
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
            <div class="entregar-section-title">
                <i class="fa fa-shopping-basket"></i> Materiales/Herramientas para Comprar
            </div>
            <table class="entregar-table">
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

        <form method="post" class="entregar-form" id="entregarForm">
            <label>
                Observación de entrega (opcional):
                <input type="text" name="observacion" class="entregar-input">
            </label>
            <div class="entregar-actions">
                <button type="submit" class="btn-entregar" onclick="return confirmarEntrega(event);">
                    <i class="fa fa-box"></i> Marcar como Entregada
                </button>
                <a href="revisar_solicitudes.php" class="btn-volver">
                    <i class="fa fa-arrow-left"></i> Volver a lista
                </a>
            </div>
        </form>
        <script>
        function confirmarEntrega(e) {
            if(!confirm("¿Confirmas que la solicitud ha sido entregada?\nEsta acción actualizará el estado a 'Entregada'.")) {
                e.preventDefault();
                return false;
            }
            return true;
        }
        </script>
    <?php else: ?>
        <p>Solicitud no encontrada o no aprobada.</p>
    <?php endif; ?>
</div>
</body>
</html>
