<?php

namespace Src\Models;

use PDO;
use Src\Classes\DB;

class Category extends Model
{
    protected static $table = "categories";

    public static function ordered()
    {
        $stmt = DB::conn()->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByName($name)
    {
        $stmt = DB::conn()->prepare("SELECT * FROM categories WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
