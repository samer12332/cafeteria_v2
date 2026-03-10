<?php

namespace Src\Controllers;

use Src\Models\Order;
use Src\Models\Product;
use Src\Models\User;
use Throwable;

class OrderController
{
    public function store()
    {
        $currentUser = require_auth('user');
        $room = trim($_POST['room'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $decoded = $this->decodeCartPayload($_POST['cart_payload'] ?? '[]');
        set_flash('order_old', ['room' => $room, 'notes' => $notes]);

        if ($room === '') {
            set_flash('order_error', 'Please select a room.');
            redirect('/user');
        }

        $orderItems = $this->buildOrderItems($decoded, '/user', 'order_error');
        if ($orderItems === null) {
            redirect('/user');
        }

        try {
            Order::createWithItems($currentUser['id'], $room, $notes, $orderItems);
            set_flash('order_success', 'Order placed successfully.');
            set_flash('order_old', []);
        } catch (Throwable $e) {
            set_flash('order_error', 'Unable to place the order right now.');
        }

        redirect('/user');
    }

    public function manualOrder()
    {
        $currentUser = require_auth('admin');
        $pageTitle = 'Cafeteria | Manual Order';
        $users = User::allNormalUsers();
        $products = Product::availableWithCategories(true);
        $successMessage = get_flash('manual_order_success');
        $errorMessage = get_flash('manual_order_error');
        $oldForm = get_flash('manual_order_old', [
            'user_id' => '',
            'room' => '',
            'notes' => '',
        ]);

        view('orders/manual_order.php', compact('pageTitle', 'currentUser', 'users', 'products', 'successMessage', 'errorMessage', 'oldForm'));
    }

    public function storeManualOrder()
    {
        require_auth('admin');

        $userId = (int) ($_POST['user_id'] ?? 0);
        $room = trim($_POST['room'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $decoded = $this->decodeCartPayload($_POST['cart_payload'] ?? '[]');
        set_flash('manual_order_old', [
            'user_id' => (string) $userId,
            'room' => $room,
            'notes' => $notes,
        ]);

        $selectedUser = User::findNormalUser($userId);
        if (!$selectedUser) {
            set_flash('manual_order_error', 'Please select a valid user.');
            redirect('/admin/manual-order');
        }

        if ($room === '') {
            set_flash('manual_order_error', 'Please select a room.');
            redirect('/admin/manual-order');
        }

        $orderItems = $this->buildOrderItems($decoded, '/admin/manual-order', 'manual_order_error');
        if ($orderItems === null) {
            redirect('/admin/manual-order');
        }

        try {
            Order::createWithItems($selectedUser['id'], $room, $notes, $orderItems);
            set_flash('manual_order_success', 'Manual order created successfully.');
            set_flash('manual_order_old', []);
        } catch (Throwable $e) {
            set_flash('manual_order_error', 'Unable to create the order right now.');
        }

        redirect('/admin/manual-order');
    }

    public function myOrders()
    {
        $currentUser = require_auth('user');
        $pageTitle = 'Cafeteria | My Orders';
        $dateFrom = trim($_GET['date_from'] ?? '');
        $dateTo = trim($_GET['date_to'] ?? '');
        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];

        $orders = Order::forUser($currentUser['id'], $filters);
        $successMessage = get_flash('order_success');
        $errorMessage = get_flash('order_error');

        view('orders/my_orders.php', compact('pageTitle', 'currentUser', 'orders', 'dateFrom', 'dateTo', 'successMessage', 'errorMessage'));
    }

    public function cancel()
    {
        $currentUser = require_auth('user');
        $orderId = (int) ($_POST['order_id'] ?? 0);

        if ($orderId <= 0) {
            set_flash('order_error', 'Invalid order selected.');
            redirect('/user/orders');
        }

        if (Order::cancelForUser($orderId, $currentUser['id'])) {
            set_flash('order_success', 'Order cancelled successfully.');
        } else {
            set_flash('order_error', 'Only processing orders can be cancelled.');
        }

        redirect('/user/orders');
    }

    public function adminOrders()
    {
        $currentUser = require_auth('admin');
        $pageTitle = 'Cafeteria | Orders';
        $search = trim($_GET['search'] ?? '');
        $orders = Order::allWithUsers($search);
        $successMessage = get_flash('admin_order_success');
        $errorMessage = get_flash('admin_order_error');

        view('orders/index.php', compact('pageTitle', 'currentUser', 'orders', 'successMessage', 'errorMessage', 'search'));
    }

    public function markOutForDelivery()
    {
        require_auth('admin');
        $orderId = (int) ($_POST['order_id'] ?? 0);

        if ($orderId <= 0) {
            set_flash('admin_order_error', 'Invalid order selected.');
            redirect('/admin/orders');
        }

        if (Order::markOutForDelivery($orderId)) {
            set_flash('admin_order_success', 'Order marked as out for delivery.');
        } else {
            set_flash('admin_order_error', 'Order could not be updated.');
        }

        redirect('/admin/orders');
    }

    public function markDone()
    {
        require_auth('admin');
        $orderId = (int) ($_POST['order_id'] ?? 0);

        if ($orderId <= 0) {
            set_flash('admin_order_error', 'Invalid order selected.');
            redirect('/admin/orders');
        }

        if (Order::markDone($orderId)) {
            set_flash('admin_order_success', 'Order marked as done.');
        } else {
            set_flash('admin_order_error', 'Order could not be updated.');
        }

        redirect('/admin/orders');
    }

    public function checks()
    {
        $currentUser = require_auth('admin');
        $pageTitle = 'Cafeteria | Checks';
        $dateFrom = trim($_GET['date_from'] ?? '');
        $dateTo = trim($_GET['date_to'] ?? '');
        $selectedUserId = trim($_GET['user_id'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'user_id' => $selectedUserId,
        ];

        $users = User::allNormalUsers();
        $allReports = Order::reportByUsers($filters);
        $pagination = pagination_meta(count($allReports), $page, 10);
        $reports = array_slice($allReports, ($pagination['page'] - 1) * $pagination['per_page'], $pagination['per_page']);

        view('orders/checks.php', compact('pageTitle', 'currentUser', 'users', 'reports', 'dateFrom', 'dateTo', 'selectedUserId', 'pagination'));
    }

    private function decodeCartPayload($payload)
    {
        $decoded = json_decode($payload, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function buildOrderItems(array $decoded, $redirectPath, $flashKey)
    {
        if (empty($decoded)) {
            set_flash($flashKey, 'Please add at least one product to the cart.');
            return null;
        }

        $cartItems = [];
        foreach ($decoded as $item) {
            $productId = (int) ($item['id'] ?? 0);
            $quantity = (int) ($item['qty'] ?? 0);

            if ($productId > 0 && $quantity > 0) {
                $cartItems[$productId] = $quantity;
            }
        }

        if (empty($cartItems)) {
            set_flash($flashKey, 'Please add valid products to the cart.');
            return null;
        }

        $products = Product::findAvailableByIds(array_keys($cartItems));
        $productsById = [];
        foreach ($products as $product) {
            if (!(bool) $product['is_available']) {
                continue;
            }

            $productsById[(int) $product['id']] = $product;
        }

        $orderItems = [];
        foreach ($cartItems as $productId => $quantity) {
            if (!isset($productsById[$productId])) {
                set_flash($flashKey, 'One or more selected products are unavailable.');
                return null;
            }

            $orderItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => (float) $productsById[$productId]['price'],
            ];
        }

        return $orderItems;
    }
}
