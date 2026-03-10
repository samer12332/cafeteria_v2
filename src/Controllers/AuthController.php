<?php

namespace Src\Controllers;

use Src\Models\User;

class AuthController
{
    public function showLogin()
    {
        if (is_authenticated()) {
            redirect_by_role();
        }

        $pageTitle = 'Cafeteria | Login';
        $errors = [];
        $old = ['email' => ''];
        $authError = '';

        view('auth/login.php', compact('pageTitle', 'errors', 'old', 'authError'));
    }

    public function login()
    {
        if (is_authenticated()) {
            redirect_by_role();
        }

        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        $pageTitle = 'Cafeteria | Login';
        $old = ['email' => $email];
        $authError = '';

        if (!empty($errors)) {
            view('auth/login.php', compact('pageTitle', 'errors', 'old', 'authError'));
            return;
        }

        $user = User::attempt($email, $password);
        if (!$user) {
            $authError = 'Invalid email or password.';
            view('auth/login.php', compact('pageTitle', 'errors', 'old', 'authError'));
            return;
        }

        login_user($user);
        redirect_by_role();
    }

    public function logout()
    {
        logout_user();
        redirect('/login');
    }

    public function showForgotPassword()
    {
        if (is_authenticated()) {
            redirect_by_role();
        }

        $pageTitle = 'Cafeteria | Forgot Password';
        view('auth/forgot_password.php', compact('pageTitle'));
    }
}
