<?php

use Src\Classes\Router;
use Src\Controllers\AdminUserController;
use Src\Controllers\AuthController;
use Src\Controllers\OrderController;
use Src\Controllers\ProductController;
use Src\Controllers\UserController;

Router::get("/", [UserController::class, "index"]);
Router::get("/login", [AuthController::class, "showLogin"]);
Router::post("/login", [AuthController::class, "login"]);
Router::get("/forgot-password", [AuthController::class, "showForgotPassword"]);
Router::post("/forgot-password", [AuthController::class, "forgotPassword"]);
Router::get("/logout", [AuthController::class, "logout"]);
Router::get("/user", [UserController::class, "dashboard"]);
Router::post("/user/orders", [OrderController::class, "store"]);
Router::get("/user/orders", [OrderController::class, "myOrders"]);
Router::post("/user/orders/cancel", [OrderController::class, "cancel"]);
Router::get("/admin", [UserController::class, "adminDashboard"]);
Router::get("/admin/orders", [OrderController::class, "adminOrders"]);
Router::post("/admin/orders/out-for-delivery", [OrderController::class, "markOutForDelivery"]);
Router::post("/admin/orders/done", [OrderController::class, "markDone"]);
Router::get("/admin/checks", [OrderController::class, "checks"]);
Router::get("/admin/manual-order", [OrderController::class, "manualOrder"]);
Router::post("/admin/manual-order", [OrderController::class, "storeManualOrder"]);
Router::get("/admin/products", [ProductController::class, "index"]);
Router::get("/admin/products/create", [ProductController::class, "create"]);
Router::post("/admin/products/store", [ProductController::class, "store"]);
Router::get("/admin/products/edit", [ProductController::class, "edit"]);
Router::post("/admin/products/update", [ProductController::class, "update"]);
Router::post("/admin/products/delete", [ProductController::class, "delete"]);
Router::post("/admin/products/toggle", [ProductController::class, "toggleAvailability"]);
Router::get("/admin/users", [AdminUserController::class, "index"]);
Router::get("/admin/users/create", [AdminUserController::class, "create"]);
Router::post("/admin/users/store", [AdminUserController::class, "store"]);
Router::get("/admin/users/edit", [AdminUserController::class, "edit"]);
Router::post("/admin/users/update", [AdminUserController::class, "update"]);
Router::post("/admin/users/delete", [AdminUserController::class, "delete"]);
