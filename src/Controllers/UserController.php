<?php

namespace Src\Controllers;

use Src\Classes\DB;
use Src\Models\Product;
use Throwable;

class UserController
{
    public function index()
    {
        if (!is_authenticated()) {
            redirect('/login');
        }

        redirect_by_role();
    }

    public function dashboard()
    {
        $currentUser = require_auth('user');
        $dashboardRole = 'user';
        $dashboardLabel = 'Office Team';
        $homePath = '/user';
        $pageTitle = 'Cafeteria | User Dashboard';
        $dashboardData = $this->buildDashboardData($currentUser, false);
        $successMessage = get_flash('order_success');
        $errorMessage = get_flash('order_error');
        $oldForm = get_flash('order_old', []);

        view('users.php', array_merge(
            $dashboardData,
            compact('pageTitle', 'currentUser', 'dashboardRole', 'dashboardLabel', 'homePath', 'successMessage', 'errorMessage', 'oldForm')
        ));
    }

    public function adminDashboard()
    {
        $currentUser = require_auth('admin');
        $dashboardRole = 'admin';
        $dashboardLabel = 'Administrator';
        $homePath = '/admin';
        $pageTitle = 'Cafeteria | Admin Dashboard';
        $dashboardData = $this->buildDashboardData($currentUser, true);
        $successMessage = '';
        $errorMessage = '';
        $oldForm = [];

        view('users.php', array_merge(
            $dashboardData,
            compact('pageTitle', 'currentUser', 'dashboardRole', 'dashboardLabel', 'homePath', 'successMessage', 'errorMessage', 'oldForm')
        ));
    }

    private function buildDashboardData(array $currentUser, $adminView = false)
    {
        $rooms = ['2010', '2011', '2012', '3010', '3011'];
        $products = [];
        $highlights = [
            ['label' => 'Open Orders', 'value' => '0'],
            ['label' => 'Today Revenue', 'value' => 'EGP 0.00'],
            ['label' => 'Top Drink', 'value' => 'No orders yet'],
        ];

        try {
            $connection = DB::conn();

            if (!empty($currentUser['room_no']) && !in_array($currentUser['room_no'], $rooms, true)) {
                array_unshift($rooms, $currentUser['room_no']);
            }

            $iconMap = [
                'tea' => '&#x1F375;',
                'coffee' => '&#x2615;',
                'nescafe' => '&#x2615;',
                'cola' => '&#x1F964;',
                'juice' => '&#x1F9C3;',
                'water' => '&#x1F4A7;',
                'chocolate' => '&#x1F36B;',
                'hot drinks' => '&#x2615;',
                'cold drinks' => '&#x1F964;',
            ];

            foreach (Product::availableWithCategories(!$adminView) as $product) {
                $lookup = strtolower($product['name']);
                $categoryLookup = strtolower($product['category_name']);
                $icon = $iconMap[$lookup] ?? $iconMap[$categoryLookup] ?? '&#x2615;';

                $products[] = [
                    'id' => (int) $product['id'],
                    'name' => $product['name'],
                    'price' => (float) $product['price'],
                    'icon' => $icon,
                    'tag' => $product['category_name'],
                    'available' => (bool) $product['is_available'],
                ];
            }

            if ($adminView) {
                $statsStmt = $connection->query("
                    SELECT
                        SUM(CASE WHEN status IN ('Processing', 'Out for delivery') THEN 1 ELSE 0 END) AS open_orders,
                        COALESCE(SUM(CASE WHEN DATE(order_date) = CURDATE() THEN total_amount ELSE 0 END), 0) AS today_revenue
                    FROM orders
                ");
                $topProductStmt = $connection->query("
                    SELECT p.name
                    FROM order_items oi
                    INNER JOIN products p ON p.id = oi.product_id
                    GROUP BY p.id, p.name
                    ORDER BY SUM(oi.quantity) DESC, p.name ASC
                    LIMIT 1
                ");
            } else {
                $statsStmt = $connection->prepare("
                    SELECT
                        SUM(CASE WHEN status IN ('Processing', 'Out for delivery') THEN 1 ELSE 0 END) AS open_orders,
                        COALESCE(SUM(CASE WHEN DATE(order_date) = CURDATE() THEN total_amount ELSE 0 END), 0) AS today_revenue
                    FROM orders
                    WHERE user_id = ?
                ");
                $statsStmt->execute([$currentUser['id']]);

                $topProductStmt = $connection->prepare("
                    SELECT p.name
                    FROM order_items oi
                    INNER JOIN orders o ON o.id = oi.order_id
                    INNER JOIN products p ON p.id = oi.product_id
                    WHERE o.user_id = ?
                    GROUP BY p.id, p.name
                    ORDER BY SUM(oi.quantity) DESC, p.name ASC
                    LIMIT 1
                ");
                $topProductStmt->execute([$currentUser['id']]);
            }

            $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC) ?: ['open_orders' => 0, 'today_revenue' => 0];
            $topProduct = $topProductStmt->fetchColumn() ?: 'No orders yet';

            $highlights = [
                ['label' => 'Open Orders', 'value' => (string) ($stats['open_orders'] ?? 0)],
                ['label' => 'Today Revenue', 'value' => 'EGP ' . number_format((float) ($stats['today_revenue'] ?? 0), 2)],
                ['label' => 'Top Drink', 'value' => $topProduct],
            ];

            if ($adminView) {
                $usersCount = $connection->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn();
                $highlights[2] = ['label' => 'Active Users', 'value' => (string) $usersCount];
            }
        } catch (Throwable $e) {
            $products = [];
        }

        if ($adminView && empty($products)) {
            $products = [
                ['id' => 1, 'name' => 'Tea', 'price' => 5, 'icon' => '&#x1F375;', 'tag' => 'Classic', 'available' => true],
                ['id' => 2, 'name' => 'Coffee', 'price' => 6, 'icon' => '&#x2615;', 'tag' => 'Popular', 'available' => true],
                ['id' => 3, 'name' => 'Nescafe', 'price' => 12, 'icon' => '&#x2615;', 'tag' => 'Classic', 'available' => true],
                ['id' => 4, 'name' => 'Cola', 'price' => 10, 'icon' => '&#x1F964;', 'tag' => 'Cold', 'available' => true],
                ['id' => 5, 'name' => 'Water', 'price' => 3, 'icon' => '&#x1F4A7;', 'tag' => 'Essentials', 'available' => true],
                ['id' => 6, 'name' => 'Hot Choco', 'price' => 15, 'icon' => '&#x1F36B;', 'tag' => 'Sweet', 'available' => true],
            ];
        }

        return compact('rooms', 'products', 'highlights');
    }
}
