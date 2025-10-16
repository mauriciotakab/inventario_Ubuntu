<?php require __DIR__ . '/../layouts/header.php'; ?>
<h1>Solicitudes</h1>
<table>
<thead><tr><th>ID</th><th>Usuario</th><th>Tipo</th><th>Estado</th></tr></thead>
<tbody>
<?php foreach($solicitudes as $s): ?>
<tr>
  <td><?= $s['id'] ?></td>
  <td><?= htmlspecialchars($s['usuario_nombre']) ?></td>
  <td><?= htmlspecialchars($s['tipo_solicitud']) ?></td>
  <td><?= htmlspecialchars($s['estado']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php require __DIR__ . '/../layouts/footer.php'; ?>