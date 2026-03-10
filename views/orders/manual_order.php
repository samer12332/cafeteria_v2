<?php require_once __DIR__ . '/../layout/header.php'; ?>
<header class="border-b border-orange-100 bg-white/70 backdrop-blur">
    <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-500 text-xl text-white shadow-glow">&#x2615;</div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Cafeteria</p>
                <p class="text-xs text-slate-500">Manual order</p>
            </div>
        </div>

        <nav class="hidden items-center gap-5 text-sm font-medium text-slate-600 md:flex">
            <a href="<?= url('/admin') ?>" class="transition hover:text-brand-600">Home</a>
            <a href="<?= url('/admin/orders') ?>" class="transition hover:text-brand-600">Orders</a>
            <a href="<?= url('/admin/products') ?>" class="transition hover:text-brand-600">Products</a>
            <a href="<?= url('/admin/users') ?>" class="transition hover:text-brand-600">Users</a>
            <a href="<?= url('/admin/manual-order') ?>" class="text-brand-700">Manual Order</a>
            <a href="<?= url('/admin/checks') ?>" class="transition hover:text-brand-600">Checks</a>
        </nav>

        <div class="flex items-center gap-3 rounded-full border border-orange-100 bg-white px-3 py-2 text-sm shadow-sm">
            <?php if (!empty($currentUser['profile_picture'])): ?>
                <img src="<?= auth_profile_image() ?>" alt="<?= htmlspecialchars($currentUser['name']) ?>" class="h-9 w-9 rounded-full object-cover">
            <?php else: ?>
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-100">&#x1F464;</span>
            <?php endif; ?>
            <div>
                <p class="font-medium text-slate-800"><?= htmlspecialchars($currentUser['name']) ?></p>
                <p class="text-xs text-slate-500">Administrator</p>
            </div>
            <a href="<?= url('/logout') ?>" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">Logout</a>
        </div>
    </div>
</header>

<main class="mx-auto w-full max-w-6xl px-6 py-8">
    <section class="grid gap-6 lg:grid-cols-[1.05fr_1.6fr]">
        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Manual Order</p>
                <h1 class="mt-2 text-3xl font-semibold">Create an order for a specific user</h1>
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    Select the user, add products to the cart, choose a room, and confirm the order on their behalf.
                </p>
            </div>

            <?php if (!empty($successMessage)): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <section class="rounded-[2rem] bg-white/90 p-6 shadow-glow" data-cart>
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Current Order</h2>
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-brand-700">Admin Create</span>
                </div>

                <form class="mt-6 space-y-4" method="post" action="<?= url('/admin/manual-order') ?>" data-validate-form data-require-cart>
                    <input type="hidden" name="cart_payload" value="[]" data-cart-payload>

                    <div data-field>
                        <label for="user_id" class="text-sm font-semibold text-slate-700">User</label>
                        <select id="user_id" name="user_id" required class="mt-2 w-full rounded-2xl border border-orange-100 bg-white/70 p-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                            <option value="">Select user</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= (int) $user['id'] ?>" <?= (string) ($oldForm['user_id'] ?? '') === (string) $user['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['room_no']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 hidden text-xs text-red-600" data-error></p>
                    </div>

                    <div class="space-y-4" data-cart-items></div>

                    <div>
                        <label for="notes" class="text-sm font-semibold text-slate-700">Notes</label>
                        <textarea id="notes" name="notes" class="mt-2 w-full rounded-2xl border border-orange-100 bg-white/70 p-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100" rows="3" placeholder="1 Tea extra sugar"><?= htmlspecialchars($oldForm['notes'] ?? '') ?></textarea>
                    </div>

                    <div data-field>
                        <label for="room" class="text-sm font-semibold text-slate-700">Room</label>
                        <select id="room" name="room" required class="mt-2 w-full rounded-2xl border border-orange-100 bg-white/70 p-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                            <option value="">Select a room</option>
                            <?php foreach (['2010', '2011', '2012', '3010', '3011'] as $room): ?>
                                <option value="<?= htmlspecialchars($room) ?>" <?= (($oldForm['room'] ?? '') === $room) ? 'selected' : '' ?>><?= htmlspecialchars($room) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 hidden text-xs text-red-600" data-error></p>
                    </div>

                    <div class="hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-2 text-xs text-red-600" data-form-alert>
                        Please fix the highlighted fields.
                    </div>

                    <div class="flex items-center justify-between border-t border-orange-100 pt-4 text-base font-semibold">
                        <span>Total</span>
                        <span class="text-brand-700" data-cart-total>EGP 0</span>
                    </div>

                    <button class="w-full rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-200 transition hover:bg-brand-700" type="submit">
                        Confirm Order
                    </button>
                </form>
            </section>
        </div>

        <section data-menu>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Menu</p>
                    <h2 class="text-2xl font-semibold">Pick products for the selected user</h2>
                </div>
                <label class="flex items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 text-sm shadow-sm">
                    <span>&#x1F50D;</span>
                    <input type="search" class="w-44 bg-transparent text-sm focus:outline-none" placeholder="Search products..." data-search>
                </label>
            </div>

            <?php if (empty($products)): ?>
                <div class="mt-6 rounded-3xl border border-dashed border-orange-200 bg-orange-50 px-6 py-10 text-center text-sm text-slate-500">
                    No available products found. Add or enable products first.
                </div>
            <?php else: ?>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($products as $product): ?>
                        <article
                            class="rounded-[1.75rem] border border-orange-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-glow"
                            data-product
                            data-id="<?= $product['id'] ?>"
                            data-name="<?= htmlspecialchars($product['name']) ?>"
                            data-price="<?= $product['price'] ?>"
                            data-available="1"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="text-4xl">&#x2615;</div>
                                <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-brand-700"><?= htmlspecialchars($product['category_name']) ?></span>
                            </div>
                            <p class="mt-4 text-lg font-semibold"><?= htmlspecialchars($product['name']) ?></p>
                            <div class="mt-2 flex items-center justify-between text-sm">
                                <span class="text-slate-500"><?= htmlspecialchars($product['category_name']) ?></span>
                                <span class="font-semibold text-slate-800"><?= htmlspecialchars(number_format((float) $product['price'], 2)) ?> LE</span>
                            </div>
                            <button class="mt-5 w-full rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-semibold text-brand-700 transition hover:bg-brand-600 hover:text-white" type="button">
                                Add to order
                            </button>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </section>
</main>

<script src="<?= url('/public/js/main.js') ?>"></script>
</body>
</html>
