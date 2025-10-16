<?php
require_once __DIR__ . '/../app/controllers/PrestamoController.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$controller = new PrestamoController();
$controller->devolver($id);

