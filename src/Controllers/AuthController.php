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
        $successMessage = get_flash('auth_success');

        view('auth/login.php', compact('pageTitle', 'errors', 'old', 'authError', 'successMessage'));
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
        $successMessage = '';

        if (!empty($errors)) {
            view('auth/login.php', compact('pageTitle', 'errors', 'old', 'authError', 'successMessage'));
            return;
        }

        $user = User::attempt($email, $password);
        if (!$user) {
            $authError = 'Invalid email or password.';
            view('auth/login.php', compact('pageTitle', 'errors', 'old', 'authError', 'successMessage'));
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
        $errors = [];
        $old = ['email' => ''];
        $successMessage = '';

        view('auth/forgot_password.php', compact('pageTitle', 'errors', 'old', 'successMessage'));
    }

    public function forgotPassword()
    {
        if (is_authenticated()) {
            redirect_by_role();
        }

        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');
        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (!User::findByEmail($email)) {
            $errors['email'] = 'No account was found for this email.';
        }

        if ($password === '') {
            $errors['password'] = 'New password is required.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if ($passwordConfirmation === '') {
            $errors['password_confirmation'] = 'Please confirm the new password.';
        } elseif ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        $pageTitle = 'Cafeteria | Forgot Password';
        $old = ['email' => $email];
        $successMessage = '';

        if (!empty($errors)) {
            view('auth/forgot_password.php', compact('pageTitle', 'errors', 'old', 'successMessage'));
            return;
        }

        User::updatePasswordByEmail($email, password_hash($password, PASSWORD_DEFAULT));
        set_flash('auth_success', 'Password updated successfully. You can log in now.');
        redirect('/login');
    }
}
