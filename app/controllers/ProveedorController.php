<?php
require_once __DIR__ . '/../models/Proveedor.php';
require_once __DIR__ . '/../helpers/Session.php';

class ProveedorController
{
    public function index()
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $proveedores = Proveedor::all();
        include __DIR__ . '/../views/proveedores/index.php';
    }

    public function create()
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $msg = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'contacto' => $_POST['contacto'],
                'telefono' => $_POST['telefono'],
                'email' => $_POST['email'],
                'direccion' => $_POST['direccion'],
                'condiciones_pago' => $_POST['condiciones_pago'],
            ];
            Proveedor::create($data);
            $msg = "Proveedor registrado correctamente.";
        }
        include __DIR__ . '/../views/proveedores/create.php';
    }

    public function edit($id)
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $proveedor = Proveedor::find($id);
        $msg = '';
        if (!$proveedor) die("Proveedor no encontrado.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'contacto' => $_POST['contacto'],
                'telefono' => $_POST['telefono'],
                'email' => $_POST['email'],
                'direccion' => $_POST['direccion'],
                'condiciones_pago' => $_POST['condiciones_pago'],
            ];
            Proveedor::update($id, $data);
            $msg = "Proveedor actualizado correctamente.";
            $proveedor = Proveedor::find($id);
        }
        include __DIR__ . '/../views/proveedores/edit.php';
    }

    public function delete($id)
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        Proveedor::delete($id);
        header("Location: proveedores.php?deleted=1");
        exit();
    }
}
