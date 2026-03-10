<?php

namespace Src\Models;

use PDO;
use Src\Classes\DB;
use Throwable;

class Order extends Model
{
    protected static $table = "orders";

    public static function createWithItems($userId, $room, $notes, array $items)
    {
        $connection = DB::conn();

        try {
            $connection->beginTransaction();

            $total = 0;
            foreach ($items as $item) {
                $total += $item['quantity'] * $item['unit_price'];
            }

            $stmt = $connection->prepare("
                INSERT INTO orders (total_amount, notes, delivery_room, user_id)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$total, $notes, $room, $userId]);

            $orderId = (int) $connection->lastInsertId();
            $itemStmt = $connection->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['unit_price']]);
            }

            $connection->commit();
            return $orderId;
        } catch (Throwable $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $e;
        }
    }

    public static function forUser($userId, array $filters = [])
    {
        $conditions = ['o.user_id = ?'];
        $parameters = [$userId];

        if (!empty($filters['date_from'])) {
            $conditions[] = 'DATE(o.order_date) >= ?';
            $parameters[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = 'DATE(o.order_date) <= ?';
            $parameters[] = $filters['date_to'];
        }

        $where = implode(' AND ', $conditions);
        $stmt = DB::conn()->prepare("
            SELECT o.*
            FROM orders o
            WHERE $where
            ORDER BY o.order_date DESC, o.id DESC
        ");
        $stmt->execute($parameters);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orders)) {
            return [];
        }

        $orderIds = array_map(fn($order) => (int) $order['id'], $orders);
        $placeholders = implode(', ', array_fill(0, count($orderIds), '?'));
        $itemsStmt = DB::conn()->prepare("
            SELECT oi.order_id, oi.quantity, oi.unit_price, p.name
            FROM order_items oi
            INNER JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id IN ($placeholders)
            ORDER BY oi.order_id DESC, p.name ASC
        ");
        $itemsStmt->execute($orderIds);

        $itemsByOrder = [];
        foreach ($itemsStmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $itemsByOrder[$item['order_id']][] = $item;
        }

        foreach ($orders as &$order) {
            $order['items'] = $itemsByOrder[$order['id']] ?? [];
        }

        return $orders;
    }

    public static function cancelForUser($orderId, $userId)
    {
        $stmt = DB::conn()->prepare("
            DELETE FROM orders
            WHERE id = ? AND user_id = ? AND status = 'Processing'
        ");
        $stmt->execute([$orderId, $userId]);

        return $stmt->rowCount() > 0;
    }

    public static function allWithUsers($search = '')
    {
        $sql = "
            SELECT
                o.*,
                u.name AS user_name,
                u.room_no,
                u.ext
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
        ";
        $parameters = [];

        if ($search !== '') {
            $sql .= " WHERE u.name LIKE ? OR o.delivery_room LIKE ? OR u.ext LIKE ? OR o.status LIKE ?";
            $term = '%' . $search . '%';
            $parameters = [$term, $term, $term, $term];
        }

        $sql .= " ORDER BY o.order_date DESC, o.id DESC";
        $stmt = DB::conn()->prepare($sql);
        $stmt->execute($parameters);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orders)) {
            return [];
        }

        $orderIds = array_map(fn($order) => (int) $order['id'], $orders);
        $placeholders = implode(', ', array_fill(0, count($orderIds), '?'));
        $itemsStmt = DB::conn()->prepare("
            SELECT oi.order_id, oi.quantity, oi.unit_price, p.name
            FROM order_items oi
            INNER JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id IN ($placeholders)
            ORDER BY oi.order_id DESC, p.name ASC
        ");
        $itemsStmt->execute($orderIds);

        $itemsByOrder = [];
        foreach ($itemsStmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $itemsByOrder[$item['order_id']][] = $item;
        }

        foreach ($orders as &$order) {
            $order['items'] = $itemsByOrder[$order['id']] ?? [];
        }

        return $orders;
    }

    public static function markOutForDelivery($orderId)
    {
        $stmt = DB::conn()->prepare("
            UPDATE orders
            SET status = 'Out for delivery'
            WHERE id = ? AND status = 'Processing'
        ");
        $stmt->execute([$orderId]);

        return $stmt->rowCount() > 0;
    }

    public static function markDone($orderId)
    {
        $stmt = DB::conn()->prepare("
            UPDATE orders
            SET status = 'Done'
            WHERE id = ? AND status = 'Out for delivery'
        ");
        $stmt->execute([$orderId]);

        return $stmt->rowCount() > 0;
    }

    public static function reportByUsers(array $filters = [])
    {
        $conditions = [];
        $parameters = [];

        if (!empty($filters['date_from'])) {
            $conditions[] = 'DATE(o.order_date) >= ?';
            $parameters[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = 'DATE(o.order_date) <= ?';
            $parameters[] = $filters['date_to'];
        }

        if (!empty($filters['user_id'])) {
            $conditions[] = 'u.id = ?';
            $parameters[] = $filters['user_id'];
        }

        $conditions[] = 'u.is_admin = 0';
        $where = 'WHERE ' . implode(' AND ', $conditions);

        $stmt = DB::conn()->prepare("
            SELECT
                u.id AS user_id,
                u.name AS user_name,
                u.email,
                COALESCE(SUM(o.total_amount), 0) AS total_amount
            FROM users u
            LEFT JOIN orders o ON o.user_id = u.id
            $where
            GROUP BY u.id, u.name, u.email
            HAVING COUNT(o.id) > 0
            ORDER BY total_amount DESC, u.name ASC
        ");
        $stmt->execute($parameters);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return [];
        }

        $userIds = array_map(fn($row) => (int) $row['user_id'], $rows);
        $userPlaceholders = implode(', ', array_fill(0, count($userIds), '?'));

        $orderConditions = ['o.user_id IN (' . $userPlaceholders . ')'];
        $orderParameters = $userIds;

        if (!empty($filters['date_from'])) {
            $orderConditions[] = 'DATE(o.order_date) >= ?';
            $orderParameters[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $orderConditions[] = 'DATE(o.order_date) <= ?';
            $orderParameters[] = $filters['date_to'];
        }

        $orderWhere = implode(' AND ', $orderConditions);
        $ordersStmt = DB::conn()->prepare("
            SELECT
                o.*,
                u.name AS user_name
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            WHERE $orderWhere
            ORDER BY o.order_date DESC, o.id DESC
        ");
        $ordersStmt->execute($orderParameters);
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orders)) {
            return [];
        }

        $orderIds = array_map(fn($order) => (int) $order['id'], $orders);
        $orderPlaceholders = implode(', ', array_fill(0, count($orderIds), '?'));
        $itemsStmt = DB::conn()->prepare("
            SELECT oi.order_id, oi.quantity, oi.unit_price, p.name
            FROM order_items oi
            INNER JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id IN ($orderPlaceholders)
            ORDER BY oi.order_id DESC, p.name ASC
        ");
        $itemsStmt->execute($orderIds);

        $itemsByOrder = [];
        foreach ($itemsStmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $itemsByOrder[$item['order_id']][] = $item;
        }

        $ordersByUser = [];
        foreach ($orders as $order) {
            $order['items'] = $itemsByOrder[$order['id']] ?? [];
            $ordersByUser[$order['user_id']][] = $order;
        }

        foreach ($rows as &$row) {
            $row['orders'] = $ordersByUser[$row['user_id']] ?? [];
        }

        return $rows;
    }
}
