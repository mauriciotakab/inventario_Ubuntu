<?php
// DIAGNÓSTICO: no dejar en producción
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Mexico_City');

$dsn  = 'mysql:host=127.0.0.1;dbname=takab_inventario;charset=utf8mb4';
$user = 'inventario_user';
$pass = 'AdminTakab123';

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  echo "<h3>Conexión OK</h3>";
} catch (PDOException $e) {
  http_response_code(500);
  exit("Error de conexión: ".$e->getMessage());
}

// Descubrir columnas obligatorias de productos
$cols = $pdo->query("SHOW COLUMNS FROM productos")->fetchAll();
echo "<pre>SHOW COLUMNS FROM productos:\n".print_r($cols,true)."</pre>";

// Intento de INSERT mínimo (ajustaremos tras ver columnas)
try {
  // Valores tentativos: corrige nombres/valores según tus columnas NOT NULL
  $stmt = $pdo->prepare("
    INSERT INTO productos (codigo, nombre, descripcion, precio_venta, stock_minimo, stock_actual, tipo, activo_id, created_at)
    VALUES (:codigo, :nombre, :descripcion, :precio, :stock_min, :stock_act, 'Consumible', 1, NOW())
  ");
  $stmt->execute([
    ':codigo'      => 'TEST-'.time(),
    ':nombre'      => 'Producto de prueba',
    ':descripcion' => 'Insert de diagnóstico',
    ':precio'      => 0,
    ':stock_min'   => 0,
    ':stock_act'   => 0,
  ]);
  $id = $pdo->lastInsertId();
  echo "<p>INSERT OK, id=$id</p>";
} catch (PDOException $e) {
  echo "<p style='color:red'>Error en INSERT: ".$e->getMessage()."</p>";
}

// Intento de DELETE del id recién creado (si se logró insertar)
if (!empty($id)) {
  try {
    $pdo->prepare("DELETE FROM productos WHERE id = ?")->execute([$id]);
    echo "<p>DELETE OK del id=$id</p>";
  } catch (PDOException $e) {
    echo "<p style='color:red'>Error en DELETE: ".$e->getMessage()."</p>";
  }
}
