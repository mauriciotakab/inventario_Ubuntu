<?php
require_once __DIR__ . '/../app/controllers/ProductoController.php';
$id = $_GET['id'] ?? 0;
$active = $_GET['active'] ?? 0;
$controller = new ProductoController();
$controller->setActive($id, $active);
