<?php
require_once __DIR__ . '/../helpers/Database.php';

class Proveedor
{
    public static function all()
    {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT * FROM proveedores ORDER BY nombre ASC")->fetchAll();
    }

    public static function find($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, condiciones_pago) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['contacto'],
            $data['telefono'],
            $data['email'],
            $data['direccion'],
            $data['condiciones_pago']
        ]);
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE proveedores SET 
            nombre=?, contacto=?, telefono=?, email=?, direccion=?, condiciones_pago=?
            WHERE id=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['contacto'],
            $data['telefono'],
            $data['email'],
            $data['direccion'],
            $data['condiciones_pago'],
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM proveedores WHERE id=?");
        return $stmt->execute([$id]);
    }
}
