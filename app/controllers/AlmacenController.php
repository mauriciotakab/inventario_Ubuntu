<?php
require_once __DIR__ . '/../models/Almacen.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/Session.php';

class AlmacenController
{
    public function index() {
        Session::requireLogin(['Administrador']);
        $almacenes = Almacen::all();
        include __DIR__ . '/../views/almacenes/index.php';
    }

    public function create() {
        Session::requireLogin(['Administrador']);
        $usuarios = Usuario::all();
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Almacen::create($_POST);
            header("Location: almacenes.php?success=1");
            exit();
        }
        include __DIR__ . '/../views/almacenes/create.php';
    }

    public function edit($id) {
        Session::requireLogin(['Administrador']);
        $almacen = Almacen::find($id);
        $usuarios = Usuario::all();
        $error = '';
        if (!$almacen) die("Almacén no encontrado.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Almacen::update($id, $_POST);
            header("Location: almacenes.php?success=2");
            exit();
        }
        include __DIR__ . '/../views/almacenes/edit.php';
    }

    public function delete($id) {
        Session::requireLogin(['Administrador']);
        Almacen::delete($id);
        header("Location: almacenes.php?deleted=1");
        exit();
    }
}
