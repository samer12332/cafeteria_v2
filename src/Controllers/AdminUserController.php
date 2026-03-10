<?php

namespace Src\Controllers;

use Src\Models\User;

class AdminUserController
{
    public function index()
    {
        $currentUser = require_auth('admin');
        $pageTitle = 'Cafeteria | Users';
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = User::paginateNormalUsers($search, $page, 10);
        $users = $result['items'];
        $pagination = pagination_meta($result['total'], $page, 10);
        $successMessage = get_flash('user_success');
        $errorMessage = get_flash('user_error');

        view('admin_users/index.php', compact('pageTitle', 'currentUser', 'users', 'successMessage', 'errorMessage', 'search', 'pagination'));
    }

    public function create()
    {
        $currentUser = require_auth('admin');
        $pageTitle = 'Cafeteria | Add User';
        $user = null;
        $errors = get_flash('user_form_errors', []);
        $old = get_flash('user_form_old', [
            'name' => '',
            'email' => '',
            'room_no' => '',
            'ext' => '',
        ]);
        $formAction = url('/admin/users/store');
        $formHeading = 'Add User';
        $submitLabel = 'Save User';

        view('admin_users/form.php', compact('pageTitle', 'currentUser', 'user', 'errors', 'old', 'formAction', 'formHeading', 'submitLabel'));
    }

    public function store()
    {
        require_auth('admin');

        [$data, $old, $errors] = $this->validatePayload();
        if (!empty($errors)) {
            set_flash('user_form_errors', $errors);
            set_flash('user_form_old', $old);
            redirect('/admin/users/create');
        }

        $imagePath = $this->handleImageUpload();
        if ($imagePath === false) {
            set_flash('user_form_errors', ['profile_picture' => 'Please upload a valid image file.']);
            set_flash('user_form_old', $old);
            redirect('/admin/users/create');
        }

        if ($imagePath !== null) {
            $data['profile_picture'] = $imagePath;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        User::create($data);

        set_flash('user_success', 'User created successfully.');
        redirect('/admin/users');
    }

    public function edit()
    {
        $currentUser = require_auth('admin');
        $userId = (int) ($_GET['id'] ?? 0);
        $user = User::findNormalUser($userId);
        if (!$user) {
            set_flash('user_error', 'User not found.');
            redirect('/admin/users');
        }

        $pageTitle = 'Cafeteria | Edit User';
        $errors = get_flash('user_form_errors', []);
        $old = get_flash('user_form_old', [
            'name' => $user['name'],
            'email' => $user['email'],
            'room_no' => $user['room_no'],
            'ext' => $user['ext'],
        ]);
        $formAction = url('/admin/users/update');
        $formHeading = 'Edit User';
        $submitLabel = 'Update User';

        view('admin_users/form.php', compact('pageTitle', 'currentUser', 'user', 'errors', 'old', 'formAction', 'formHeading', 'submitLabel'));
    }

    public function update()
    {
        require_auth('admin');
        $userId = (int) ($_POST['id'] ?? 0);
        $user = User::findNormalUser($userId);
        if (!$user) {
            set_flash('user_error', 'User not found.');
            redirect('/admin/users');
        }

        [$data, $old, $errors] = $this->validatePayload($user);
        if (!empty($errors)) {
            set_flash('user_form_errors', $errors);
            set_flash('user_form_old', $old);
            redirect('/admin/users/edit?id=' . $userId);
        }

        $imagePath = $this->handleImageUpload();
        if ($imagePath === false) {
            set_flash('user_form_errors', ['profile_picture' => 'Please upload a valid image file.']);
            set_flash('user_form_old', $old);
            redirect('/admin/users/edit?id=' . $userId);
        }

        if ($imagePath !== null) {
            $data['profile_picture'] = $imagePath;
            $this->deleteImageFile($user['profile_picture'] ?? null);
        }

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        User::update($userId, $data);
        set_flash('user_success', 'User updated successfully.');
        redirect('/admin/users');
    }

    public function delete()
    {
        require_auth('admin');
        $userId = (int) ($_POST['id'] ?? 0);
        $user = User::findNormalUser($userId);
        if (!$user) {
            set_flash('user_error', 'User not found.');
            redirect('/admin/users');
        }

        User::delete($userId);
        $this->deleteImageFile($user['profile_picture'] ?? null);
        set_flash('user_success', 'User deleted successfully.');
        redirect('/admin/users');
    }

    private function validatePayload($existingUser = null)
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $roomNo = trim($_POST['room_no'] ?? '');
        $extension = trim($_POST['ext'] ?? '');
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (User::emailExists($email, $existingUser['id'] ?? null)) {
            $errors['email'] = 'This email is already in use.';
        }

        if ($existingUser === null) {
            if ($password === '') {
                $errors['password'] = 'Password is required.';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters.';
            }
        } elseif ($password !== '' && strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if ($roomNo === '') {
            $errors['room_no'] = 'Room number is required.';
        }

        if ($extension === '') {
            $errors['ext'] = 'Extension is required.';
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'room_no' => $roomNo,
            'ext' => $extension,
            'is_admin' => 0,
        ];

        $old = [
            'name' => $name,
            'email' => $email,
            'room_no' => $roomNo,
            'ext' => $extension,
        ];

        return [$data, $old, $errors];
    }

    private function handleImageUpload()
    {
        if (empty($_FILES['profile_picture']) || ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return false;
        }

        $tmpName = $_FILES['profile_picture']['tmp_name'];
        $originalName = $_FILES['profile_picture']['name'] ?? 'user';
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowed, true)) {
            return false;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/users';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid('user_', true) . '.' . $extension;
        $destination = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            return false;
        }

        return 'uploads/users/' . $fileName;
    }

    private function deleteImageFile($relativePath)
    {
        if (!$relativePath) {
            return;
        }

        $filePath = __DIR__ . '/../../public/' . ltrim($relativePath, '/');
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }
}
