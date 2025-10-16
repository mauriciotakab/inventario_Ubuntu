<?php
require_once __DIR__ . '/../app/controllers/UsuarioController.php';
$id = $_GET['id'] ?? 0;
$controller = new UsuarioController();
$controller->edit($id);
