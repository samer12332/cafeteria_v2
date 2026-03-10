<?php

namespace Src\Models;

use PDO;
use Src\Classes\DB;

class Product extends Model
{
    protected static $table = "products";

    public static function availableWithCategories($availableOnly = false)
    {
        $sql = "
            SELECT p.id, p.name, p.price, p.is_available, COALESCE(c.name, 'General') AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
        ";

        if ($availableOnly) {
            $sql .= " WHERE p.is_available = 1";
        }

        $sql .= " ORDER BY p.name ASC";
        $stmt = DB::conn()->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findAvailableByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $stmt = DB::conn()->prepare("
            SELECT id, name, price, is_available
            FROM products
            WHERE id IN ($placeholders)
        ");
        $stmt->execute($ids);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function allWithCategories($search = '')
    {
        return static::paginateWithCategories($search, 1, PHP_INT_MAX)['items'];
    }

    public static function paginateWithCategories($search = '', $page = 1, $perPage = 10)
    {
        $sql = "
            SELECT
                p.*,
                COALESCE(c.name, 'Uncategorized') AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
        ";
        $countSql = "
            SELECT COUNT(*)
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
        ";
        $parameters = [];
        $where = "";

        if ($search !== '') {
            $where = " WHERE p.name LIKE ? OR COALESCE(c.name, 'Uncategorized') LIKE ?";
            $term = '%' . $search . '%';
            $parameters = [$term, $term];
        }

        $countStmt = DB::conn()->prepare($countSql . $where);
        $countStmt->execute($parameters);
        $total = (int) $countStmt->fetchColumn();

        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;

        $sql .= $where . " ORDER BY p.id DESC LIMIT $perPage OFFSET $offset";
        $stmt = DB::conn()->prepare($sql);
        $stmt->execute($parameters);

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
        ];
    }

    public static function findWithCategory($id)
    {
        $stmt = DB::conn()->prepare("
            SELECT
                p.*,
                c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
