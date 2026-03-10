<?php

use Src\Exceptions\ViewNotFoundException;

function view($path, $data = []){
    extract($data);
    if(file_exists(__DIR__ . "/views/$path")){
        require_once __DIR__ . "/views/$path";
    }else{
        throw new ViewNotFoundException();
    }
}

function url($path){
    return dirname($_SERVER['SCRIPT_NAME']) . $path;
}

function redirect($path)
{
    header('Location: ' . url($path));
    exit;
}

function auth_user()
{
    return $_SESSION['auth'] ?? null;
}

function is_authenticated()
{
    return auth_user() !== null;
}

function is_admin()
{
    $user = auth_user();
    return !empty($user['is_admin']);
}

function login_user(array $user)
{
    $_SESSION['auth'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'room_no' => $user['room_no'] ?? null,
        'ext' => $user['ext'] ?? null,
        'profile_picture' => $user['profile_picture'] ?? null,
        'is_admin' => (bool) $user['is_admin'],
    ];
}

function logout_user()
{
    unset($_SESSION['auth']);
}

function redirect_by_role()
{
    redirect(is_admin() ? '/admin' : '/user');
}

function require_auth($role = null)
{
    if (!is_authenticated()) {
        redirect('/login');
    }

    if ($role === 'admin' && !is_admin()) {
        redirect('/user');
    }

    if ($role === 'user' && is_admin()) {
        redirect('/admin');
    }

    return auth_user();
}

function set_flash($key, $value)
{
    $_SESSION['_flash'][$key] = $value;
}

function get_flash($key, $default = null)
{
    if (!isset($_SESSION['_flash'][$key])) {
        return $default;
    }

    $value = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);

    return $value;
}

function pagination_meta($total, $page, $perPage)
{
    $page = max(1, (int) $page);
    $perPage = max(1, (int) $perPage);
    $totalPages = max(1, (int) ceil($total / $perPage));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    return [
        'total' => (int) $total,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => $totalPages,
        'has_prev' => $page > 1,
        'has_next' => $page < $totalPages,
        'prev_page' => max(1, $page - 1),
        'next_page' => min($totalPages, $page + 1),
    ];
}

function query_url($path, array $params = [])
{
    $filtered = [];
    foreach ($params as $key => $value) {
        if ($value === '' || $value === null) {
            continue;
        }

        $filtered[$key] = $value;
    }

    if (empty($filtered)) {
        return url($path);
    }

    return url($path) . '?' . http_build_query($filtered);
}

function auth_profile_image()
{
    $user = auth_user();

    if (empty($user['profile_picture'])) {
        return null;
    }

    return url('/public/' . ltrim($user['profile_picture'], '/'));
}

function user_nav_items()
{
    return [
        ['key' => 'home', 'label' => 'Home', 'path' => '/user'],
        ['key' => 'my_orders', 'label' => 'My Orders', 'path' => '/user/orders'],
    ];
}

function admin_nav_items()
{
    return [
        ['key' => 'home', 'label' => 'Home', 'path' => '/admin'],
        ['key' => 'orders', 'label' => 'Orders', 'path' => '/admin/orders'],
        ['key' => 'products', 'label' => 'Products', 'path' => '/admin/products'],
        ['key' => 'users', 'label' => 'Users', 'path' => '/admin/users'],
        ['key' => 'manual_order', 'label' => 'Manual Order', 'path' => '/admin/manual-order'],
        ['key' => 'checks', 'label' => 'Checks', 'path' => '/admin/checks'],
    ];
}
