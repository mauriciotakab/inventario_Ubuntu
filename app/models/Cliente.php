<?php
require_once __DIR__ . '/../helpers/Database.php';

class Cliente
{
    public static function all()
    {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT * FROM clientes ORDER BY nombre ASC")->fetchAll();
    }

    public static function find($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO clientes (nombre, contacto, telefono, email, direccion)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['contacto'],
            $data['telefono'],
            $data['email'],
            $data['direccion']
        ]);
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE clientes SET
                nombre=?, contacto=?, telefono=?, email=?, direccion=?
                WHERE id=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['contacto'],
            $data['telefono'],
            $data['email'],
            $data['direccion'],
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM clientes WHERE id=?");
        return $stmt->execute([$id]);
    }
}
