<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../helpers/Session.php';

class ClienteController
{
    public function index()
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $clientes = Cliente::all();
        include __DIR__ . '/../views/clientes/index.php';
    }

    public function create()
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $msg = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'   => $_POST['nombre'],
                'contacto' => $_POST['contacto'],
                'telefono' => $_POST['telefono'],
                'email'    => $_POST['email'],
                'direccion'=> $_POST['direccion'],
            ];
            Cliente::create($data);
            $msg = "Cliente registrado correctamente.";
        }
        include __DIR__ . '/../views/clientes/create.php';
    }

    public function edit($id)
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        $cliente = Cliente::find($id);
        $msg = '';
        if (!$cliente) die("Cliente no encontrado.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'   => $_POST['nombre'],
                'contacto' => $_POST['contacto'],
                'telefono' => $_POST['telefono'],
                'email'    => $_POST['email'],
                'direccion'=> $_POST['direccion'],
            ];
            Cliente::update($id, $data);
            $msg = "Cliente actualizado correctamente.";
            $cliente = Cliente::find($id);
        }
        include __DIR__ . '/../views/clientes/edit.php';
    }

    public function delete($id)
    {
        Session::requireLogin(['Administrador', 'Almacen']);
        Cliente::delete($id);
        header("Location: clientes.php?deleted=1");
        exit();
    }
}
