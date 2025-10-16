<?php
require_once __DIR__ . '/../models/SolicitudMaterial.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../models/Prestamo.php';


class SolicitudMaterialController
{
    // Formulario de solicitud para inventario (proyecto/servicio)
    public function crear()
    {
        $this->crearSolicitud(false);
    }

    // Formulario de solicitud general (material no en inventario)
    public function crearGeneral()
    {
        $this->crearSolicitud(true);
    }

    // Lógica compartida
private function crearSolicitud($isGeneral = false)
{
    Session::requireLogin('Empleado');
    $productos_consumibles = $isGeneral ? [] : Producto::all(['tipo' => 'Consumible']);
    $productos_herramientas = $isGeneral ? [] : Producto::all(['tipo' => 'Herramienta']);
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $comentario = trim($_POST['comentario']);
        $observacion = trim($_POST['observacion'] ?? '');
        $tipo = $isGeneral ? 'General' : 'Mixta';
        $tipo_solicitud = $isGeneral ? 'General' : 'Servicio';  // <-- Aquí lo defines
        $detalles = [];
        $extras = [];
        if (empty($comentario)) {
            $msg = $isGeneral
                ? "Debes especificar el motivo de la solicitud."
                : "Debes especificar para qué proyecto/destino se va a utilizar el material.";
        } elseif (empty($_POST['material'])) {
            $msg = "Debes agregar al menos un material o herramienta a la solicitud.";
        } else {
            $materiales = json_decode($_POST['material'], true);
            foreach ($materiales as $item) {
                if ($isGeneral) {
                    if ($item['producto_nombre'] && $item['cantidad'] > 0) {
                        $extras[] = [
                            'descripcion' => $item['producto_nombre'],
                            'cantidad' => $item['cantidad'],
                            'observacion' => $item['observacion'] ?? ''
                        ];
                    }
                } else {
                    if ($item['tipo'] === 'Extra') {
                        $extras[] = [
                            'descripcion' => $item['producto_nombre'],
                            'cantidad' => $item['cantidad'],
                            'observacion' => $item['observacion'] ?? ''
                        ];
                    } else if ($item['producto_id'] && $item['cantidad'] > 0) {
                        $detalles[] = [
                            'producto_id' => $item['producto_id'],
                            'cantidad' => $item['cantidad'],
                            'observacion' => $item['observacion'] ?? ''
                        ];
                        if ($tipo === 'Mixta') {
                            $tipo = $item['tipo'];
                        } elseif ($tipo !== $item['tipo']) {
                            $tipo = 'Mixta';
                        }
                    }
                }
            }
            if (count($detalles) == 0 && count($extras) == 0) {
                $msg = "Debes agregar al menos un material, herramienta o material extra válido.";
            } else {
                $data = [
                    'usuario_id' => $_SESSION['user_id'],
                    'tipo' => $tipo,
                    'tipo_solicitud' => $tipo_solicitud, // <-- IMPORTANTE
                    'comentario' => $comentario,
                    'observacion' => $observacion,
                    'extras' => $extras
                ];
                SolicitudMaterial::create($data, $detalles);
                $msg = "Solicitud enviada correctamente.";
            }
        }
    }
    if ($isGeneral) {
        include __DIR__ . '/../views/solicitudes/crear_general.php';
    } else {
        include __DIR__ . '/../views/solicitudes/crear.php';
    }
}


    public function historial()
    {
        Session::requireLogin('Empleado');
        $solicitudes = SolicitudMaterial::historialPorUsuario($_SESSION['user_id']);
        include __DIR__ . '/../views/solicitudes/historial.php';
    }

    public function detalle($id)
    {
        Session::requireLogin('Empleado');
        $solicitud = SolicitudMaterial::find($id, $_SESSION['user_id']);
        $detalles = $solicitud ? SolicitudMaterial::detalles($id) : [];
        include __DIR__ . '/../views/solicitudes/detalle.php';
    }


    // Listar solicitudes pendientes para revisión/entrega
public function revisar()
{
    Session::requireLogin(['Administrador', 'Almacen']);
    $solicitudes = SolicitudMaterial::listarPendientes(['pendiente', 'aprobada']);
    include __DIR__ . '/../views/solicitudes/revisar.php';
}

// Ver y aprobar/rechazar solicitud (formulario con detalle y comentario)
public function aprobar($id)
{
    Session::requireLogin(['Administrador', 'Almacen']);
    $solicitud = SolicitudMaterial::find($id);
    $detalles = $solicitud ? SolicitudMaterial::detalles($id) : [];
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'];
        $observacion = trim($_POST['observacion'] ?? '');
        if ($accion === 'aprobar') {
            SolicitudMaterial::actualizarEstado($id, 'aprobada', $_SESSION['user_id'], $observacion);
            $msg = "Solicitud aprobada correctamente.";
        } elseif ($accion === 'rechazar') {
            SolicitudMaterial::actualizarEstado($id, 'rechazada', $_SESSION['user_id'], $observacion);
            $msg = "Solicitud rechazada.";
        }
        // Recargar solicitud
        $solicitud = SolicitudMaterial::find($id);
    }
    include __DIR__ . '/../views/solicitudes/aprobar.php';
}

// Entregar materiales (solo si ya está aprobada)
public function entregar($id)
{
    Session::requireLogin(['Administrador', 'Almacen']);
    $solicitud = SolicitudMaterial::find($id);
    $detalles = $solicitud ? SolicitudMaterial::detalles($id) : [];
    $msg = '';
    if (!$solicitud || strtolower($solicitud['estado']) !== 'aprobada') {
        $msg = "La solicitud no está aprobada o no existe.";
        include __DIR__ . '/../views/solicitudes/entregar.php';
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $observacion = trim($_POST['observacion'] ?? '');

        // ↓↓↓ RESTA STOCK SOLO A CONSUMIBLES, CREA PRÉSTAMOS PARA HERRAMIENTAS ↓↓↓
        if ($solicitud['tipo_solicitud'] === 'Servicio' || $solicitud['tipo_solicitud'] === 'Mixta') {
            foreach ($detalles as $d) {
                if ($d['producto_id']) {
                    // Buscar tipo del producto
                    $producto = Producto::find($d['producto_id']);
                    if ($producto && $producto['tipo'] === 'Herramienta') {
                        // REGISTRA PRÉSTAMO
                        Prestamo::crear([
                            'producto_id' => $d['producto_id'],
                            'empleado_id' => $solicitud['usuario_id'],
                            'autorizado_by_user_id' => $_SESSION['user_id'],
                            'fecha_prestamo' => date('Y-m-d H:i:s'),
                            'fecha_estimada_devolucion' => null, // Puedes pedir la fecha en el form
                            'estado' => 'Prestado',
                            'observaciones' => $d['observacion'] // opcional
                        ], $d['cantidad']);
                    } else {
                        // CONSUMIBLE: DESCUENTA STOCK
                        Producto::restarStock($d['producto_id'], $d['cantidad']);
                    }
                }
            }
        }

        SolicitudMaterial::actualizarEstado($id, 'entregada', $_SESSION['user_id'], $observacion);
        $msg = "Solicitud entregada correctamente.";
        $solicitud = SolicitudMaterial::find($id);
        $detalles = $solicitud ? SolicitudMaterial::detalles($id) : [];
    }
    include __DIR__ . '/../views/solicitudes/entregar.php';
}



}
