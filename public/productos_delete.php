<?php
require_once __DIR__ . '/../app/controllers/ProductoController.php';
$id = $_GET['id'] ?? 0;
$controller = new ProductoController();
$controller->delete($id);
