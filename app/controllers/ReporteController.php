<?php
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../helpers/Database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Prestamo.php';
require_once __DIR__ . '/../models/MovimientoInventario.php';

class ReporteController
{
    public function index(): void
    {
        Session::requireLogin(['Administrador', 'Almacen']);

        $role = $_SESSION['role'] ?? 'Almacen';
        $mostrarCostos = $role === 'Administrador';

        $fechaFin = $this->parseDate($_GET['to'] ?? date('Y-m-d'));
        $fechaInicio = $this->parseDate($_GET['from'] ?? date('Y-m-01'));
        if ($fechaInicio > $fechaFin) {
            $tmp = $fechaInicio;
            $fechaInicio = $fechaFin;
            $fechaFin = $tmp;
        }

        $movTipo = $_GET['mov_tipo'] ?? '';
        $movTipo = in_array($movTipo, ['Entrada', 'Salida', 'Transferencia'], true) ? $movTipo : '';

        $inventarioResumen = $this->resumenInventario();
        $inventarioBajo = $this->reporteInventarioBajo();
        $valorPorAlmacen = $mostrarCostos ? $this->reporteValorPorAlmacen() : [];
        $movimientos = $this->reporteMovimientos($fechaInicio, $fechaFin, $movTipo);
        $prestamosAbiertos = $this->reportePrestamosActivos($fechaInicio, $fechaFin);
        $prestamosVencidos = $this->reportePrestamosVencidos();
        $topSalidas = $this->reporteTopSalidas($fechaInicio, $fechaFin);
        $estadoInventario = $this->reporteEstadoInventario();

        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $section = $_GET['section'] ?? '';
            $this->exportCsv($section, [
                'inventario_bajo' => $inventarioBajo,
                'valor_almacen' => $valorPorAlmacen,
                'movimientos' => $movimientos,
                'prestamos_abiertos' => $prestamosAbiertos,
                'prestamos_vencidos' => $prestamosVencidos,
                'top_salidas' => $topSalidas,
                'estado_inventario' => $estadoInventario,
            ], $mostrarCostos, $fechaInicio, $fechaFin);
            return;
        }

        $filters = [
            'from' => $fechaInicio,
            'to' => $fechaFin,
            'mov_tipo' => $movTipo,
        ];

        include __DIR__ . '/../views/reportes/index.php';
    }

    private function parseDate(string $value): string
    {
        $date = DateTime::createFromFormat('Y-m-d', substr($value, 0, 10));
        return $date ? $date->format('Y-m-d') : date('Y-m-d');
    }

    private function resumenInventario(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) AS total,
                       SUM(CASE WHEN stock_actual < stock_minimo THEN 1 ELSE 0 END) AS stock_bajo,
                       SUM(CASE WHEN stock_actual <= 0 THEN 1 ELSE 0 END) AS sin_stock,
                       SUM(CASE WHEN tipo = 'Consumible' THEN 1 ELSE 0 END) AS consumibles,
                       SUM(CASE WHEN tipo = 'Herramienta' THEN 1 ELSE 0 END) AS herramientas,
                       SUM(CASE WHEN activo_id = 1 THEN 1 ELSE 0 END) AS activos,
                       SUM(CASE WHEN activo_id <> 1 THEN 1 ELSE 0 END) AS inactivos,
                       SUM(stock_actual * costo_compra) AS valor_total
                FROM productos";
        $data = $db->query($sql)->fetch();

        $prestamosPendientes = $db->query("SELECT COUNT(*) FROM prestamos WHERE estado = 'Prestado'")->fetchColumn();
        $prestamosVencidos = $db->query("SELECT COUNT(*)
                                         FROM prestamos
                                         WHERE estado = 'Prestado'
                                           AND fecha_estimada_devolucion IS NOT NULL
                                           AND fecha_estimada_devolucion < NOW()")->fetchColumn();

        return [
            'total' => (int) ($data['total'] ?? 0),
            'stock_bajo' => (int) ($data['stock_bajo'] ?? 0),
            'sin_stock' => (int) ($data['sin_stock'] ?? 0),
            'consumibles' => (int) ($data['consumibles'] ?? 0),
            'herramientas' => (int) ($data['herramientas'] ?? 0),
            'activos' => (int) ($data['activos'] ?? 0),
            'inactivos' => (int) ($data['inactivos'] ?? 0),
            'valor_total' => (float) ($data['valor_total'] ?? 0),
            'prestamos_pendientes' => (int) $prestamosPendientes,
            'prestamos_vencidos' => (int) $prestamosVencidos,
        ];
    }

    private function reporteInventarioBajo(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.codigo,
                       p.nombre,
                       c.nombre AS categoria,
                       a.nombre AS almacen,
                       p.stock_actual,
                       p.stock_minimo,
                       um.abreviacion AS unidad,
                       p.tipo
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN almacenes a ON p.almacen_id = a.id
                LEFT JOIN unidades_medida um ON p.unidad_medida_id = um.id
                WHERE p.stock_actual < p.stock_minimo
                ORDER BY p.stock_actual ASC";
        return $db->query($sql)->fetchAll();
    }

    private function reporteValorPorAlmacen(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT a.nombre AS almacen,
                       COUNT(p.id) AS productos,
                       SUM(p.stock_actual) AS unidades,
                       SUM(p.stock_actual * p.costo_compra) AS valor_total
                FROM almacenes a
                LEFT JOIN productos p ON p.almacen_id = a.id
                GROUP BY a.id
                ORDER BY valor_total DESC";
        return $db->query($sql)->fetchAll();
    }

    private function reporteMovimientos(string $desde, string $hasta, string $tipo = ''): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT m.fecha,
                       m.tipo,
                       m.cantidad,
                       m.observaciones,
                       p.codigo,
                       p.nombre AS producto,
                       ao.nombre AS almacen_origen,
                       ad.nombre AS almacen_destino,
                       u.nombre_completo AS usuario
                FROM movimientos_inventario m
                LEFT JOIN productos p ON m.producto_id = p.id
                LEFT JOIN almacenes ao ON m.almacen_origen_id = ao.id
                LEFT JOIN almacenes ad ON m.almacen_destino_id = ad.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE DATE(m.fecha) BETWEEN ? AND ?";
        $params = [$desde, $hasta];
        if ($tipo !== '') {
            $sql .= " AND m.tipo = ?";
            $params[] = $tipo;
        }
        $sql .= " ORDER BY m.fecha DESC LIMIT 200";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function reportePrestamosActivos(string $desde, string $hasta): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT pr.id,
                       pr.fecha_prestamo,
                       pr.fecha_estimada_devolucion,
                       p.nombre AS producto,
                       p.codigo,
                       u.nombre_completo AS empleado,
                       pr.observaciones,
                       pr.estado
                FROM prestamos pr
                LEFT JOIN productos p ON pr.producto_id = p.id
                LEFT JOIN usuarios u ON pr.empleado_id = u.id
                WHERE pr.estado = 'Prestado'
                  AND DATE(pr.fecha_prestamo) BETWEEN ? AND ?
                ORDER BY pr.fecha_prestamo DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    private function reportePrestamosVencidos(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT pr.id,
                       pr.fecha_prestamo,
                       pr.fecha_estimada_devolucion,
                       TIMESTAMPDIFF(DAY, pr.fecha_estimada_devolucion, NOW()) AS dias_vencidos,
                       p.nombre AS producto,
                       p.codigo,
                       u.nombre_completo AS empleado,
                       pr.observaciones
                FROM prestamos pr
                LEFT JOIN productos p ON pr.producto_id = p.id
                LEFT JOIN usuarios u ON pr.empleado_id = u.id
                WHERE pr.estado = 'Prestado'
                  AND pr.fecha_estimada_devolucion IS NOT NULL
                  AND pr.fecha_estimada_devolucion < NOW()
                ORDER BY pr.fecha_estimada_devolucion ASC";
        return $db->query($sql)->fetchAll();
    }

    private function reporteTopSalidas(string $desde, string $hasta): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.codigo,
                       p.nombre,
                       SUM(m.cantidad) AS total_salidas,
                       SUM(m.cantidad * p.costo_compra) AS costo_estimado
                FROM movimientos_inventario m
                INNER JOIN productos p ON m.producto_id = p.id
                WHERE m.tipo = 'Salida'
                  AND DATE(m.fecha) BETWEEN ? AND ?
                GROUP BY p.id
                ORDER BY total_salidas DESC
                LIMIT 10";
        $stmt = $db->prepare($sql);
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    private function reporteEstadoInventario(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT estado,
                       COUNT(*) AS cantidad,
                       SUM(stock_actual) AS unidades,
                       SUM(stock_actual * costo_compra) AS valor
                FROM productos
                GROUP BY estado
                ORDER BY estado";
        return $db->query($sql)->fetchAll();
    }

    private function exportCsv(string $section, array $datasets, bool $mostrarCostos, string $desde, string $hasta): void
    {
        if (!isset($datasets[$section])) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Reporte no disponible.';
            return;
        }

        if (!$mostrarCostos && $section === 'valor_almacen') {
            header('HTTP/1.1 403 Forbidden');
            echo 'No tienes permisos para exportar este reporte.';
            return;
        }

        $rows = $datasets[$section];
        $filename = 'reporte_' . $section . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputs($output, chr(239) . chr(187) . chr(191));

        switch ($section) {
            case 'inventario_bajo':
                fputcsv($output, ['Código', 'Producto', 'Categoría', 'Almacén', 'Stock actual', 'Stock mínimo', 'Unidad', 'Tipo']);
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['codigo'],
                        $row['nombre'],
                        $row['categoria'],
                        $row['almacen'],
                        $row['stock_actual'],
                        $row['stock_minimo'],
                        $row['unidad'],
                        $row['tipo'],
                    ]);
                }
                break;
            case 'valor_almacen':
                fputcsv($output, ['Almacén', 'Productos', 'Unidades', 'Valor total (MXN)']);
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['almacen'],
                        $row['productos'],
                        $row['unidades'],
                        number_format((float) $row['valor_total'], 2, '.', ''),
                    ]);
                }
                break;
            case 'movimientos':
                fputcsv($output, ['Fecha', 'Tipo', 'Código', 'Producto', 'Cantidad', 'Almacén origen', 'Almacén destino', 'Usuario', 'Observaciones']);
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['fecha'],
                        $row['tipo'],
                        $row['codigo'],
                        $row['producto'],
                        $row['cantidad'],
                        $row['almacen_origen'],
                        $row['almacen_destino'],
                        $row['usuario'],
                        $row['observaciones'],
                    ]);
                }
                break;
            case 'prestamos_abiertos':
                fputcsv($output, ['ID', 'Fecha préstamo', 'Fecha estimada', 'Producto', 'Código', 'Empleado', 'Observaciones', 'Estado']);
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['id'],
                        $row['fecha_prestamo'],
                        $row['fecha_estimada_devolucion'],
                        $row['producto'],
                        $row['codigo'],
                        $row['empleado'],
                        $row['observaciones'],
                        $row['estado'],
                    ]);
                }
                break;
            case 'prestamos_vencidos':
                fputcsv($output, ['ID', 'Fecha préstamo', 'Fecha estimada', 'Días vencidos', 'Producto', 'Código', 'Empleado', 'Observaciones']);
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['id'],
                        $row['fecha_prestamo'],
                        $row['fecha_estimada_devolucion'],
                        $row['dias_vencidos'],
                        $row['producto'],
                        $row['codigo'],
                        $row['empleado'],
                        $row['observaciones'],
                    ]);
                }
                break;
            case 'top_salidas':
                fputcsv($output, ['Código', 'Producto', 'Cantidad salida', 'Costo estimado (MXN)']);
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['codigo'],
                        $row['nombre'],
                        $row['total_salidas'],
                        number_format((float) $row['costo_estimado'], 2, '.', ''),
                    ]);
                }
                break;
            case 'estado_inventario':
                fputcsv($output, ['Estado', 'Productos', 'Unidades', 'Valor (MXN)']);
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['estado'],
                        $row['cantidad'],
                        $row['unidades'],
                        number_format((float) $row['valor'], 2, '.', ''),
                    ]);
                }
                break;
        }

        fclose($output);
    }
}
