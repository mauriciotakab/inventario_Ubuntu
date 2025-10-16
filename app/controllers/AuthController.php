<?php
require_once __DIR__ . '/../models/Usuario.php';
session_start();

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = Usuario::findByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                // Guardar datos en sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nombre'] = $user['nombre_completo'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }
        include __DIR__ . '/../views/auth/login.php';
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
    public function forgotPassword()
{
    $mensaje = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $user = Usuario::findByUsername($username);
        if ($user) {
            $mensaje = "Por favor contacta al administrador para restablecer tu contraseña.";
            // O aquí puedes implementar envío de correo o generación de código temporal
        } else {
            $mensaje = "Usuario no encontrado.";
        }
    }
    include __DIR__ . '/../views/auth/forgot.php';
}

}
