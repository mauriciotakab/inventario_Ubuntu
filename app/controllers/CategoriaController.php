<?php
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../helpers/Session.php';

class CategoriaController
{
    public function index() {
        Session::requireLogin(['Administrador']);
        $categorias = Categoria::all();
        include __DIR__ . '/../views/categorias/index.php';
    }

    public function create() {
        Session::requireLogin(['Administrador']);
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nombre'])) {
                $error = "El nombre es obligatorio.";
            } else {
                Categoria::create($_POST);
                header("Location: categorias.php?success=1");
                exit();
            }
        }
        include __DIR__ . '/../views/categorias/create.php';
    }

    public function edit($id) {
        Session::requireLogin(['Administrador']);
        $categoria = Categoria::find($id);
        $error = '';
        if (!$categoria) die("Categoría no encontrada.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nombre'])) {
                $error = "El nombre es obligatorio.";
            } else {
                Categoria::update($id, $_POST);
                header("Location: categorias.php?success=2");
                exit();
            }
        }
        include __DIR__ . '/../views/categorias/edit.php';
    }

    public function delete($id) {
        Session::requireLogin(['Administrador']);
        Categoria::delete($id);
        header("Location: categorias.php?deleted=1");
        exit();
    }
}
