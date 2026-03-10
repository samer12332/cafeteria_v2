<?php require_once __DIR__ . '/../layout/auth_start.php'; ?>
    <div class="w-full max-w-md rounded-3xl bg-white/90 p-8 shadow-2xl shadow-orange-100">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-600 text-2xl text-white">&#x2615;</div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Cafeteria</p>
                <h1 class="text-2xl font-semibold">Welcome back</h1>
            </div>
        </div>

        <p class="mt-2 text-sm text-slate-500">Sign in to access your cafeteria dashboard.</p>

        <form class="mt-6 space-y-4" method="post" action="<?= url('/login') ?>" data-validate-form>
            <?php if (!empty($successMessage)): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($authError)): ?>
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                    <?= htmlspecialchars($authError) ?>
                </div>
            <?php endif; ?>

            <div data-field>
                <label for="email" class="text-sm font-medium text-slate-700">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    data-validate="email"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    class="mt-2 w-full rounded-2xl border <?= !empty($errors['email']) ? 'border-red-500 ring-red-200' : 'border-orange-100' ?> bg-white/70 px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100"
                    placeholder="you@company.com"
                />
                <p class="mt-1 <?= !empty($errors['email']) ? '' : 'hidden' ?> text-xs text-red-600" data-error><?= htmlspecialchars($errors['email'] ?? '') ?></p>
            </div>

            <div data-field>
                <label for="password" class="text-sm font-medium text-slate-700">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    data-validate="min"
                    data-min="6"
                    class="mt-2 w-full rounded-2xl border <?= !empty($errors['password']) ? 'border-red-500 ring-red-200' : 'border-orange-100' ?> bg-white/70 px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100"
                    placeholder="Enter your password"
                />
                <p class="mt-1 <?= !empty($errors['password']) ? '' : 'hidden' ?> text-xs text-red-600" data-error><?= htmlspecialchars($errors['password'] ?? '') ?></p>
            </div>

            <button class="w-full rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-200 transition hover:bg-brand-700" type="submit">
                Login
            </button>
        </form>

        <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
            <a class="font-medium text-brand-600 hover:text-brand-700" href="<?= url('/forgot-password') ?>">Forgot Password?</a>
        </div>
    </div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
