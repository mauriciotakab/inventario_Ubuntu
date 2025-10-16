<?php
require_once __DIR__ . '/../helpers/Database.php';

class UnidadMedida {
    public static function all() {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT * FROM unidades_medida")->fetchAll();
    }

    public static function find($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM unidades_medida WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO unidades_medida (nombre, abreviacion) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$data['nombre'], $data['abreviacion']]);
    }

    public static function update($id, $data) {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE unidades_medida SET nombre=?, abreviacion=? WHERE id=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$data['nombre'], $data['abreviacion'], $id]);
    }

    public static function delete($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM unidades_medida WHERE id=?");
        return $stmt->execute([$id]);
    }
}
