<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin('Empleado');
?>
<h2>Solicitar Material y Herramienta para Proyecto/Servicio</h2>
<?php if (!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<form method="post" id="solicitudForm">
    <fieldset>
        <legend>Consumibles</legend>
        <input type="search" id="busqueda_consumible" placeholder="Buscar consumible..." onkeyup="filtrarOpciones('consumible')">
        <select id="select_consumible">
            <option value="">Seleccionar...</option>
            <?php foreach ($productos_consumibles as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" id="cantidad_consumible" placeholder="Cantidad" min="1">
        <input type="text" id="obs_consumible" placeholder="Observación (opcional)">
        <button type="button" onclick="agregarMaterial('Consumible')">Agregar</button>
    </fieldset>

    <fieldset>
        <legend>Herramientas</legend>
        <input type="search" id="busqueda_herramienta" placeholder="Buscar herramienta..." onkeyup="filtrarOpciones('herramienta')">
        <select id="select_herramienta">
            <option value="">Seleccionar...</option>
            <?php foreach ($productos_herramientas as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" id="cantidad_herramienta" placeholder="Cantidad" min="1">
        <input type="text" id="obs_herramienta" placeholder="Observación (opcional)">
        <button type="button" onclick="agregarMaterial('Herramienta')">Agregar</button>
    </fieldset>

    <fieldset>
        <legend>Material Extra para Comprar (opcional)</legend>
        <input type="text" id="extra_nombre" placeholder="Nombre o descripción del material extra">
        <input type="number" id="extra_cantidad" min="1" placeholder="Cantidad">
        <input type="text" id="extra_observacion" placeholder="Observación (opcional)">
        <button type="button" onclick="agregarMaterialExtra()">Agregar Extra</button>
    </fieldset>

    <h4>Materiales/Herramientas Solicitadas:</h4>
    <table id="tabla_materiales" border="1" cellpadding="5">
        <tr>
            <th>Tipo</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Observación</th>
            <th>Acción</th>
        </tr>
    </table>
    <input type="hidden" name="material" id="input_materiales">

    <label>Proyecto/Destino (obligatorio):
        <input type="text" name="comentario" required>
    </label><br>
    <label>Observaciones generales (opcional):
        <input type="text" name="observacion">
    </label><br>
    <button type="submit">Enviar Solicitud</button>
</form>

<script>
let materiales = [];

function agregarMaterial(tipo) {
    let select, cantidad, observacion;
    if (tipo === 'Consumible') {
        select = document.getElementById('select_consumible');
        cantidad = document.getElementById('cantidad_consumible').value;
        observacion = document.getElementById('obs_consumible').value;
    } else {
        select = document.getElementById('select_herramienta');
        cantidad = document.getElementById('cantidad_herramienta').value;
        observacion = document.getElementById('obs_herramienta').value;
    }
    const producto_id = select.value;
    const producto_nombre = select.options[select.selectedIndex].text;
    if (!producto_id || !cantidad || cantidad < 1) {
        alert('Selecciona un producto y cantidad válida.');
        return;
    }
    materiales.push({ tipo, producto_id, producto_nombre, cantidad, observacion });
    actualizarTabla();
}


function agregarMaterialExtra() {
    const nombre = document.getElementById('extra_nombre').value.trim();
    const cantidad = document.getElementById('extra_cantidad').value;
    const observacion = document.getElementById('extra_observacion').value;
    if (!nombre || !cantidad || cantidad < 1) {
        alert('Escribe nombre y cantidad válida para el material extra.');
        return;
    }
    materiales.push({ tipo: 'Extra', producto_id: null, producto_nombre: nombre, cantidad, observacion });
    actualizarTabla();
    document.getElementById('extra_nombre').value = '';
    document.getElementById('extra_cantidad').value = '';
    document.getElementById('extra_observacion').value = '';
}

function actualizarTabla() {
    const tabla = document.getElementById('tabla_materiales');
    tabla.innerHTML = `<tr><th>Tipo</th><th>Producto</th><th>Cantidad</th><th>Observación</th><th>Acción</th></tr>`;
    materiales.forEach((item, idx) => {
        tabla.innerHTML += `<tr>
            <td>${item.tipo}</td>
            <td>${item.producto_nombre}</td>
            <td>${item.cantidad}</td>
            <td>${item.observacion || ''}</td>
            <td><button type="button" onclick="eliminarMaterial(${idx})">Eliminar</button></td>
        </tr>`;
    });
    document.getElementById('input_materiales').value = JSON.stringify(materiales);
}

function eliminarMaterial(idx) {
    materiales.splice(idx, 1);
    actualizarTabla();
}

function filtrarOpciones(tipo) {
    let input, select;
    if (tipo === 'consumible') {
        input = document.getElementById('busqueda_consumible').value.toLowerCase();
        select = document.getElementById('select_consumible');
    } else {
        input = document.getElementById('busqueda_herramienta').value.toLowerCase();
        select = document.getElementById('select_herramienta');
    }
    for (let i = 0; i < select.options.length; i++) {
        const opt = select.options[i];
        if (i === 0) continue;
        opt.style.display = opt.text.toLowerCase().includes(input) ? '' : 'none';
    }
}

document.getElementById('solicitudForm').onsubmit = function() {
    if (materiales.length === 0) {
        alert('Agrega al menos un material o herramienta a la lista.');
        return false;
    }
    document.getElementById('input_materiales').value = JSON.stringify(materiales);
    return true;
};
</script>
