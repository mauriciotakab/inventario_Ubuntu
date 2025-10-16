<?php
require_once __DIR__ . '/../models/MovimientoInventario.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Almacen.php';
require_once __DIR__ . '/../helpers/Session.php';

class InventarioController
{
    public function entrada()
    {
        Session::requireLogin(['Administrador', 'Almacen']);

        $productos = Producto::all();
        $almacenes = Almacen::all();
        $msg = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoId = $_POST['producto_id'] ?? null;
            $almacenId = $_POST['almacen_id'] ?? null;
            $cantidad = isset($_POST['cantidad']) ? (float) $_POST['cantidad'] : 0;

            if ($productoId && $almacenId && $cantidad > 0) {
                $data = [
                    'producto_id' => $productoId,
                    'tipo' => 'Entrada',
                    'cantidad' => $cantidad,
                    'usuario_id' => $_SESSION['user_id'],
                    'almacen_destino_id' => $almacenId,
                    'observaciones' => trim($_POST['observaciones'] ?? '')
                ];

                MovimientoInventario::registrar($data);
                Producto::sumarStock($data['producto_id'], $data['cantidad']);
                $msg = "Entrada registrada correctamente.";
            } else {
                $msg = "Por favor completa los campos obligatorios.";
            }
        }

        $movimientosRecientes = MovimientoInventario::ultimos('Entrada', 6);

        include __DIR__ . '/../views/inventario/entrada.php';
    }

    public function salida()
    {
        Session::requireLogin(['Administrador', 'Almacen']);

        $productos = Producto::all();
        $almacenes = Almacen::all();
        $msg = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoId = $_POST['producto_id'] ?? null;
            $almacenId = $_POST['almacen_id'] ?? null;
            $cantidad = isset($_POST['cantidad']) ? (float) $_POST['cantidad'] : 0;

            if ($productoId && $almacenId && $cantidad > 0) {
                $data = [
                    'producto_id' => $productoId,
                    'tipo' => 'Salida',
                    'cantidad' => $cantidad,
                    'usuario_id' => $_SESSION['user_id'],
                    'almacen_origen_id' => $almacenId,
                    'observaciones' => trim($_POST['observaciones'] ?? '')
                ];

                MovimientoInventario::registrar($data);
                Producto::restarStock($data['producto_id'], $data['cantidad']);
                $msg = "Salida registrada correctamente.";
            } else {
                $msg = "Por favor completa los campos obligatorios.";
            }
        }

        $movimientosRecientes = MovimientoInventario::ultimos('Salida', 6);

        include __DIR__ . '/../views/inventario/salida.php';
    }

    public function actual()
    {
        Session::requireLogin();

        $role = $_SESSION['role'] ?? 'Empleado';

        $filtros = [
            'buscar' => trim($_GET['buscar'] ?? ($_GET['q'] ?? '')),
            'categoria_id' => $_GET['categoria_id'] ?? '',
            'almacen_id' => $_GET['almacen_id'] ?? '',
            'proveedor_id' => $_GET['proveedor_id'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'activo_id' => $_GET['activo_id'] ?? '',
            'stock_flag' => $_GET['stock_flag'] ?? '',
            'valor_min' => $_GET['valor_min'] ?? '',
            'valor_max' => $_GET['valor_max'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'unidad_medida_id' => $_GET['unidad_medida_id'] ?? '',
        ];

        if (!empty($_GET['cat']) && empty($filtros['categoria_id'])) {
            $filtros['categoria'] = $_GET['cat'];
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPageOptions = [10, 15, 25, 50, 100];
        $perPage = (int) ($_GET['per_page'] ?? 15);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 15;
        }
        $offset = ($page - 1) * $perPage;

        $resultado = Producto::inventarioListado($filtros, $perPage, $offset);
        $productos = $resultado['items'];
        $stats = $resultado['stats'];
        $totalRegistros = $resultado['total'];
        $totalPaginas = max(1, (int) ceil($totalRegistros / $perPage));

        if ($page > $totalPaginas) {
            $page = $totalPaginas;
            $offset = ($page - 1) * $perPage;
            $resultado = Producto::inventarioListado($filtros, $perPage, $offset);
            $productos = $resultado['items'];
            $stats = $resultado['stats'];
        }

        $db = Database::getInstance()->getConnection();
        $categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetchAll();
        $almacenes = $db->query("SELECT id, nombre FROM almacenes ORDER BY nombre ASC")->fetchAll();
        $proveedores = $db->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC")->fetchAll();
        $unidades = $db->query("SELECT id, nombre, abreviacion FROM unidades_medida ORDER BY nombre ASC")->fetchAll();

        $tiposProducto = Producto::tiposDisponibles();
        $estadosProducto = Producto::estadosDisponibles();
        $estadosActivos = Producto::estadosActivos();

        $hayFiltros = false;
        foreach ($filtros as $valor) {
            if ($valor !== '' && $valor !== null) {
                $hayFiltros = true;
                break;
            }
        }

        include __DIR__ . '/../views/inventario/actual.php';
    }
}
