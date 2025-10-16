jfdlk<?php
require_once __DIR__ . '/../models/UnidadMedida.php';
require_once __DIR__ . '/../helpers/Session.php';

class UnidadMedidaController
{
    public function index() {
        Session::requireLogin(['Administrador']);
        $unidades = UnidadMedida::all();
        include __DIR__ . '/../views/unidades/index.php';
    }

    public function create() {
        Session::requireLogin(['Administrador']);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nombre']) || empty($_POST['abreviacion'])) {
                $error = "Todos los campos son obligatorios.";
            } else {
                UnidadMedida::create($_POST);
                header("Location: unidades.php?success=1");
                exit();
            }
        }
        include __DIR__ . '/../views/unidades/create.php';
    }
    public function edit($id) {
        Session::requireLogin(['Administrador']);
        $unidad = UnidadMedida::find($id);
        $error = '';
        if (!$unidad) die("Unidad no encontrada.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nombre']) || empty($_POST['abreviacion'])) {
                $error = "Todos los campos son obligatorios.";
            } else {
                UnidadMedida::update($id, $_POST);
                header("Location: unidades.php?success=2");
                exit();
            }
        }
        include __DIR__ . '/../views/unidades/edit.php';
    }

    public function delete($id) {
        Session::requireLogin(['Administrador']);
        UnidadMedida::delete($id);
        header("Location: unidades.php?deleted=1");
        exit();
    }
}
