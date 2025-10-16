<?php
require_once __DIR__ . '/../helpers/Database.php';

class Categoria {
    public static function all() {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT * FROM categorias")->fetchAll();
    }

    public static function find($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$data['nombre'], $data['descripcion']]);
    }

    public static function update($id, $data) {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE categorias SET nombre=?, descripcion=? WHERE id=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$data['nombre'], $data['descripcion'], $id]);
    }

    public static function delete($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM categorias WHERE id=?");
        return $stmt->execute([$id]);
    }
}
