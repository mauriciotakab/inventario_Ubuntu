<?php
require_once __DIR__ . '/../app/controllers/ProveedorController.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$controller = new ProveedorController();
$controller->edit($id);
