private function checkAuth() {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: /auth/index');
        exit;
    }
}
