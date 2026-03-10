<?php
require_once __DIR__ . '/header.php';

$appTitle = $appTitle ?? 'Cafeteria';
$appSubtitle = $appSubtitle ?? '';
$activeNav = $activeNav ?? '';
$navItems = $navItems ?? [];
$userRoleLabel = $userRoleLabel ?? '';
$headerWidthClass = $headerWidthClass ?? 'max-w-6xl';
$mainClass = $mainClass ?? 'mx-auto w-full max-w-6xl px-6 py-8';
?>
<div data-spa-root data-layout="app">
    <header class="border-b border-orange-100 bg-white/70 backdrop-blur">
        <div class="mx-auto flex w-full <?= htmlspecialchars($headerWidthClass) ?> items-center justify-between px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-500 text-xl text-white shadow-glow">&#x2615;</div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Cafeteria</p>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($appSubtitle) ?></p>
                </div>
            </div>

            <nav class="hidden items-center gap-5 text-sm font-medium text-slate-600 md:flex">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= url($item['path']) ?>" class="<?= $activeNav === $item['key'] ? 'text-brand-700' : 'transition hover:text-brand-600' ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="flex items-center gap-3 rounded-full border border-orange-100 bg-white px-3 py-2 text-sm shadow-sm">
                <?php if (!empty($currentUser['profile_picture'])): ?>
                    <img src="<?= auth_profile_image() ?>" alt="<?= htmlspecialchars($currentUser['name']) ?>" class="h-9 w-9 rounded-full object-cover">
                <?php else: ?>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-100">&#x1F464;</span>
                <?php endif; ?>
                <div>
                    <p class="font-medium text-slate-800"><?= htmlspecialchars($currentUser['name']) ?></p>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($userRoleLabel) ?></p>
                </div>
                <a href="<?= url('/logout') ?>" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">Logout</a>
            </div>
        </div>
    </header>

    <main class="<?= htmlspecialchars($mainClass) ?>">
