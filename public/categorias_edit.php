<?php
require_once __DIR__ . '/../app/controllers/CategoriaController.php';
$id = $_GET['id'] ?? 0;
$controller = new CategoriaController();
$controller->edit($id);
