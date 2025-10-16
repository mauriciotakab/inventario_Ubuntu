<!-- app/views/usuarios/list.php -->
<h2>Lista de Usuarios</h2>
<a href="/usuario/crear">Crear nuevo usuario</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Nombre</th>
        <th>Rol</th>
        <th>Activo</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($usuarios as $u): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>
        <td><?= $u['activo'] ? 'Sí' : 'No' ?></td>
        <td>
            <a href="/usuario/ver/<?= $u['id'] ?>">Ver</a> | 
            <a href="/usuario/editar/<?= $u['id'] ?>">Editar</a> | 
            <a href="/usuario/eliminar/<?= $u['id'] ?>" onclick="return confirm('¿Seguro de eliminar?');">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
