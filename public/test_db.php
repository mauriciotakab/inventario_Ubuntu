<?php
require __DIR__ . '/../app/helpers/Database.php';

try {
    $db = \App\Helpers\Database::getInstance()->getConnection();
    echo "Conexión a la base de datos exitosa.";
} catch (Exception $e) {
    echo "Error en la conexión: " . $e->getMessage();
}