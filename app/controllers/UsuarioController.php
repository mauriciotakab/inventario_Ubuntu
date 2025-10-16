<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/Session.php';

class UsuarioController
{
    public function index()
    {
        Session::requireLogin('Administrador');
        $usuarios = Usuario::all();
        include __DIR__ . '/../views/usuarios/index.php';
    }

    public function create()
    {
        Session::requireLogin('Administrador');
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validaciones básicas
            $username = trim($_POST['username']);
            $nombre = trim($_POST['nombre_completo']);
            $password = $_POST['password'];
            $role = $_POST['role'];
            $activo = isset($_POST['activo']) ? 1 : 0;

            if (empty($username) || empty($nombre) || empty($password) || empty($role)) {
                $error = "Todos los campos son obligatorios.";
            } else {
                // Verificar que no exista el usuario
                if (Usuario::findByUsername($username)) {
                    $error = "Ese nombre de usuario ya está registrado.";
                } else {
                    Usuario::create([
                        'username' => $username,
                        'password' => $password,
                        'nombre_completo' => $nombre,
                        'role' => $role,
                        'activo' => $activo
                    ]);
                    header("Location: usuarios.php?success=1");
                    exit();
                }
            }
        }
        include __DIR__ . '/../views/usuarios/create.php';
    }

    public function edit($id)
    {
        Session::requireLogin('Administrador');
        $usuario = Usuario::findById($id);
        $error = '';
        if (!$usuario) {
            die("Usuario no encontrado.");
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $nombre = trim($_POST['nombre_completo']);
            $password = $_POST['password'];
            $role = $_POST['role'];
            $activo = isset($_POST['activo']) ? 1 : 0;
            Usuario::update($id, [
                'username' => $username,
                'nombre_completo' => $nombre,
                'password' => $password,
                'role' => $role,
                'activo' => $activo
            ]);
            header("Location: usuarios.php?success=2");
            exit();
        }
        include __DIR__ . '/../views/usuarios/edit.php';
    }

    public function setActive($id, $active)
    {
        Session::requireLogin('Administrador');
        Usuario::setActive($id, $active);
        header("Location: usuarios.php");
        exit();
    }
}
