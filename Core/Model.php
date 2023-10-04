<?php

class Model
{
    protected $table;
    protected $id;
    protected $db;

    public function __construct(string $table, string $id, PDO $db)
    {
        $this->table = $table;
        $this->id = $id;
        $this->db = $db;
    }

    // All
    public function getAll(string $prefix = '')
    {
        $stmt = $this->db->prepare("SELECT * FROM {$prefix}{$this->table}");
        if (!$stmt->execute()) {
            throw new Exception($stmt->errorInfo()[2]);
        }
        return $stmt->fetchAll();
    }

    // Get by id
    public function getById(int $id, string $prefix = '')
    {
        $stmt = $this->db->prepare("SELECT * FROM {$prefix}{$this->table} WHERE $this->id = :$this->id LIMIT 1");
        $stmt->bindValue(":$this->id", $id);
        if (!$stmt->execute()) {
            throw new Exception($stmt->errorInfo()[2]);
        }
        return $stmt->fetch();
    }

    // Delete
    public function deleteById(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->id} = :{$this->id}");
        $stmt->bindValue(":{$this->id}", $id);
        if (!$stmt->execute()) {
            throw new Exception($stmt->errorInfo()[2]);
        }

        return $id;
    }

    // Update
    public function updateById(int $id, array $data)
    {
        // Update
        $columnUpdates = [];
        foreach ($data as $key => $value) {
            $columnUpdates[] = "{$key} = :{$key}";
        }
        $columnUpdatesString = implode(", ", $columnUpdates);

        $sql = "UPDATE {$this->table} SET {$columnUpdatesString} WHERE {$this->id} = :{$this->id}";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $rowValue) {
            $paramType = PDO::PARAM_STR;
            if (is_bool($rowValue)) {
                $paramType = PDO::PARAM_BOOL;
            } elseif (is_int($rowValue)) {
                $paramType = PDO::PARAM_INT;
            }
            $stmt->bindValue(":{$key}", $rowValue, $paramType);
        }
        $stmt->bindValue(":{$this->id}", $id);

        if (!$stmt->execute()) {
            throw new Exception($stmt->errorInfo()[2]);
        }

        return $id;
    }

    // Insert
    public function insert(array $data)
    {
        // Insert Params
        $columns = array_keys($data);
        $columnNames = implode(", ", $columns);
        $columnPlaceholders = implode(", :", $columns);

        // SQL Statement
        $sql = "INSERT INTO {$this->table} ({$columnNames}) VALUES (:{$columnPlaceholders})";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $rowValue) {
            $paramType = PDO::PARAM_STR;
            if (is_bool($rowValue)) {
                $paramType = PDO::PARAM_BOOL;
            } elseif (is_int($rowValue)) {
                $paramType = PDO::PARAM_INT;
            }
            $stmt->bindValue(":{$key}", $rowValue, $paramType);
        }

        if (!$stmt->execute()) {
            throw new Exception($stmt->errorInfo()[2]);
        }

        return $this->db->lastInsertId();
    }
}
