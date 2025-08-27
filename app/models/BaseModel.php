<?php
/**
 * BaseModel - Modelo base corregido para BD real
 */
abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id'; // Por defecto, pero puede ser sobrescrito

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Obtener todos los registros
    public function getAll() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener por ID (usa la primary key correcta)
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Crear registro
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute(array_values($data))) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Actualizar registro (usa la primary key correcta)
    public function update($id, $data) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);

        $values = array_values($data);
        $values[] = $id;

        return $stmt->execute($values);
    }

    // Eliminar registro (usa la primary key correcta)
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
