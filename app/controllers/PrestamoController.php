<?php
require_once __DIR__ . '/../models/Prestamo.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../helpers/Session.php';

class PrestamoController
{
    // Listar préstamos pendientes de devolución
    public function pendientes()
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $prestamos = Prestamo::pendientes();
        include __DIR__ . '/../views/prestamos/pendientes.php';
    }

    // Registrar devolución (formulario)
    public function devolver($id)
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $prestamo = Prestamo::find($id);
        $msg = '';
        if (!$prestamo || $prestamo['estado'] !== 'Prestado') {
            $msg = "Este préstamo no está pendiente de devolución o no existe.";
            include __DIR__ . '/../views/prestamos/devolver.php';
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $estado_devolucion = $_POST['estado_devolucion'];
            $observaciones = trim($_POST['observaciones'] ?? '');
            Prestamo::devolver($id, $estado_devolucion, $observaciones);
            $msg = "Devolución registrada correctamente.";
            // Recargar préstamo actualizado
            $prestamo = Prestamo::find($id);
        }
        include __DIR__ . '/../views/prestamos/devolver.php';
    }

    // Historial completo
    public function historial()
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $prestamos = Prestamo::historial();
        include __DIR__ . '/../views/prestamos/historial.php';
    }
}
