<?php require_once 'layout/header.php'; ?>
<header class="border-b border-orange-100 bg-white/70 backdrop-blur">
    <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-500 text-xl text-white shadow-glow">&#x2615;</div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Cafeteria</p>
                <p class="text-xs text-slate-500"><?= $dashboardRole === 'admin' ? 'Admin dashboard' : 'Make order' ?></p>
            </div>
        </div>

        <nav class="hidden items-center gap-5 text-sm font-medium text-slate-600 md:flex">
            <a href="<?= url($homePath) ?>" class="text-brand-700">Home</a>
            <?php if ($dashboardRole === 'user'): ?>
                <a href="<?= url('/user/orders') ?>" class="transition hover:text-brand-600">My Orders</a>
            <?php else: ?>
                <a href="<?= url('/admin/orders') ?>" class="transition hover:text-brand-600">Orders</a>
                <a href="<?= url('/admin/products') ?>" class="transition hover:text-brand-600">Products</a>
                <a href="<?= url('/admin/users') ?>" class="transition hover:text-brand-600">Users</a>
                <a href="<?= url('/admin/manual-order') ?>" class="transition hover:text-brand-600">Manual Order</a>
                <a href="<?= url('/admin/checks') ?>" class="transition hover:text-brand-600">Checks</a>
            <?php endif; ?>
        </nav>

        <div class="flex items-center gap-3 rounded-full border border-orange-100 bg-white px-3 py-2 text-sm shadow-sm">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-100">&#x1F464;</span>
            <div>
                <p class="font-medium text-slate-800"><?= htmlspecialchars($currentUser['name']) ?></p>
                <p class="text-xs text-slate-500"><?= htmlspecialchars($dashboardLabel) ?></p>
            </div>
            <a href="<?= url('/logout') ?>" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">Logout</a>
        </div>
    </div>
</header>

<main class="mx-auto w-full max-w-6xl px-6 py-8">
    <?php if ($dashboardRole === 'admin'): ?>
        <section class="space-y-6">
            <div class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Overview</p>
                <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-semibold">Monitor cafeteria operations in one place</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                            Use the admin dashboard to manage orders, maintain the menu, handle users, create manual orders, and review financial checks.
                        </p>
                    </div>
                    <a href="<?= url('/admin/manual-order') ?>" class="rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">Create Manual Order</a>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <?php foreach ($highlights as $highlight): ?>
                        <div class="rounded-3xl border border-orange-100 bg-gradient-to-br from-white to-orange-50 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400"><?= htmlspecialchars($highlight['label']) ?></p>
                            <p class="mt-2 text-lg font-semibold text-slate-800"><?= htmlspecialchars($highlight['value']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <a href="<?= url('/admin/orders') ?>" class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-glow">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Orders</p>
                    <h2 class="mt-3 text-xl font-semibold text-slate-800">Track delivery workflow</h2>
                    <p class="mt-2 text-sm text-slate-500">Review all orders, inspect product breakdowns, and move them from processing to delivered.</p>
                </a>
                <a href="<?= url('/admin/products') ?>" class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-glow">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Products</p>
                    <h2 class="mt-3 text-xl font-semibold text-slate-800">Maintain the menu</h2>
                    <p class="mt-2 text-sm text-slate-500">Add products, update pricing, upload images, and manage availability by category.</p>
                </a>
                <a href="<?= url('/admin/users') ?>" class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-glow">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Users</p>
                    <h2 class="mt-3 text-xl font-semibold text-slate-800">Manage staff accounts</h2>
                    <p class="mt-2 text-sm text-slate-500">Create, edit, and remove user accounts with rooms, extensions, and profile images.</p>
                </a>
                <a href="<?= url('/admin/manual-order') ?>" class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-glow">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Manual Order</p>
                    <h2 class="mt-3 text-xl font-semibold text-slate-800">Order on behalf of users</h2>
                    <p class="mt-2 text-sm text-slate-500">Build a cart for any user, set notes and room, then confirm the order directly from admin.</p>
                </a>
                <a href="<?= url('/admin/checks') ?>" class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-glow md:col-span-2 xl:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Checks</p>
                    <h2 class="mt-3 text-xl font-semibold text-slate-800">Review financial performance</h2>
                    <p class="mt-2 text-sm text-slate-500">Filter by date and user, compare totals, and inspect the products included in each order.</p>
                </a>
            </section>

            <section class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Catalog Snapshot</p>
                        <h2 class="mt-2 text-2xl font-semibold">Current product overview</h2>
                    </div>
                    <a href="<?= url('/admin/products') ?>" class="rounded-2xl border border-orange-200 px-4 py-3 text-sm font-semibold text-brand-700 transition hover:bg-orange-50">Open Products</a>
                </div>

                <?php if (empty($products)): ?>
                    <div class="mt-6 rounded-3xl border border-dashed border-orange-200 bg-orange-50 px-6 py-10 text-center text-sm text-slate-500">
                        No products are available yet. Add products in the database first, then they will appear here.
                    </div>
                <?php else: ?>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <?php foreach (array_slice($products, 0, 6) as $product): ?>
                            <article class="rounded-[1.75rem] border border-orange-100 bg-white p-5 shadow-sm <?= !empty($product['available']) ? '' : 'opacity-60' ?>">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="text-4xl"><?= html_entity_decode($product['icon'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-brand-700">
                                        <?= htmlspecialchars(!empty($product['available']) ? $product['tag'] : 'Unavailable') ?>
                                    </span>
                                </div>
                                <p class="mt-4 text-lg font-semibold"><?= htmlspecialchars($product['name']) ?></p>
                                <div class="mt-2 flex items-center justify-between text-sm">
                                    <span class="text-slate-500"><?= htmlspecialchars($product['tag']) ?></span>
                                    <span class="font-semibold text-slate-800"><?= htmlspecialchars(number_format((float) $product['price'], 2)) ?> LE</span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </section>
    <?php else: ?>
        <section class="grid gap-6 lg:grid-cols-[1.05fr_1.6fr]">
            <div class="space-y-6">
                <div class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Make Order</p>
                    <h1 class="mt-2 text-3xl font-semibold">Build today&apos;s order in one place</h1>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Add drinks to the cart, choose a room, and confirm the order. You can review or cancel processing orders from My Orders.
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
                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-brand-700">Room Selection</span>
                    </div>

                    <form class="mt-6 space-y-4" method="post" action="<?= url('/user/orders') ?>" data-validate-form data-require-cart>
                        <input type="hidden" name="cart_payload" value="[]" data-cart-payload>

                        <div class="space-y-4" data-cart-items></div>

                        <div>
                            <label for="notes" class="text-sm font-semibold text-slate-700">Notes</label>
                            <textarea id="notes" name="notes" class="mt-2 w-full rounded-2xl border border-orange-100 bg-white/70 p-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100" rows="3" placeholder="1 Tea extra sugar"><?= htmlspecialchars($oldForm['notes'] ?? '') ?></textarea>
                        </div>

                        <div data-field>
                            <label for="room" class="text-sm font-semibold text-slate-700">Room</label>
                            <select id="room" name="room" required class="mt-2 w-full rounded-2xl border border-orange-100 bg-white/70 p-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                                <option value="">Select a room</option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?= htmlspecialchars($room) ?>" <?= (($oldForm['room'] ?? $currentUser['room_no'] ?? '') === $room) ? 'selected' : '' ?>><?= htmlspecialchars($room) ?></option>
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
                        <h2 class="text-2xl font-semibold">Pick your favorites</h2>
                    </div>
                    <label class="flex items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 text-sm shadow-sm">
                        <span>&#x1F50D;</span>
                        <input type="search" class="w-44 bg-transparent text-sm focus:outline-none" placeholder="Search products..." data-search>
                    </label>
                </div>

                <?php if (empty($products)): ?>
                    <div class="mt-6 rounded-3xl border border-dashed border-orange-200 bg-orange-50 px-6 py-10 text-center text-sm text-slate-500">
                        No products are available yet. Add products in the database first, then they will appear here for ordering.
                    </div>
                <?php else: ?>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($products as $product): ?>
                            <article
                                class="rounded-[1.75rem] border border-orange-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-glow <?= !empty($product['available']) ? '' : 'opacity-60' ?>"
                                data-product
                                data-id="<?= $product['id'] ?>"
                                data-name="<?= htmlspecialchars($product['name']) ?>"
                                data-price="<?= $product['price'] ?>"
                                data-available="<?= !empty($product['available']) ? '1' : '0' ?>"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="text-4xl"><?= html_entity_decode($product['icon'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-brand-700">
                                        <?= htmlspecialchars(!empty($product['available']) ? $product['tag'] : 'Unavailable') ?>
                                    </span>
                                </div>
                                <p class="mt-4 text-lg font-semibold"><?= htmlspecialchars($product['name']) ?></p>
                                <div class="mt-2 flex items-center justify-between text-sm">
                                    <span class="text-slate-500"><?= htmlspecialchars($product['tag']) ?> drink</span>
                                    <span class="font-semibold text-slate-800"><?= htmlspecialchars(number_format((float) $product['price'], 2)) ?> LE</span>
                                </div>
                                <button class="mt-5 w-full rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-semibold text-brand-700 transition hover:bg-brand-600 hover:text-white disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400" type="button" <?= !empty($product['available']) ? '' : 'disabled' ?>>
                                    <?= !empty($product['available']) ? 'Add to order' : 'Not available' ?>
                                </button>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </section>
    <?php endif; ?>
</main>

<script src="<?= url('/public/js/main.js') ?>"></script>
</body>
</html>
