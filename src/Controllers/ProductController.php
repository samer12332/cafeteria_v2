<?php

namespace Src\Controllers;

use Src\Models\Category;
use Src\Models\Product;

class ProductController
{
    public function index()
    {
        $currentUser = require_auth('admin');
        $pageTitle = 'Cafeteria | Products';
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = Product::paginateWithCategories($search, $page, 10);
        $products = $result['items'];
        $pagination = pagination_meta($result['total'], $page, 10);
        $successMessage = get_flash('product_success');
        $errorMessage = get_flash('product_error');

        view('products/index.php', compact('pageTitle', 'currentUser', 'products', 'successMessage', 'errorMessage', 'search', 'pagination'));
    }

    public function create()
    {
        $currentUser = require_auth('admin');
        $pageTitle = 'Cafeteria | Add Product';
        $product = null;
        $categories = Category::ordered();
        $errors = get_flash('product_form_errors', []);
        $old = get_flash('product_form_old', [
            'name' => '',
            'price' => '',
            'category_id' => '',
            'new_category' => '',
        ]);
        $formAction = url('/admin/products/store');
        $formHeading = 'Add Product';
        $submitLabel = 'Save Product';

        view('products/form.php', compact('pageTitle', 'currentUser', 'product', 'categories', 'errors', 'old', 'formAction', 'formHeading', 'submitLabel'));
    }

    public function store()
    {
        require_auth('admin');

        [$data, $old, $errors] = $this->validatePayload(null);
        if (!empty($errors)) {
            set_flash('product_form_errors', $errors);
            set_flash('product_form_old', $old);
            redirect('/admin/products/create');
        }

        $imagePath = $this->handleImageUpload();
        if ($imagePath === false || $imagePath === null) {
            set_flash('product_form_errors', ['image' => 'Please upload a valid image file.']);
            set_flash('product_form_old', $old);
            redirect('/admin/products/create');
        }

        $data['product_picture'] = $imagePath;

        Product::create($data);
        set_flash('product_success', 'Product created successfully.');
        redirect('/admin/products');
    }

    public function edit()
    {
        $currentUser = require_auth('admin');
        $productId = (int) ($_GET['id'] ?? 0);
        $product = Product::findWithCategory($productId);
        if (!$product) {
            set_flash('product_error', 'Product not found.');
            redirect('/admin/products');
        }

        $pageTitle = 'Cafeteria | Edit Product';
        $categories = Category::ordered();
        $errors = get_flash('product_form_errors', []);
        $old = get_flash('product_form_old', [
            'name' => $product['name'],
            'price' => $product['price'],
            'category_id' => (string) ($product['category_id'] ?? ''),
            'new_category' => '',
            'is_available' => (int) $product['is_available'],
        ]);
        $formAction = url('/admin/products/update');
        $formHeading = 'Edit Product';
        $submitLabel = 'Update Product';

        view('products/form.php', compact('pageTitle', 'currentUser', 'product', 'categories', 'errors', 'old', 'formAction', 'formHeading', 'submitLabel'));
    }

    public function update()
    {
        require_auth('admin');
        $productId = (int) ($_POST['id'] ?? 0);
        $product = Product::findWithCategory($productId);
        if (!$product) {
            set_flash('product_error', 'Product not found.');
            redirect('/admin/products');
        }

        [$data, $old, $errors] = $this->validatePayload($product);
        if (!empty($errors)) {
            set_flash('product_form_errors', $errors);
            set_flash('product_form_old', $old + ['is_available' => (int) $product['is_available']]);
            redirect('/admin/products/edit?id=' . $productId);
        }

        $imagePath = $this->handleImageUpload();
        if ($imagePath === false) {
            set_flash('product_form_errors', ['image' => 'Please upload a valid image file.']);
            set_flash('product_form_old', $old + ['is_available' => (int) $product['is_available']]);
            redirect('/admin/products/edit?id=' . $productId);
        }

        if ($imagePath !== null) {
            $data['product_picture'] = $imagePath;
            $this->deleteImageFile($product['product_picture'] ?? null);
        }

        Product::update($productId, $data);
        set_flash('product_success', 'Product updated successfully.');
        redirect('/admin/products');
    }

    public function delete()
    {
        require_auth('admin');
        $productId = (int) ($_POST['id'] ?? 0);
        $product = Product::findWithCategory($productId);
        if (!$product) {
            set_flash('product_error', 'Product not found.');
            redirect('/admin/products');
        }

        Product::delete($productId);
        $this->deleteImageFile($product['product_picture'] ?? null);
        set_flash('product_success', 'Product deleted successfully.');
        redirect('/admin/products');
    }

    public function toggleAvailability()
    {
        require_auth('admin');
        $productId = (int) ($_POST['id'] ?? 0);
        $product = Product::find($productId);
        if (!$product) {
            set_flash('product_error', 'Product not found.');
            redirect('/admin/products');
        }

        Product::update($productId, ['is_available' => $product['is_available'] ? 0 : 1]);
        set_flash('product_success', 'Product availability updated.');
        redirect('/admin/products');
    }

    private function validatePayload($existingProduct = null)
    {
        $name = trim($_POST['name'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $categoryId = trim($_POST['category_id'] ?? '');
        $newCategory = trim($_POST['new_category'] ?? '');
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Product name is required.';
        }

        if ($price === '') {
            $errors['price'] = 'Price is required.';
        } elseif (!is_numeric($price) || (float) $price <= 0) {
            $errors['price'] = 'Price must be a positive number.';
        }

        if ($categoryId === '' && $newCategory === '') {
            $errors['category_id'] = 'Select an existing category or add a new one.';
        }

        $resolvedCategoryId = $categoryId !== '' ? (int) $categoryId : null;
        if ($newCategory !== '') {
            $existingCategory = Category::findByName($newCategory);
            if ($existingCategory) {
                $resolvedCategoryId = (int) $existingCategory['id'];
            } else {
                $createdCategory = Category::create(['name' => $newCategory]);
                $resolvedCategoryId = (int) $createdCategory['id'];
            }
        }

        $data = [
            'name' => $name,
            'price' => number_format((float) $price, 2, '.', ''),
            'category_id' => $resolvedCategoryId,
            'is_available' => $existingProduct ? (int) $existingProduct['is_available'] : 1,
        ];

        $old = [
            'name' => $name,
            'price' => $price,
            'category_id' => (string) ($resolvedCategoryId ?? $categoryId),
            'new_category' => $newCategory,
        ];

        return [$data, $old, $errors];
    }

    private function handleImageUpload()
    {
        if (empty($_FILES['image']) || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($_FILES['image']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return false;
        }

        $tmpName = $_FILES['image']['tmp_name'];
        $originalName = $_FILES['image']['name'] ?? 'product';
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowed, true)) {
            return false;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/products';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid('product_', true) . '.' . $extension;
        $destination = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            return false;
        }

        return 'uploads/products/' . $fileName;
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
