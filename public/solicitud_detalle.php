<?php
require_once __DIR__ . '/../app/controllers/SolicitudMaterialController.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$controller = new SolicitudMaterialController();
$controller->detalle($id);
