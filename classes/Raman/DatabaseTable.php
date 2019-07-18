<?php
namespace Raman;

class DatabaseTable
{

  private $pdo;
  private $table;
  private $primaryKey;

  public function __construct(\PDO $pdo, string $table, string $primaryKey)
  {
    $this->pdo = $pdo;
    $this->table = $table;
    $this->primaryKey = $primaryKey;
  }

  // Query
  private function query($sql, $params = [])
  {
    $query = $this->pdo->prepare($sql);
    $query->execute($params);
    return $query;
  }

  // Fetch
  public function fetch($id)
  {
    $sql = "SELECT * FROM $this->table WHERE $this->primaryKey = :primaryKey";
    $params = ['primaryKey' => $id];
    $query = $this->query($sql, $params);
    return $query->fetch();
  }

  // Fetch all by column with certain value
  public function fetchByCol($column, $value)
  {
    $sql = "SELECT * FROM $this->table WHERE $column = :value";
    $params = ['value' => $value];
    $query = $this->query($sql, $params);
    return $query->fetchAll();
  }

  // Fetch all
  public function fetchAll()
  {
    $sql = "SELECT * FROM $this->table";
    $query = $this->query($sql);
    return $query->fetchAll();
  }

  // Insert
  private function insert($fields)
  {
    // Preparing SQL query
    $sql = 'INSERT INTO ' . $this->table . ' (';
    foreach ($fields as $key => $value) {
      $sql .= $key . ',';
    }
    $sql = rtrim($sql , ',');
    $sql .= ') VALUES (';

    foreach ($fields as $key => $value) {
      $sql .= ':' . $key . ',';
    }
    $sql = rtrim($sql , ',');
    $sql .= ')';

    // Executing query
    $query = $this->query($sql, $fields);
    // Check if query succeeded
    return $query->rowCount();
  }

  // Update
  private function update($fields)
  {
    // Prepare SQL query
    $sql = 'UPDATE ' . $this->table . ' SET ';
    foreach ($fields as $key => $value) {
      $sql .= $key . ' = :' . $key . ',';
    }
    $sql = rtrim($sql, ',');
    $sql .= ' WHERE ' . $this->primaryKey . ' = :primaryKey';
    $fields['primaryKey'] = $fields[$this->primaryKey];

    // Execute query
    $query = $this->query($sql, $fields);
    // Check if query succeeded
    return $query->rowCount();
  }

  // Insert/Update
  public function save($fields)
  {
    try {
      if (empty($fields[$this->primaryKey])) {
        $fields[$this->primaryKey] = NULL;
      }
      return $this->insert($fields);
    }
    catch (\PDOException $e) {
      return $this->update($fields);
    }
  }

  // Delete
  public function delete($id)
  {
    $sql = "DELETE from $this->table WHERE $this->primaryKey = :primaryKey";
    $params = ['primaryKey' => $id];
    $query = $this->query($sql, $params);
    // Check if query succeeded
    return $query->rowCount();
  }
}
