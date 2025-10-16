<?php
require_once __DIR__ . '/../helpers/Database.php';

class Producto
{
    private const ESTADOS = ['Nuevo', 'Usado', 'Dañado', 'En reparación'];
    private const TIPOS = ['Consumible', 'Herramienta'];

    public static function all($filtros = [])
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.*,
                       c.nombre AS categoria,
                       a.nombre AS almacen,
                       u.nombre_completo AS last_user,
                       pr.nombre AS proveedor,
                       um.nombre AS unidad_medida_nombre,
                       um.abreviacion AS unidad_abreviacion,
                       epa.nombre AS estado_activo
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN almacenes a ON p.almacen_id = a.id
                LEFT JOIN usuarios u ON p.last_requested_by_user_id = u.id
                LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
                LEFT JOIN unidades_medida um ON p.unidad_medida_id = um.id
                LEFT JOIN estados_producto_activo epa ON p.activo_id = epa.id
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = '%' . trim($filtros['buscar']) . '%';
            $sql .= " AND (p.nombre LIKE ? OR p.codigo LIKE ? OR p.descripcion LIKE ? OR p.tags LIKE ?)";
            array_push($params, $buscar, $buscar, $buscar, $buscar);
        }

        if (!empty($filtros['nombre'])) {
            $sql .= " AND p.nombre LIKE ?";
            $params[] = '%' . trim($filtros['nombre']) . '%';
        }

        if (!empty($filtros['codigo'])) {
            $sql .= " AND p.codigo LIKE ?";
            $params[] = '%' . trim($filtros['codigo']) . '%';
        }

        if (!empty($filtros['tipo']) && in_array($filtros['tipo'], self::TIPOS, true)) {
            $sql .= " AND p.tipo = ?";
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND p.categoria_id = ?";
            $params[] = (int) $filtros['categoria_id'];
        }

        if (!empty($filtros['almacen_id'])) {
            $sql .= " AND p.almacen_id = ?";
            $params[] = (int) $filtros['almacen_id'];
        }

        if (!empty($filtros['proveedor_id'])) {
            $sql .= " AND p.proveedor_id = ?";
            $params[] = (int) $filtros['proveedor_id'];
        }

        if (!empty($filtros['estado']) && in_array($filtros['estado'], self::ESTADOS, true)) {
            $sql .= " AND p.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['activo_id'])) {
            $sql .= " AND p.activo_id = ?";
            $params[] = (int) $filtros['activo_id'];
        }

        if (!empty($filtros['stock_flag'])) {
            switch ($filtros['stock_flag']) {
                case 'bajo':
                    $sql .= " AND p.stock_actual < p.stock_minimo";
                    break;
                case 'sin':
                    $sql .= " AND p.stock_actual <= 0";
                    break;
                case 'suficiente':
                    $sql .= " AND p.stock_actual >= p.stock_minimo";
                    break;
            }
        }

        if (!empty($filtros['unidad_medida_id'])) {
            $sql .= " AND p.unidad_medida_id = ?";
            $params[] = (int) $filtros['unidad_medida_id'];
        }

        if (!empty($filtros['tags'])) {
            $sql .= " AND p.tags LIKE ?";
            $params[] = '%' . trim($filtros['tags']) . '%';
        }

        if (!empty($filtros['fecha_desde'])) {
            $dt = DateTime::createFromFormat('Y-m-d', $filtros['fecha_desde']);
            if ($dt) {
                $sql .= " AND DATE(p.created_at) >= ?";
                $params[] = $dt->format('Y-m-d');
            }
        }

        if (!empty($filtros['fecha_hasta'])) {
            $dt = DateTime::createFromFormat('Y-m-d', $filtros['fecha_hasta']);
            if ($dt) {
                $sql .= " AND DATE(p.created_at) <= ?";
                $params[] = $dt->format('Y-m-d');
            }
        }

        if (!empty($filtros['valor_min']) && is_numeric($filtros['valor_min'])) {
            $sql .= " AND (p.costo_compra * p.stock_actual) >= ?";
            $params[] = (float) $filtros['valor_min'];
        }

        if (!empty($filtros['valor_max']) && is_numeric($filtros['valor_max'])) {
            $sql .= " AND (p.costo_compra * p.stock_actual) <= ?";
            $params[] = (float) $filtros['valor_max'];
        }

        $sql .= " ORDER BY p.nombre ASC";

        if (isset($filtros['limit']) && is_numeric($filtros['limit'])) {
            $limit = max(1, (int) $filtros['limit']);
            $sql .= " LIMIT {$limit}";
            if (isset($filtros['offset']) && is_numeric($filtros['offset'])) {
                $offset = max(0, (int) $filtros['offset']);
                $sql .= " OFFSET {$offset}";
            }
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO productos (
            codigo, nombre, descripcion, proveedor_id, categoria_id,
            peso, ancho, alto, profundidad, unidad_medida_id, clase_categoria,
            marca, color, forma, especificaciones_tecnicas, origen,
            costo_compra, precio_venta, stock_minimo, stock_actual, almacen_id,
            estado, tipo, imagen_url, last_requested_by_user_id, last_request_date, tags
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['codigo'], $data['nombre'], $data['descripcion'], $data['proveedor_id'], $data['categoria_id'],
            $data['peso'], $data['ancho'], $data['alto'], $data['profundidad'], $data['unidad_medida_id'],
            $data['clase_categoria'], $data['marca'], $data['color'], $data['forma'], $data['especificaciones_tecnicas'],
            $data['origen'], $data['costo_compra'], $data['precio_venta'], $data['stock_minimo'], $data['stock_actual'],
            $data['almacen_id'], $data['estado'], $data['tipo'], $data['imagen_url'],
            $data['last_requested_by_user_id'], $data['last_request_date'], $data['tags']
        ]);
    }

    public static function find($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT p.*,
                                     c.nombre AS categoria,
                                     a.nombre AS almacen,
                                     u.nombre_completo AS last_user,
                                     pr.nombre AS proveedor,
                                     um.nombre AS unidad_medida_nombre,
                                     um.abreviacion AS unidad_abreviacion,
                                     epa.nombre AS estado_activo
                              FROM productos p
                              LEFT JOIN categorias c ON p.categoria_id = c.id
                              LEFT JOIN almacenes a ON p.almacen_id = a.id
                              LEFT JOIN usuarios u ON p.last_requested_by_user_id = u.id
                              LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
                              LEFT JOIN unidades_medida um ON p.unidad_medida_id = um.id
                              LEFT JOIN estados_producto_activo epa ON p.activo_id = epa.id
                              WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByCodigo($codigo)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM productos WHERE codigo = ?");
        $stmt->execute([$codigo]);
        return $stmt->fetch();
    }

    public static function existsCodigoExcept($codigo, $id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM productos WHERE codigo = ? AND id != ?");
        $stmt->execute([$codigo, $id]);
        return $stmt->fetch();
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE productos SET
            codigo=?, nombre=?, descripcion=?, proveedor_id=?, categoria_id=?,
            peso=?, ancho=?, alto=?, profundidad=?, unidad_medida_id=?, clase_categoria=?,
            marca=?, color=?, forma=?, especificaciones_tecnicas=?, origen=?,
            costo_compra=?, precio_venta=?, stock_minimo=?, stock_actual=?, almacen_id=?,
            estado=?, tipo=?, imagen_url=?, last_requested_by_user_id=?, last_request_date=?, tags=?
            WHERE id=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['codigo'], $data['nombre'], $data['descripcion'], $data['proveedor_id'], $data['categoria_id'],
            $data['peso'], $data['ancho'], $data['alto'], $data['profundidad'], $data['unidad_medida_id'],
            $data['clase_categoria'], $data['marca'], $data['color'], $data['forma'], $data['especificaciones_tecnicas'],
            $data['origen'], $data['costo_compra'], $data['precio_venta'], $data['stock_minimo'], $data['stock_actual'],
            $data['almacen_id'], $data['estado'], $data['tipo'], $data['imagen_url'],
            $data['last_requested_by_user_id'], $data['last_request_date'], $data['tags'], $id
        ]);
    }

    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM productos WHERE id=?");
        return $stmt->execute([$id]);
    }

    public static function setActive($id, $active)
    {
        $db = Database::getInstance()->getConnection();
        $estado = $active ? 1 : 2;
        $stmt = $db->prepare("UPDATE productos SET activo_id=? WHERE id=?");
        return $stmt->execute([$estado, $id]);
    }

    public static function sumarStock($id, $cantidad)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE productos SET stock_actual = stock_actual + ? WHERE id = ?");
        return $stmt->execute([$cantidad, $id]);
    }

    public static function restarStock($id, $cantidad)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE productos SET stock_actual = GREATEST(stock_actual - ?, 0) WHERE id = ?");
        return $stmt->execute([$cantidad, $id]);
    }

    public static function allInventario($filtros = [])
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.*,
                       c.nombre AS categoria,
                       u.abreviacion AS unidad,
                       (p.costo_compra * p.stock_actual) AS valor_total,
                       (SELECT MAX(fecha) FROM movimientos_inventario m WHERE m.producto_id = p.id) AS ultimo_movimiento,
                       epa.nombre AS estado_activo
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN unidades_medida u ON p.unidad_medida_id = u.id
                LEFT JOIN estados_producto_activo epa ON p.activo_id = epa.id
                WHERE 1=1";
        $params = [];
        if (!empty($filtros['q'])) {
            $sql .= " AND (p.nombre LIKE ? OR p.codigo LIKE ?)";
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }
        if (!empty($filtros['categoria'])) {
            $sql .= " AND c.nombre = ?";
            $params[] = $filtros['categoria'];
        }
        $sql .= " ORDER BY p.nombre ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }


    public static function inventarioListado(array $filtros, ?int $limit = null, int $offset = 0): array
    {
        $db = Database::getInstance()->getConnection();

        if (!empty($filtros['q']) && empty($filtros['buscar'])) {
            $filtros['buscar'] = $filtros['q'];
        }
        if (!empty($filtros['categoria']) && empty($filtros['categoria_id'])) {
            $filtros['categoria_nombre'] = $filtros['categoria'];
        }

        $condiciones = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = '%' . trim($filtros['buscar']) . '%';
            $condiciones[] = '(p.nombre LIKE ? OR p.codigo LIKE ? OR IFNULL(p.descripcion, "") LIKE ? OR IFNULL(p.tags, "") LIKE ? OR IFNULL(pr.nombre, "") LIKE ?)';
            array_push($params, $buscar, $buscar, $buscar, $buscar, $buscar);
        }

        if (!empty($filtros['categoria_id'])) {
            $condiciones[] = 'p.categoria_id = ?';
            $params[] = (int) $filtros['categoria_id'];
        } elseif (!empty($filtros['categoria_nombre'])) {
            $condiciones[] = 'c.nombre = ?';
            $params[] = trim($filtros['categoria_nombre']);
        }

        if (!empty($filtros['almacen_id'])) {
            $condiciones[] = 'p.almacen_id = ?';
            $params[] = (int) $filtros['almacen_id'];
        }

        if (!empty($filtros['proveedor_id'])) {
            $condiciones[] = 'p.proveedor_id = ?';
            $params[] = (int) $filtros['proveedor_id'];
        }

        if (!empty($filtros['tipo']) && in_array($filtros['tipo'], self::TIPOS, true)) {
            $condiciones[] = 'p.tipo = ?';
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['estado']) && in_array($filtros['estado'], self::ESTADOS, true)) {
            $condiciones[] = 'p.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['activo_id'])) {
            $condiciones[] = 'p.activo_id = ?';
            $params[] = (int) $filtros['activo_id'];
        }

        if (!empty($filtros['unidad_medida_id'])) {
            $condiciones[] = 'p.unidad_medida_id = ?';
            $params[] = (int) $filtros['unidad_medida_id'];
        }

        if (!empty($filtros['stock_flag'])) {
            switch ($filtros['stock_flag']) {
                case 'bajo':
                    $condiciones[] = 'p.stock_actual < p.stock_minimo';
                    break;
                case 'sin':
                    $condiciones[] = 'p.stock_actual <= 0';
                    break;
                case 'suficiente':
                    $condiciones[] = 'p.stock_actual >= p.stock_minimo';
                    break;
            }
        }

        if (!empty($filtros['valor_min']) && is_numeric($filtros['valor_min'])) {
            $condiciones[] = '(p.costo_compra * p.stock_actual) >= ?';
            $params[] = (float) $filtros['valor_min'];
        }

        if (!empty($filtros['valor_max']) && is_numeric($filtros['valor_max'])) {
            $condiciones[] = '(p.costo_compra * p.stock_actual) <= ?';
            $params[] = (float) $filtros['valor_max'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $fechaDesde = DateTime::createFromFormat('Y-m-d', $filtros['fecha_desde']);
            if ($fechaDesde) {
                $condiciones[] = 'DATE(p.created_at) >= ?';
                $params[] = $fechaDesde->format('Y-m-d');
            }
        }

        if (!empty($filtros['fecha_hasta'])) {
            $fechaHasta = DateTime::createFromFormat('Y-m-d', $filtros['fecha_hasta']);
            if ($fechaHasta) {
                $condiciones[] = 'DATE(p.created_at) <= ?';
                $params[] = $fechaHasta->format('Y-m-d');
            }
        }

        $joins = " LEFT JOIN categorias c ON p.categoria_id = c.id"
               . " LEFT JOIN almacenes a ON p.almacen_id = a.id"
               . " LEFT JOIN usuarios u ON p.last_requested_by_user_id = u.id"
               . " LEFT JOIN proveedores pr ON p.proveedor_id = pr.id"
               . " LEFT JOIN unidades_medida um ON p.unidad_medida_id = um.id"
               . " LEFT JOIN estados_producto_activo epa ON p.activo_id = epa.id";

        $whereSql = $condiciones ? ' WHERE ' . implode(' AND ', $condiciones) : '';

        $totalesSql = "SELECT COUNT(*) AS total,"
                    . " SUM(p.costo_compra * p.stock_actual) AS valor_total,"
                    . " SUM(CASE WHEN p.stock_actual < p.stock_minimo THEN 1 ELSE 0 END) AS stock_bajo,"
                    . " SUM(CASE WHEN p.stock_actual <= 0 THEN 1 ELSE 0 END) AS sin_stock,"
                    . " SUM(CASE WHEN p.tipo = 'Consumible' THEN 1 ELSE 0 END) AS consumibles,"
                    . " SUM(CASE WHEN p.tipo = 'Herramienta' THEN 1 ELSE 0 END) AS herramientas,"
                    . " SUM(CASE WHEN p.activo_id = 1 THEN 1 ELSE 0 END) AS activos,"
                    . " SUM(CASE WHEN p.activo_id <> 1 THEN 1 ELSE 0 END) AS inactivos"
                    . " FROM productos p" . $joins . $whereSql;

        $stmtTotales = $db->prepare($totalesSql);
        $stmtTotales->execute($params);
        $totales = $stmtTotales->fetch() ?: [];

        $selectSql = "SELECT p.id, p.codigo, p.nombre, p.descripcion, p.tipo, p.estado, p.stock_actual, p.stock_minimo,"
                    . " p.costo_compra, p.precio_venta, p.almacen_id, p.proveedor_id, p.activo_id, p.created_at,"
                    . " c.nombre AS categoria, a.nombre AS almacen, pr.nombre AS proveedor, um.nombre AS unidad_medida_nombre,"
                    . " um.abreviacion AS unidad_abreviacion, epa.nombre AS estado_activo, u.nombre_completo AS last_user,"
                    . " p.last_requested_by_user_id, p.last_request_date, p.tags,"
                    . " (p.costo_compra * p.stock_actual) AS valor_total,"
                    . " (SELECT MAX(m.fecha) FROM movimientos_inventario m WHERE m.producto_id = p.id) AS ultimo_movimiento"
                    . " FROM productos p" . $joins . $whereSql . " ORDER BY p.nombre ASC";

        if ($limit !== null) {
            $limit = max(1, (int) $limit);
            $offset = max(0, (int) $offset);
            $selectSql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        }

        $stmt = $db->prepare($selectSql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        return [
            'items' => $items,
            'total' => (int) ($totales['total'] ?? 0),
            'stats' => [
                'valor_total' => (float) ($totales['valor_total'] ?? 0),
                'stock_bajo' => (int) ($totales['stock_bajo'] ?? 0),
                'sin_stock' => (int) ($totales['sin_stock'] ?? 0),
                'consumibles' => (int) ($totales['consumibles'] ?? 0),
                'herramientas' => (int) ($totales['herramientas'] ?? 0),
                'activos' => (int) ($totales['activos'] ?? 0),
                'inactivos' => (int) ($totales['inactivos'] ?? 0),
            ],
        ];
    }

    public static function categorias()
    {
        $db = Database::getInstance()->getConnection();
        $cats = $db->query("SELECT nombre FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_COLUMN);
        return $cats ?: [];
    }

    public static function estadosDisponibles(): array
    {
        return self::ESTADOS;
    }

    public static function tiposDisponibles(): array
    {
        return self::TIPOS;
    }

    public static function estadosActivos(): array
    {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT id, nombre FROM estados_producto_activo ORDER BY id")->fetchAll();
    }
}