<?php
require_once __DIR__ . '/../app/controllers/UnidadMedidaController.php';
$id = $_GET['id'] ?? 0;
$controller = new UnidadMedidaController();
$controller->edit($id);
