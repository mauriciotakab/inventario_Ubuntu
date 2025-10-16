<?php
require_once __DIR__ . '/../app/controllers/AlmacenController.php';
$id = $_GET['id'] ?? 0;
$controller = new AlmacenController();
$controller->delete($id);
