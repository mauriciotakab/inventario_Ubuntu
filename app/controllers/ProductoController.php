<?php
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../helpers/Session.php';

class ProductoController
{
    public function index()
    {
        Session::requireLogin(['Administrador', 'Almacen']);

        $filtros = [
            'buscar' => trim($_GET['buscar'] ?? ''),
            'nombre' => trim($_GET['nombre'] ?? ''),
            'codigo' => trim($_GET['codigo'] ?? ''),
            'tipo' => $_GET['tipo'] ?? '',
            'categoria_id' => $_GET['categoria_id'] ?? '',
            'almacen_id' => $_GET['almacen_id'] ?? '',
            'proveedor_id' => $_GET['proveedor_id'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'activo_id' => $_GET['activo_id'] ?? '',
            'stock_flag' => $_GET['stock_flag'] ?? '',
            'unidad_medida_id' => $_GET['unidad_medida_id'] ?? '',
            'tags' => trim($_GET['tags'] ?? ''),
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'valor_min' => $_GET['valor_min'] ?? '',
            'valor_max' => $_GET['valor_max'] ?? '',
        ];

        $productos = Producto::all($filtros);

        $stats = [
            'total' => count($productos),
            'consumibles' => 0,
            'herramientas' => 0,
            'stock_bajo' => 0,
            'sin_stock' => 0,
            'activos' => 0,
            'inactivos' => 0,
            'valor_total' => 0.0,
        ];

        foreach ($productos as $producto) {
            $cantidad = (int) ($producto['stock_actual'] ?? 0);
            $minimo = (int) ($producto['stock_minimo'] ?? 0);
            $valorProducto = (float) ($producto['costo_compra'] ?? 0) * $cantidad;
            $stats['valor_total'] += $valorProducto;

            if (($producto['tipo'] ?? '') === 'Consumible') {
                $stats['consumibles']++;
            } elseif (($producto['tipo'] ?? '') === 'Herramienta') {
                $stats['herramientas']++;
            }

            if ($cantidad < $minimo) {
                $stats['stock_bajo']++;
            }
            if ($cantidad <= 0) {
                $stats['sin_stock']++;
            }
            if ((int) ($producto['activo_id'] ?? 1) === 1) {
                $stats['activos']++;
            } else {
                $stats['inactivos']++;
            }
        }

        $db = Database::getInstance()->getConnection();
        $categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetchAll();
        $almacenes = $db->query("SELECT id, nombre FROM almacenes ORDER BY nombre ASC")->fetchAll();
        $proveedores = $db->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC")->fetchAll();
        $unidades = $db->query("SELECT id, nombre, abreviacion FROM unidades_medida ORDER BY nombre ASC")->fetchAll();
        $estadosActivos = Producto::estadosActivos();
        $estadosProducto = Producto::estadosDisponibles();
        $tiposProducto = Producto::tiposDisponibles();

        $hayFiltros = false;
        foreach ($filtros as $valor) {
            if ($valor !== '' && $valor !== null) {
                $hayFiltros = true;
                break;
            }
        }

        $alerta = [
            'success' => $_GET['success'] ?? null,
            'deleted' => $_GET['deleted'] ?? null,
        ];

        include __DIR__ . '/../views/productos/index.php';
    }

    public function create()
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $db = Database::getInstance()->getConnection();
        $categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetchAll();
        $proveedores = $db->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC")->fetchAll();
        $almacenes = $db->query("SELECT id, nombre FROM almacenes ORDER BY nombre ASC")->fetchAll();
        $unidades = $db->query("SELECT id, nombre, abreviacion FROM unidades_medida ORDER BY nombre ASC")->fetchAll();
        $estadosProducto = Producto::estadosDisponibles();
        $tiposProducto = Producto::tiposDisponibles();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Producto::findByCodigo($_POST['codigo'])) {
                $error = "Ya existe un producto con ese código.";
            } else {
                $imagen_path = null;
                if (!empty($_FILES['imagen_url']['name'])) {
                    $upload_dir = __DIR__ . '/..//assets/images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen_url']['name']);
                    $target_file = $upload_dir . $nombreArchivo;
                    $tipoArchivo = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    $check = @getimagesize($_FILES['imagen_url']['tmp_name']);
                    if ($check === false) {
                        $error = "El archivo no es una imagen válida.";
                    } elseif ($_FILES['imagen_url']['size'] > 2 * 1024 * 1024) {
                        $error = "La imagen es demasiado grande (máx. 2MB).";
                    } elseif (!in_array($tipoArchivo, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                        $error = "Formato de imagen no permitido.";
                    } elseif (!move_uploaded_file($_FILES['imagen_url']['tmp_name'], $target_file)) {
                        $error = "Error al subir la imagen.";
                    } else {
                        $imagen_path = 'assets/images/' . $nombreArchivo;
                    }
                }
                if (empty($error)) {
                    $data = $_POST;
                    $data['imagen_url'] = $imagen_path;
                    $data['last_requested_by_user_id'] = null;
                    $data['last_request_date'] = null;
                    Producto::create($data);
                    header('Location: productos.php?success=1');
                    exit();
                }
            }
        }

        include __DIR__ . '/../views/productos/create.php';
    }

    public function edit($id)
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $producto = Producto::find($id);
        if (!$producto) {
            die('Producto no encontrado.');
        }

        $db = Database::getInstance()->getConnection();
        $categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetchAll();
        $proveedores = $db->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC")->fetchAll();
        $almacenes = $db->query("SELECT id, nombre FROM almacenes ORDER BY nombre ASC")->fetchAll();
        $unidades = $db->query("SELECT id, nombre, abreviacion FROM unidades_medida ORDER BY nombre ASC")->fetchAll();
        $estadosProducto = Producto::estadosDisponibles();
        $tiposProducto = Producto::tiposDisponibles();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Producto::existsCodigoExcept($_POST['codigo'], $id)) {
                $error = "Ya existe otro producto con ese código.";
            } else {
                $imagen_path = $producto['imagen_url'];
                if (!empty($_FILES['imagen_url']['name'])) {
                    $upload_dir = __DIR__ . '/..//assets/images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen_url']['name']);
                    $target_file = $upload_dir . $nombreArchivo;
                    $tipoArchivo = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    $check = @getimagesize($_FILES['imagen_url']['tmp_name']);
                    if ($check === false) {
                        $error = "El archivo no es una imagen válida.";
                    } elseif ($_FILES['imagen_url']['size'] > 2 * 1024 * 1024) {
                        $error = "La imagen es demasiado grande (máx. 2MB).";
                    } elseif (!in_array($tipoArchivo, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                        $error = "Formato de imagen no permitido.";
                    } elseif (!move_uploaded_file($_FILES['imagen_url']['tmp_name'], $target_file)) {
                        $error = "Error al subir la imagen.";
                    } else {
                        $imagen_path = 'assets/images/' . $nombreArchivo;
                    }
                }
                if (empty($error)) {
                    $data = $_POST;
                    $data['imagen_url'] = $imagen_path;
                    $data['last_requested_by_user_id'] = $producto['last_requested_by_user_id'];
                    $data['last_request_date'] = $producto['last_request_date'];
                    Producto::update($id, $data);
                    header('Location: productos.php?success=2');
                    exit();
                }
            }
        }

        include __DIR__ . '/../views/productos/edit.php';
    }

    public function view($id)
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $producto = Producto::find($id);
        if (!$producto) {
            die('Producto no encontrado.');
        }
        include __DIR__ . '/../views/productos/view.php';
    }

    public function delete($id)
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        Producto::delete($id);
        header('Location: productos.php?deleted=1');
        exit();
    }

    public function setActive($id, $active)
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        Producto::setActive($id, $active);
        header('Location: productos.php');
        exit();
    }
}