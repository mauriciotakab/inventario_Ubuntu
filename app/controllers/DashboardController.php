<?php
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../helpers/Database.php';

class DashboardController
{
    public function index(): void
    {
        Session::requireLogin();

        $role = $_SESSION['role'] ?? '';
        $nombre = $_SESSION['nombre'] ?? '';
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        $db = Database::getInstance()->getConnection();

        $datos = [
            'nombre' => $nombre,
            'role' => $role,
            'last_update' => date('d/m/Y, h:i:s a'),
            'alertas' => [],
        ];

        switch ($role) {
            case 'Administrador':
                $datos = array_merge($datos, $this->datosAdministrador($db));
                break;
            case 'Almacen':
                $datos = array_merge($datos, $this->datosAlmacen($db));
                break;
            case 'Empleado':
                $datos = array_merge($datos, $this->datosEmpleado($db, $userId));
                break;
            default:
                $datos = array_merge($datos, $this->datosGenerales($db));
                break;
        }

        include __DIR__ . '/../views/dashboard/index.php';
    }

    private function datosGenerales($db): array
    {
        $totalProductos = (int) $db->query('SELECT COUNT(*) FROM productos')->fetchColumn();
        $stockBajo = (int) $db->query('SELECT COUNT(*) FROM productos WHERE stock_actual < stock_minimo')->fetchColumn();
        $valorTotal = (float) $db->query('SELECT SUM(stock_actual * costo_compra) FROM productos')->fetchColumn();
        $herramientasPrestadas = (int) $db->query("SELECT COUNT(*) FROM prestamos WHERE estado = 'Prestado'")->fetchColumn();

        return [
            'totalProductos' => $totalProductos,
            'stockBajo' => $stockBajo,
            'valorTotalInventario' => $valorTotal,
            'herramientasPrestadas' => $herramientasPrestadas,
            'alertas' => $this->alertasInventario($db),
        ];
    }

    private function datosAdministrador($db): array
    {
        $datos = $this->datosGenerales($db);

        $solicitudesPendientes = (int) $db->query("SELECT COUNT(*) FROM solicitudes_material WHERE estado = 'pendiente'")->fetchColumn();
        $solicitudesAprobadas = (int) $db->query("SELECT COUNT(*) FROM solicitudes_material WHERE estado = 'aprobada'")->fetchColumn();

        $datos['solicitudesPendientes'] = $solicitudesPendientes;
        $datos['solicitudesAprobadas'] = $solicitudesAprobadas;
        $datos['ultimaActualizacion'] = $this->ultimasActualizaciones($db);

        return $datos;
    }

    private function datosAlmacen($db): array
    {
        $datos = $this->datosGenerales($db);

        $productosAlmacen = (int) $db->query('SELECT COUNT(*) FROM productos')->fetchColumn();
        $solicitudesPorGestionar = (int) $db->query("SELECT COUNT(*) FROM solicitudes_material WHERE estado IN ('pendiente','aprobada')")->fetchColumn();

        $datos['productosAlmacen'] = $productosAlmacen;
        $datos['solicitudesAlmacen'] = $solicitudesPorGestionar;
        $datos['ultimosMovimientos'] = $this->expuestosMovimientos($db);

        return $datos;
    }

    private function datosEmpleado($db, int $userId): array
    {
        $solicitudesEnviadas = (int) $db->query("SELECT COUNT(*) FROM solicitudes_material WHERE usuario_id = {$userId}")->fetchColumn();
        $pendientesAprobacion = (int) $db->query("SELECT COUNT(*) FROM solicitudes_material WHERE usuario_id = {$userId} AND estado = 'pendiente'")->fetchColumn();
        $entregadas = (int) $db->query("SELECT COUNT(*) FROM solicitudes_material WHERE usuario_id = {$userId} AND estado = 'entregada'")->fetchColumn();

        $alertas = $db->prepare("SELECT comentario, estado, DATE_FORMAT(fecha_solicitud, '%d/%m/%Y') AS fecha
                                  FROM solicitudes_material
                                  WHERE usuario_id = ?
                                  ORDER BY fecha_solicitud DESC
                                  LIMIT 5");
        $alertas->execute([$userId]);

        return [
            'solicitudesMias' => $solicitudesEnviadas,
            'pendientesAprobar' => $pendientesAprobacion,
            'entregadas' => $entregadas,
            'alertas' => $alertas->fetchAll() ?: [],
        ];
    }

    private function alertasInventario($db): array
    {
        $stmt = $db->query("SELECT nombre, stock_actual, stock_minimo, DATE_FORMAT(created_at, '%d/%m/%Y') AS fecha
                             FROM productos
                             WHERE stock_actual < stock_minimo
                             ORDER BY stock_actual ASC
                             LIMIT 5");
        $productos = $stmt->fetchAll();

        $alertas = [];
        foreach ($productos as $p) {
            $alertas[] = [
                $p['nombre'] . ' por debajo del stock mínimo',
                $p['fecha'],
                'alta',
            ];
        }
        return $alertas;
    }

    private function ultimasActualizaciones($db): array
    {
        $stmt = $db->query("SELECT p.nombre,
                                    m.tipo,
                                    m.fecha,
                                    m.cantidad,
                                    COALESCE(a.nombre, ad.nombre) AS almacen
                             FROM movimientos_inventario m
                             LEFT JOIN productos p ON m.producto_id = p.id
                             LEFT JOIN almacenes a ON m.almacen_origen_id = a.id
                             LEFT JOIN almacenes ad ON m.almacen_destino_id = ad.id
                             ORDER BY m.fecha DESC
                             LIMIT 5");
        return $stmt->fetchAll();
    }

    private function expuestosMovimientos($db): array
    {
        $stmt = $db->query("SELECT p.nombre,
                                    p.codigo,
                                    m.tipo,
                                    m.cantidad,
                                    m.fecha,
                                    COALESCE(a.nombre, ad.nombre) AS almacen
                             FROM movimientos_inventario m
                             LEFT JOIN productos p ON m.producto_id = p.id
                             LEFT JOIN almacenes a ON m.almacen_origen_id = a.id
                             LEFT JOIN almacenes ad ON m.almacen_destino_id = ad.id
                             ORDER BY m.fecha DESC
                             LIMIT 7");
        return $stmt->fetchAll();
    }
}
