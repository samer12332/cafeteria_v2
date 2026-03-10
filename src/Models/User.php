<?php

namespace Src\Models;

use PDO;
use Src\Classes\DB;

class User extends Model{
    protected static $table = "users";

    public static function findByEmail($email)
    {
        $stmt = DB::conn()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function attempt($email, $password)
    {
        $user = static::findByEmail($email);
        if (!$user) {
            return null;
        }

        $storedPassword = (string) ($user['password'] ?? '');
        $isValid = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);

        return $isValid ? $user : null;
    }

    public static function allNormalUsers($search = '')
    {
        return static::paginateNormalUsers($search, 1, PHP_INT_MAX)['items'];
    }

    public static function paginateNormalUsers($search = '', $page = 1, $perPage = 10)
    {
        $sql = "
            SELECT *
            FROM users
            WHERE is_admin = 0
        ";
        $countSql = "
            SELECT COUNT(*)
            FROM users
            WHERE is_admin = 0
        ";
        $parameters = [];
        $searchSql = '';

        if ($search !== '') {
            $searchSql = " AND (name LIKE ? OR email LIKE ? OR room_no LIKE ? OR ext LIKE ?)";
            $term = '%' . $search . '%';
            $parameters = [$term, $term, $term, $term];
        }

        $countStmt = DB::conn()->prepare($countSql . $searchSql);
        $countStmt->execute($parameters);
        $total = (int) $countStmt->fetchColumn();

        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;

        $sql .= $searchSql . " ORDER BY id DESC LIMIT $perPage OFFSET $offset";
        $stmt = DB::conn()->prepare($sql);
        $stmt->execute($parameters);

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
        ];
    }

    public static function findNormalUser($id)
    {
        $stmt = DB::conn()->prepare("
            SELECT *
            FROM users
            WHERE id = ? AND is_admin = 0
            LIMIT 1
        ");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function emailExists($email, $ignoreId = null)
    {
        $sql = "SELECT id FROM users WHERE email = ?";
        $params = [$email];

        if ($ignoreId !== null) {
            $sql .= " AND id != ?";
            $params[] = $ignoreId;
        }

        $sql .= " LIMIT 1";
        $stmt = DB::conn()->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    public static function updatePasswordByEmail($email, $hashedPassword)
    {
        $stmt = DB::conn()->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        return $stmt->rowCount() > 0;
    }
}
