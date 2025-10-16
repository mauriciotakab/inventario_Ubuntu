<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::requireLogin('Empleado');
?>
<h2>Solicitar Material/Herramienta General (NO disponible en inventario)</h2>
<?php if (!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<form method="post" id="solicitudForm">
    <fieldset>
        <legend>Material/Herramienta a Solicitar</legend>
        <input type="text" id="extra_nombre" placeholder="Nombre o descripción del material/herramienta">
        <input type="number" id="extra_cantidad" min="1" placeholder="Cantidad">
        <input type="text" id="extra_observacion" placeholder="Observación (opcional)">
        <button type="button" onclick="agregarMaterialExtra()">Agregar a la lista</button>
    </fieldset>

    <h4>Lista de materiales/herramientas a comprar:</h4>
    <table id="tabla_materiales" border="1" cellpadding="5">
        <tr>
            <th>Nombre/Descripción</th>
            <th>Cantidad</th>
            <th>Observación</th>
            <th>Acción</th>
        </tr>
    </table>
    <input type="hidden" name="material" id="input_materiales">

    <label>Motivo de la Solicitud:
        <input type="text" name="comentario" required>
    </label><br>
    <label>Observaciones generales (opcional):
        <input type="text" name="observacion">
    </label><br>
    <button type="submit">Enviar Solicitud</button>
</form>

<script>
let materiales = [];

function agregarMaterialExtra() {
    const nombre = document.getElementById('extra_nombre').value.trim();
    const cantidad = document.getElementById('extra_cantidad').value;
    const observacion = document.getElementById('extra_observacion').value;
    if (!nombre || !cantidad || cantidad < 1) {
        alert('Escribe nombre y cantidad válida.');
        return;
    }
    materiales.push({ producto_nombre: nombre, cantidad, observacion });
    actualizarTabla();
    document.getElementById('extra_nombre').value = '';
    document.getElementById('extra_cantidad').value = '';
    document.getElementById('extra_observacion').value = '';
}

function actualizarTabla() {
    const tabla = document.getElementById('tabla_materiales');
    tabla.innerHTML = `<tr><th>Nombre/Descripción</th><th>Cantidad</th><th>Observación</th><th>Acción</th></tr>`;
    materiales.forEach((item, idx) => {
        tabla.innerHTML += `<tr>
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

document.getElementById('solicitudForm').onsubmit = function() {
    if (materiales.length === 0) {
        alert('Agrega al menos un material/herramienta a la lista.');
        return false;
    }
    document.getElementById('input_materiales').value = JSON.stringify(materiales);
    return true;
};
</script>
