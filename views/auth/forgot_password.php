<?php require_once __DIR__ . '/../layout/header.php'; ?>
<main class="flex min-h-screen items-center justify-center px-6 py-12">
    <div class="w-full max-w-md rounded-3xl bg-white/90 p-8 shadow-2xl shadow-orange-100">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-600 text-2xl text-white">&#x2615;</div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Cafeteria</p>
                <h1 class="text-2xl font-semibold">Forgot password</h1>
            </div>
        </div>

        <p class="mt-4 text-sm leading-6 text-slate-500">
            Password reset has not been implemented yet. Use an administrator account to update the user password directly in the system for now.
        </p>

        <a href="<?= url('/login') ?>" class="mt-6 inline-flex rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-200 transition hover:bg-brand-700">
            Back to Login
        </a>
    </div>
</main>
</body>
</html>
