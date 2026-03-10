<?php

namespace Src\Models;

use PDO;
use Src\Classes\DB;

abstract class Model
{
    protected static $table = "";

    protected $condition = "";
    protected $parameters = [];

    public static function query()
    {
        return new static();
    }
    public static function all(array $columns = ["*"])
    {
        $table = static::$table;
        $columns = implode(", ", $columns);
        $stmt =  DB::conn()->query("SELECT $columns from $table");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id ,array $columns = ["*"])
    {
        $table = static::$table;
        $columns = implode(", ", $columns);
        $stmt = DB::conn()->query("SELECT $columns from $table WHERE id = $id");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function where($column, $value)
    {
        if ($this->condition != "") {
            $this->condition .= " AND $column = ?";
        }else{
            $this->condition .= "$column = ?";
        }
        $this->parameters[] = $value;

        return $this;
    }

    public function orWhere($column, $value)
    {
        if ($this->condition != "") {
            $this->condition .= " OR $column = ?";
            $this->parameters[] = $value;
            return $this;
        }else{
            return $this->where($column, $value);
        }
    }

    public function whereIn($column, array $values)
    {
        $placeholders = implode(", ", array_fill(0, count($values), "?"));

        $this->condition = "$column IN ($placeholders)";

        $this->parameters = array_merge($this->parameters, $values);

        return $this;
    }

    public function get(array $columns = ["*"])
    {
        $table = static::$table;
        $columns = implode(", ", $columns);

        $where = "";
        if ($this->condition != "") {
            $where = "where " . $this->condition;
        }

        $stmt = DB::conn()->prepare("SELECT $columns FROM $table $where");
        
        $stmt->execute($this->parameters);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data)
    {
        $table = static::$table;
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = DB::conn()->prepare($sql);
        
        if($stmt->execute(array_values($data))){
            $id = DB::conn()->lastInsertId();
            return static::find($id);
        };
        return false;
    }

    public static function createMany(array $data)
    {
        $table = static::$table;
        $columns = implode(", ", array_keys($data[0]));
        $placeholders = implode(", ", array_fill(0, count($data[0]), "?"));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = DB::conn()->prepare($sql);

        DB::conn()->beginTransaction();

        foreach ($data as $key => $row) {
            $stmt->execute(array_values($row));
        }

        DB::conn()->commit();

        return true;
    }

    public static function update($id, array $data)
    {
        $table = static::$table;
        $parameters = [];

        $sql = "UPDATE $table SET ";
        $i = 0;
        foreach ($data as $column => $value) {
            if($i == 0) {
                $sql .= "$column = ?";
            } else {
                $sql .= ", $column = ?";
            }
            $parameters[] = $value;
            $i++;
        }

        $sql .= " WHERE id = ?";
        $parameters[] = $id;

        $stmt = DB::conn()->prepare($sql);
        $stmt->execute($parameters);

        return true;
    }

    public static function delete($id, $column = "id")
    {
        $table = static::$table;
        $sql = "DELETE FROM $table where $column = ?";
        $stmt = DB::conn()->prepare($sql);
        $stmt->execute([$id]);
        return true;
    }
}
