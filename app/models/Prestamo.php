<?php
require_once __DIR__ . '/../helpers/Database.php';

class Prestamo
{
    // Listar préstamos pendientes de devolución
    public static function pendientes()
{
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT pr.*, p.nombre AS producto, p.codigo AS codigo_producto, u.nombre_completo AS empleado
            FROM prestamos pr
            LEFT JOIN productos p ON pr.producto_id = p.id
            LEFT JOIN usuarios u ON pr.empleado_id = u.id
            WHERE pr.estado = 'Prestado'
            ORDER BY pr.fecha_prestamo DESC";
    return $db->query($sql)->fetchAll();
}

    // Buscar préstamo específico
    public static function find($id)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT pr.*, p.nombre AS producto, u.nombre_completo AS empleado
                FROM prestamos pr
                LEFT JOIN productos p ON pr.producto_id = p.id
                LEFT JOIN usuarios u ON pr.empleado_id = u.id
                WHERE pr.id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Registrar devolución
    public static function devolver($id, $estado_devolucion, $observaciones, $fecha = null)
    {
        $db = Database::getInstance()->getConnection();
        // Actualiza préstamo
        $sql = "UPDATE prestamos SET estado = 'Devuelto', estado_devolucion = ?, fecha_devolucion = ?, observaciones = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $fechaDevolucion = $fecha ?? date('Y-m-d H:i:s');
        $stmt->execute([$estado_devolucion, $fechaDevolucion, $observaciones, $id]);

        // Recupera el id del producto
        $prestamo = self::find($id);
        if ($prestamo && $prestamo['producto_id']) {
            // Devuelve herramienta al inventario (suma 1)
            Producto::sumarStock($prestamo['producto_id'], 1);
        }
        return true;
    }

    // Historial completo
    
public static function historial()
{
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT pr.*, p.nombre AS producto, p.codigo AS codigo_producto, u.nombre_completo AS empleado
            FROM prestamos pr
            LEFT JOIN productos p ON pr.producto_id = p.id
            LEFT JOIN usuarios u ON pr.empleado_id = u.id
            ORDER BY pr.fecha_prestamo DESC";
    return $db->query($sql)->fetchAll();
}

    public static function crear($data, $cantidad = 1)
{
    $db = Database::getInstance()->getConnection();
    for ($i = 0; $i < $cantidad; $i++) {
        $sql = "INSERT INTO prestamos
                (producto_id, empleado_id, autorizado_by_user_id, fecha_prestamo, fecha_estimada_devolucion, estado, observaciones)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['producto_id'],
            $data['empleado_id'],
            $data['autorizado_by_user_id'],
            $data['fecha_prestamo'],
            $data['fecha_estimada_devolucion'],
            $data['estado'],
            $data['observaciones']
        ]);
    }
}

public static function historialPaginado($busqueda = '', $pagina = 1, $porPagina = 15)
{
    $db = Database::getInstance()->getConnection();
    $offset = ($pagina - 1) * $porPagina;
    $cond = '';
    $params = [];
    if ($busqueda !== '') {
        $cond = "WHERE (p.codigo LIKE ? OR u.nombre_completo LIKE ?)";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }
    $sql = "SELECT pr.*, p.nombre AS producto, p.codigo AS codigo_producto, u.nombre_completo AS empleado
            FROM prestamos pr
            LEFT JOIN productos p ON pr.producto_id = p.id
            LEFT JOIN usuarios u ON pr.empleado_id = u.id
            $cond
            ORDER BY pr.fecha_prestamo DESC
            LIMIT $porPagina OFFSET $offset";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

public static function totalHistorial($busqueda = '')
{
    $db = Database::getInstance()->getConnection();
    $cond = '';
    $params = [];
    if ($busqueda !== '') {
        $cond = "WHERE (p.codigo LIKE ? OR u.nombre_completo LIKE ?)";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }
    $sql = "SELECT COUNT(*) FROM prestamos pr
            LEFT JOIN productos p ON pr.producto_id = p.id
            LEFT JOIN usuarios u ON pr.empleado_id = u.id
            $cond";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}



}
