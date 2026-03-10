<?php
require_once __DIR__ . '/header.php';

$authMainClass = $authMainClass ?? 'flex min-h-screen items-center justify-center px-6 py-12';
?>
<div data-spa-root data-layout="auth">
    <main class="<?= htmlspecialchars($authMainClass) ?>">
